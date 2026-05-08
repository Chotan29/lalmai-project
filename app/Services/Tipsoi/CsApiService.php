<?php
// app/Services/Tipsoi/CsApiService.php
namespace App\Services\Tipsoi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class CsApiService
{
    protected $client;
    protected $token;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => rtrim(config('tipsoi.cs.base_url'), '/') . '/',
            'timeout'  => config('tipsoi.cs.timeout', 12),
            'http_errors' => false,
        ]);
        $this->token  = config('tipsoi.cs.api_token');
    }

    protected function get($path, array $query = [])
    {
        $query['api_token'] = $this->token;
        try {
            $res = $this->client->get($path, ['query' => $query]);
            return [ 'status' => $res->getStatusCode(), 'json' => json_decode($res->getBody(), true) ];
        } catch (GuzzleException $e) {
            Log::error('CS GET error', ['path'=>$path,'q'=>$query,'err'=>$e->getMessage()]);
            return [ 'status' => 0, 'error' => $e->getMessage() ];
        }
    }

    protected function post($path, array $form = [], array $files = [])
    {
        $form['api_token'] = $this->token;
        $options = ['multipart' => []];
        foreach ($form as $k=>$v){
            $options['multipart'][] = ['name'=>$k, 'contents'=>is_scalar($v) ? $v : json_encode($v)];
        }
        foreach ($files as $name => $filePath){
            $options['multipart'][] = ['name'=>$name, 'contents'=>fopen($filePath,'r'), 'filename'=>basename($filePath)];
        }

        try {
            $res = $this->client->post($path, $options);
            return [ 'status' => $res->getStatusCode(), 'json' => json_decode($res->getBody(), true) ];
        } catch (GuzzleException $e) {
            Log::error('CS POST error', ['path'=>$path,'err'=>$e->getMessage()]);
            return [ 'status' => 0, 'error' => $e->getMessage() ];
        }
    }

    // === Logs ===
    public function getPunchLogs(string $start, string $end, array $opts = [])
    {
        $q = array_merge(compact('start','end'), $opts);
        return $this->get('logs', $q); // CS /logs per docs. :contentReference[oaicite:5]{index=5}
    }

    public function getAttendanceLogs(string $start, string $end, array $opts = [])
    {
        $q = array_merge(compact('start','end'), $opts);
        return $this->get('attendance_logs', $q); // New consolidated logs. :contentReference[oaicite:6]{index=6}
    }

    // === People ===
    public function createOrUpdatePerson(array $payload, string $imagePath = null)
    {
        $files = $imagePath ? ['image'=>$imagePath] : [];
        return $this->post('people', $payload, $files); // /people form-data per docs. :contentReference[oaicite:7]{index=7}
    }

    // === Allocations ===
    public function allocateToDevice(string $deviceIdentifier, array $items)
    {
        // Body: array of {person_identifier, action} objects. :contentReference[oaicite:8]{index=8}
        return $this->post("devices/{$deviceIdentifier}/allocations", $items);
    }

    public function batchAllocate(array $payload)
    {
        // { action, person_identifiers[], device_ids[] } :contentReference[oaicite:9]{index=9}
        return $this->post('devices/batch-allocations', $payload);
    }

    // === Devices (optional helper) ===
    public function getDevices()
    {
        // Some sandboxes provide /devices via test host; token as usual. :contentReference[oaicite:10]{index=10}
        return $this->get('devices');
    }
}
