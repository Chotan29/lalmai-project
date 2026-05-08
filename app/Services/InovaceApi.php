<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;

class InovaceApi
{
    protected string $baseUrl;
    protected string $token;
    protected Client $client;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('inovace.base_url'), '/');
        $this->token   = (string) config('inovace.api_token', '');

        $this->client  = new Client([
            'base_uri'        => $this->baseUrl . '/',
            'headers'         => [
                'Accept'        => 'application/json',
                // header stays, but GET /logs also expects api_token in the query
                'Authorization' => 'Bearer ' . $this->token,
            ],
            'timeout'         => 30,
            'connect_timeout' => 10,
        ]);
    }

    /* ---------------- Helpers ---------------- */

    private function errorMessage(\Throwable $e): string
    {
        try {
            $resp = method_exists($e, 'getResponse') ? $e->getResponse() : null;
            if ($resp) {
                $raw = (string) $resp->getBody();
                $j   = json_decode($raw, true);
                if (is_array($j)) {
                    if (!empty($j['message'])) return (string) $j['message'];
                    if (!empty($j['error']))   return (string) $j['error'];
                    if (!empty($j['errors'])) {
                        $first = reset($j['errors']);
                        if (is_array($first)) return implode(', ', $first);
                        return (string) $first;
                    }
                }
                if ($raw) return $raw;
            }
        } catch (\Throwable $ignore) {}
        return $e->getMessage();
    }

    /** Always include api_token and use multipart for /people */
    private function buildMultipart(array $data, $imagePath = null): array
    {
        $data = array_merge(['api_token' => $this->token], $data);

        $mp = [];
        foreach ($data as $k => $v) {
            if ($v === null) continue;
            $mp[] = ['name' => $k, 'contents' => (string) $v];
        }
        if ($imagePath && is_file($imagePath)) {
            $mp[] = [
                'name'     => 'image',
                'contents' => fopen($imagePath, 'r'),
                'filename' => basename($imagePath),
            ];
        }
        return $mp;
    }

    private function isOkAllocationResponse($r): bool
    {
        if (!is_array($r)) return false;
        if (isset($r['status']) && in_array(strtolower((string)$r['status']), ['pending_sync','queued','ok','success'], true)) return true;
        if (isset($r['code']) && (int)$r['code'] === 200) return true;
        if (isset($r['message']) && preg_match('/(queued|pending)/i', (string)$r['message'])) return true;
        return false;
    }

    /* ---------------- Devices & Logs ---------------- */

    public function devices()
    {
        try {
            $res = $this->client->get('devices');
            return json_decode((string) $res->getBody(), true);
        } catch (\Throwable $e) {
            return ['success'=>false,'message'=>$this->errorMessage($e)];
        }
    }

    /**
     * Logs endpoint per vendor doc:
     *   GET /logs?api_token=...&start=...&end=...&page=...&per_page=...&criteria=logged_time&order_key=logged_time&order_direction=asc
     */
    public function logs(
        $start,
        $end,
        int $page = 1,
        int $perPage = 500,
        string $criteria = 'logged_time',
        string $orderKey = 'logged_time',
        string $orderDir = 'asc'
    ) {
        try {
            $res = $this->client->get('logs', [
                'query' => [
                    'api_token'       => $this->token,      // <-- REQUIRED
                    'start'           => $start,
                    'end'             => $end,
                    'page'            => $page,
                    'per_page'        => $perPage,
                    'criteria'        => $criteria,         // "sync_time" or "logged_time"
                    'order_key'       => $orderKey,         // "sync_time" or "logged_time"
                    'order_direction' => $orderDir,         // "asc" | "desc"
                ],
            ]);
            return json_decode((string) $res->getBody(), true);
        } catch (\Throwable $e) {
            return ['success'=>false,'message'=>$this->errorMessage($e)];
        }
    }


    /* ---------------- People (create/update) ---------------- */

    /**
     * Basic upsert via POST /people (create or update by identifier).
     */
    public function upsertPerson(array $payload, $imagePath = null)
    {
        if (empty($payload['identifier']))  return ['success'=>false,'message'=>'identifier missing'];
        if (empty($payload['name']))        return ['success'=>false,'message'=>'name missing'];
        if (empty($payload['person_type'])) $payload['person_type'] = 'employee';

        $multipart = $this->buildMultipart($payload, $imagePath);

        try {
            $res  = $this->client->post('people', ['multipart' => $multipart]);
            $json = json_decode((string) $res->getBody(), true);
            return ['success'=>true,'payload'=>$json];
        } catch (ClientException $e) {
            $code = $e->getResponse() ? $e->getResponse()->getStatusCode() : 0;
            $body = $e->getResponse() ? json_decode((string) $e->getResponse()->getBody(), true) : null;
            $err  = is_array($body) ? (($body['error'] ?? '') ?: ($body['message'] ?? '')) : '';

            if ($code === 409 && stripos((string)$err, 'rfid') !== false) {
                $noRfid = $payload; unset($noRfid['rfid']);
                $multipart2 = $this->buildMultipart($noRfid, $imagePath);
                try {
                    $res2  = $this->client->post('people', ['multipart' => $multipart2]);
                    $json2 = json_decode((string) $res2->getBody(), true);
                    return ['success'=>true,'payload'=>$json2,'rfid_removed'=>true];
                } catch (\Throwable $e2) {
                    return ['success'=>false,'message'=>$this->errorMessage($e2)];
                }
            }

            return ['success'=>false,'message'=>$this->errorMessage($e)];
        } catch (\Throwable $e) {
            return ['success'=>false,'message'=>$this->errorMessage($e)];
        }
    }

    /**
     * Robust upsert: tries PUT /people/{identifier} then POST, handles RFID conflicts, and vendor quirks.
     */
    public function upsertPersonSafe(array $payload, $imagePath = null): array
    {
        $identifier = trim((string) ($payload['identifier'] ?? ''));
        if ($identifier === '')  return ['ok'=>false,'message'=>'identifier missing'];
        if (empty($payload['name'])) return ['ok'=>false,'message'=>'name missing'];
        if (empty($payload['person_type'])) $payload['person_type'] = 'employee';

        $mp = fn(array $data, $img) => $this->buildMultipart($data, $img);

        $parseErr = function($e) {
            $resp = method_exists($e, 'getResponse') ? $e->getResponse() : null;
            $raw  = $resp ? (string)$resp->getBody() : '';
            $j    = $raw ? json_decode($raw, true) : null;
            if (is_array($j)) {
                if (!empty($j['errors'])) {
                    foreach ($j['errors'] as $k=>$v) {
                        if (is_array($v)) return $k.': '.implode(', ', $v);
                        return (string)$v;
                    }
                }
                if (!empty($j['message'])) return (string)$j['message'];
                if (!empty($j['error']))   return (string)$j['error'];
            }
            return $this->errorMessage($e);
        };

        try {
            // PUT update
            $res = $this->client->request('PUT', 'people/'.$identifier, [
                'multipart'   => $mp($payload, $imagePath),
                'synchronous' => true,
            ]);
            return ['ok'=>true,'data'=>json_decode((string)$res->getBody(), true)];
        } catch (RequestException $e) {
            $status = $e->getResponse() ? $e->getResponse()->getStatusCode() : 0;
            $raw    = $e->getResponse() ? (string)$e->getResponse()->getBody() : '';

            // Create on 404
            if ($status === 404) {
                try {
                    $res = $this->client->request('POST', 'people', [
                        'multipart'   => $mp($payload, $imagePath),
                        'synchronous' => true,
                    ]);
                    return ['ok'=>true,'data'=>json_decode((string)$res->getBody(), true)];
                } catch (RequestException $e2) {
                    $status2 = $e2->getResponse() ? $e2->getResponse()->getStatusCode() : 0;
                    $raw2    = $e2->getResponse() ? (string)$e2->getResponse()->getBody() : '';

                    if ($status2 === 409 && stripos($raw2, 'rfid') !== false) {
                        $no = $payload; unset($no['rfid']);
                        try {
                            $res2 = $this->client->request('POST', 'people', [
                                'multipart'   => $mp($no, $imagePath),
                                'synchronous' => true,
                            ]);
                            return ['ok'=>true,'rfid_removed'=>true,'data'=>json_decode((string)$res2->getBody(), true)];
                        } catch (\Throwable $e3) {
                            return ['ok'=>false,'message'=>$this->errorMessage($e3)];
                        }
                    }

                    if ($status2 === 422 && stripos($raw2, 'identifier') !== false) {
                        try {
                            $res3 = $this->client->request('POST', 'people', [
                                'form_params' => array_merge(['api_token'=>$this->token], array_filter($payload, fn($v)=>$v!==null)),
                                'synchronous' => true,
                            ]);
                            return ['ok'=>true,'data'=>json_decode((string)$res3->getBody(), true)];
                        } catch (\Throwable $e3b) {
                            return ['ok'=>false,'message'=>$parseErr($e3b)];
                        }
                    }

                    return ['ok'=>false,'message'=>$parseErr($e2)];
                }
            }

            // RFID conflict on PUT
            if ($status === 409 && stripos($raw, 'rfid') !== false) {
                $no = $payload; unset($no['rfid']);
                try {
                    $res = $this->client->request('PUT', 'people/'.$identifier, [
                        'multipart'   => $mp($no, $imagePath),
                        'synchronous' => true,
                    ]);
                    return ['ok'=>true,'rfid_removed'=>true,'data'=>json_decode((string)$res->getBody(), true)];
                } catch (\Throwable $e4) {
                    return ['ok'=>false,'message'=>$this->errorMessage($e4)];
                }
            }

            // Vendor 422 "identifier required" quirk
            if ($status === 422 && stripos($raw, 'identifier') !== false) {
                try {
                    $res = $this->client->request('POST', 'people', [
                        'multipart'   => $mp($payload, $imagePath),
                        'synchronous' => true,
                    ]);
                    return ['ok'=>true,'data'=>json_decode((string)$res->getBody(), true)];
                } catch (\Throwable $e2again) {
                    try {
                        $res3 = $this->client->request('POST', 'people', [
                            'form_params' => array_merge(['api_token'=>$this->token], array_filter($payload, fn($v)=>$v!==null)),
                            'synchronous' => true,
                        ]);
                        return ['ok'=>true,'data'=>json_decode((string)$res3->getBody(), true)];
                    } catch (\Throwable $e3again) {
                        return ['ok'=>false,'message'=>$parseErr($e3again)];
                    }
                }
            }

            return ['ok'=>false,'message'=>$parseErr($e)];
        } catch (\Throwable $e) {
            return ['ok'=>false,'message'=>$this->errorMessage($e)];
        }
    }

    /* ---------------- Allocations ---------------- */

    public function allocatePersonToDevice($device, $personIdentifier, $action = 'allocate')
    {
        // 1) form-encoded
        try {
            $r1 = $this->client->post("devices/{$device}/allocations", [
                'form_params' => [
                    'api_token'         => $this->token,
                    'person_identifier' => $personIdentifier,
                    'action'            => $action,
                ],
            ]);
            $j1 = json_decode((string) $r1->getBody(), true);
            if ($this->isOkAllocationResponse($j1)) return $j1;
        } catch (\Throwable $e) {}

        // 2) JSON
        try {
            $r2 = $this->client->post("devices/{$device}/allocations", [
                'json' => [
                    'api_token'         => $this->token,
                    'person_identifier' => $personIdentifier,
                    'action'            => $action,
                ],
            ]);
            $j2 = json_decode((string) $r2->getBody(), true);
            if ($this->isOkAllocationResponse($j2)) return $j2;
        } catch (\Throwable $e) {}

        // 3) batch with device_ids
        if (preg_match('/^\d+$/', (string) $device)) {
            try {
                $r3 = $this->client->post('devices/batch-allocations', [
                    'json' => [
                        'api_token'          => $this->token,
                        'action'             => $action,
                        'person_identifiers' => [$personIdentifier],
                        'device_ids'         => [(int) $device],
                    ],
                ]);
                $j3 = json_decode((string) $r3->getBody(), true);
                if ($this->isOkAllocationResponse($j3)) return $j3;
            } catch (\Throwable $e) {}
        }

        // 4) batch with device_identifiers
        try {
            $r4 = $this->client->post('devices/batch-allocations', [
                'json' => [
                    'api_token'          => $this->token,
                    'action'             => $action,
                    'person_identifiers' => [$personIdentifier],
                    'device_identifiers' => [(string) $device],
                ],
            ]);
            $j4 = json_decode((string) $r4->getBody(), true);
            if ($this->isOkAllocationResponse($j4)) return $j4;
        } catch (\Throwable $e) {
            return ['success'=>false,'message'=>$this->errorMessage($e)];
        }

        return ['success'=>false,'message'=>'Allocation request sent but response not recognized as success'];
    }

    public function batchAllocations($action, array $personIdentifiers, array $deviceIdsOrIdentifiers)
    {
        try {
            $r = $this->client->post('devices/batch-allocations', [
                'json' => [
                    'api_token'          => $this->token,
                    'action'             => $action,
                    'person_identifiers' => array_values($personIdentifiers),
                    'device_ids'         => array_values($deviceIdsOrIdentifiers),
                ],
            ]);
            return json_decode((string) $r->getBody(), true);
        } catch (\Throwable $e) {
            try {
                $r2 = $this->client->post('devices/batch-allocations', [
                    'json' => [
                        'api_token'          => $this->token,
                        'action'             => $action,
                        'person_identifiers' => array_values($personIdentifiers),
                        'device_identifiers' => array_values($deviceIdsOrIdentifiers),
                    ],
                ]);
                return json_decode((string) $r2->getBody(), true);
            } catch (\Throwable $e2) {
                return ['success'=>false,'message'=>$this->errorMessage($e2)];
            }
        }
    }

    /* ---------------- Revocation ---------------- */

    public function revokeAllDevicesForPerson(string $identifier)
    {
        try {
            $r = $this->client->post("people/{$identifier}/revoke-all", [
                'query' => ['api_token' => $this->token],
            ]);
            $j = json_decode((string) $r->getBody(), true);
            return ['success'=>true,'payload'=>$j];
        } catch (\Throwable $e) {
            try {
                $r2 = $this->client->post("people/{$identifier}/revoke-all", [
                    'json' => ['api_token' => $this->token],
                ]);
                $j2 = json_decode((string) $r2->getBody(), true);
                return ['success'=>true,'payload'=>$j2];
            } catch (\Throwable $e2) {
                return ['success'=>false,'message'=>$this->errorMessage($e2)];
            }
        }
    }

    public function revokePerson(string $identifier, $deviceIdentifier = null)
    {
        if ($deviceIdentifier) {
            $r = $this->allocatePersonToDevice($deviceIdentifier, $identifier, 'revoke');
            if ($this->isOkAllocationResponse($r) || ($r['success'] ?? false)) {
                return ['success'=>true,'payload'=>$r];
            }
            return ['error'=>true,'message'=> is_array($r) ? ($r['message'] ?? 'Revoke failed') : 'Revoke failed'];
        }

        try {
            $r = $this->client->post("people/{$identifier}/revoke-all", [
                'query' => ['api_token' => $this->token],
            ]);
            $j = json_decode((string) $r->getBody(), true);
            return ['success'=>true,'payload'=>$j];
        } catch (RequestException $e) {
            $status = $e->getResponse() ? $e->getResponse()->getStatusCode() : 0;
            if ($status === 404) {
                return ['success'=>true,'payload'=>['message'=>'already_revoked']];
            }
            try {
                $r2 = $this->client->post("people/{$identifier}/revoke-all", [
                    'json' => ['api_token' => $this->token],
                ]);
                $j2 = json_decode((string) $r2->getBody(), true);
                return ['success'=>true,'payload'=>$j2];
            } catch (\Throwable $e2) {
                return ['error'=>true,'message'=>$this->errorMessage($e2)];
            }
        } catch (\Throwable $e) {
            return ['error'=>true,'message'=>$this->errorMessage($e)];
        }
    }
}
