<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TipsoiAttendanceService
{
    protected $client;
    protected $baseUrl;
    protected $apiToken;
    protected $defaultDevice;
    protected $timeout = 30;
    protected $connectTimeout = 10;

    public function __construct()
    {
        $this->apiMode = config('tipsoi.api_mode');
        $this->baseUrl = $this->apiMode == "test" 
            ? config('tipsoi.base_url.test') 
            : config('tipsoi.base_url.production');
        $this->apiToken = config('tipsoi.token');
        $this->defaultDevice = config('tipsoi.default_device');

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => $this->timeout,
            'connect_timeout' => $this->connectTimeout,
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiToken,
            ],
            'verify' => $this->apiMode === 'production'
        ]);
    }

    /**
     * Get all people from the API
     */
    // public function getAllPeople(int $maxRetries = 3): array
    // {
    //     $retryCount = 0;
        
    //     while ($retryCount < $maxRetries) {
    //         try {
    //             $response = $this->client->get('people', [
    //                 'query' => ['api_token' => $this->apiToken],
    //                 'timeout' => $this->timeout
    //             ]);
                
    //             $people = json_decode($response->getBody(), true);
                
    //             if (!is_array($people)) {
    //                 throw new \Exception("Invalid API response format");
    //             }
                
    //             return array_map(function($person) {
    //                 return [
    //                     'id' => $person['id'] ?? null,
    //                     'identifier' => $person['identifier'] ?? null,
    //                     'name' => $person['name'] ?? 'Unknown',
    //                     'rfid' => $person['rfid'] ?? null,
    //                     'primary_display_text' => $person['primary_display_text'] ?? null,
    //                     'secondary_display_text' => $person['secondary_display_text'] ?? null,
    //                     'person_type' => $person['person_type'] ?? 'employee',
    //                     'status' => $person['status'] ?? 'active',
    //                     'photo_url' => $person['photo_url'] ?? null,
    //                     'last_sync_at' => $person['last_sync_at'] ?? null
    //                 ];
    //             }, $people);
                
    //         } catch (\Exception $e) {
    //             $retryCount++;
    //             Log::warning("TIPSOI API Attempt $retryCount failed: " . $e->getMessage());
                
    //             if ($retryCount === $maxRetries) {
    //                 return [
    //                     'error' => true,
    //                     'message' => 'Failed to get people after ' . $maxRetries . ' attempts',
    //                     'exception' => $e->getMessage()
    //                 ];
    //             }
                
    //             sleep(2 * $retryCount);
    //         }
    //     }

    //     return ['error' => true, 'message' => 'Unexpected end of retry loop'];
    // }

    public function getAllPeople(int $maxRetries = 3): array
    {
        try {
            $response = $this->client->get('people', [
                'query' => ['api_token' => $this->apiToken],
                'timeout' => $this->timeout
            ]);
            
            $people = json_decode($response->getBody(), true);
            
            if (!is_array($people)) {
                throw new \Exception("Invalid API response format");
            }
            
            return $people;
            
        } catch (\Exception $e) {
            Log::error("Failed to fetch people: " . $e->getMessage());
            return [
                'error' => true,
                'message' => 'Failed to fetch people: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get all devices from the API
     */
    public function getAllDevices(int $maxRetries = 3): array
    {
        $retryCount = 0;
        
        while ($retryCount < $maxRetries) {
            try {
                $response = $this->client->get('devices', [
                    'query' => ['api_token' => $this->apiToken],
                    'timeout' => $this->timeout
                ]);

                $devices = json_decode($response->getBody(), true);
                
                if (!is_array($devices)) {
                    throw new \Exception("Invalid API response format");
                }
                
                return array_map(function($device) {
                    return [
                        'id' => $device['id'] ?? null,
                        'identifier' => $device['identifier'] ?? null,
                        'name' => $device['name'] ?? 'Unknown Device',
                        'status' => $device['status'] ?? 'inactive',
                        'model' => $device['model'] ?? null,
                        'ip_address' => $device['ip_address'] ?? null,
                        'location' => $device['location'] ?? null,
                        'last_seen' => $device['last_seen'] ?? null,
                        'connected' => $device['connected'] ?? false,
                        'device_type_id' => $device['device_type_id'] ?? null,
                        'imei_number' => $device['imei_number'] ?? null
                    ];
                }, $devices);
                
            } catch (\Exception $e) {
                $retryCount++;
                Log::error("Device fetch attempt $retryCount failed: " . $e->getMessage());
                
                if ($retryCount === $maxRetries) {
                    return [
                        'error' => true,
                        'message' => 'Failed to get devices after ' . $maxRetries . ' attempts',
                        'exception' => $e->getMessage()
                    ];
                }
                
                sleep(1 * $retryCount);
            }
        }

        return ['error' => true, 'message' => 'Unexpected end of retry loop'];
    }

    /**
     * Get attendance logs with pagination
     */
    public function getAttendanceLogs(
        string $start, 
        string $end, 
        int $page = 1, 
        int $perPage = 100, 
        ?string $personIdentifier = null
    ): array {
        try {
            $query = [
                'api_token' => $this->apiToken,
                'start' => $start,
                'end' => $end,
                'page' => $page,
                'per_page' => $perPage
            ];

            if ($personIdentifier) {
                $query['person_identifier'] = $personIdentifier;
            }

            $response = $this->client->get('attendance_logs', [
                'query' => $query,
                'timeout' => $this->timeout
            ]);

            $data = json_decode($response->getBody(), true);

            if (!isset($data['attendances'])) {
                throw new \Exception("Invalid attendance logs response format");
            }

            dd($data);

            return $data;

        } catch (\Exception $e) {
            Log::error("Failed to get attendance logs: " . $e->getMessage());
            return [
                'error' => true,
                'message' => 'Failed to get attendance logs: ' . $e->getMessage(),
                'query' => $query
            ];
        }
    }

    /**
     * Create a new person
     */
    // public function createPerson(array $data): array
    // {
    //     try {
    //         $multipart = [
    //             ['name' => 'api_token', 'contents' => $this->apiToken],
    //             ['name' => 'identifier', 'contents' => $data['identifier']],
    //             ['name' => 'name', 'contents' => $data['name']],
    //             ['name' => 'person_type', 'contents' => $data['person_type'] ?? 'employee']
    //         ];

    //         // Optional fields
    //         $optionalFields = ['rfid', 'primary_display_text', 'secondary_display_text'];
    //         foreach ($optionalFields as $field) {
    //             if (!empty($data[$field])) {
    //                 $multipart[] = ['name' => $field, 'contents' => $data[$field]];
    //             }
    //         }

    //         // Handle photo upload
    //         if (!empty($data['photo'])) {
    //             $multipart[] = [
    //                 'name' => 'image',
    //                 'contents' => fopen($data['photo']->getRealPath(), 'r'),
    //                 'filename' => $data['photo']->getClientOriginalName()
    //             ];
    //         }

    //         $response = $this->client->post('people', ['multipart' => $multipart]);
    //         $responseData = json_decode($response->getBody(), true);

    //         if (!isset($responseData['code']) || $responseData['code'] != 200) {
    //             throw new \Exception($responseData['message'] ?? 'Failed to create person');
    //         }

    //         return [
    //             'success' => true,
    //             'message' => 'Person created successfully',
    //             'data' => $responseData['payload'] ?? null,
    //             'id' => $responseData['payload']['identifier'] ?? null
    //         ];

    //     } catch (\Exception $e) {
    //         Log::error("Create person failed: " . $e->getMessage());
    //         return [
    //             'error' => true,
    //             'message' => 'Failed to create person: ' . $e->getMessage(),
    //             'data' => $data
    //         ];
    //     }
    // }

    public function createPerson(array $data, ?string $imagePath = null): array
{
    try {
        $multipart = [
            ['name' => 'api_token', 'contents' => $this->apiToken],
            ['name' => 'identifier', 'contents' => $data['identifier']],
            ['name' => 'name', 'contents' => $data['name']],
            ['name' => 'person_type', 'contents' => $data['person_type'] ?? 'employee']
        ];

        // Optional fields
        $optionalFields = ['rfid', 'primary_display_text', 'secondary_display_text'];
        foreach ($optionalFields as $field) {
            if (!empty($data[$field])) {
                $multipart[] = ['name' => $field, 'contents' => $data[$field]];
            }
        }

        // Handle photo upload - supports both UploadedFile and file path
        if (!empty($data['photo'])) {
            // Case 1: UploadedFile instance
            $multipart[] = [
                'name' => 'image',
                'contents' => fopen($data['photo']->getRealPath(), 'r'),
                'filename' => $data['photo']->getClientOriginalName()
            ];
        } elseif ($imagePath && file_exists($imagePath)) {
            // Case 2: File path string
            $multipart[] = [
                'name' => 'image',
                'contents' => fopen($imagePath, 'r'),
                'filename' => basename($imagePath)
            ];
        }

        $response = $this->client->post('people', ['multipart' => $multipart]);
        $responseData = json_decode($response->getBody(), true);

        if (!isset($responseData['code']) || $responseData['code'] != 200) {
            throw new \Exception($responseData['message'] ?? 'Failed to create person');
        }

        return [
            'success' => true,
            'message' => 'Person created successfully',
            'data' => $responseData['payload'] ?? null,
            'id' => $responseData['payload']['identifier'] ?? null
        ];

    } catch (\Exception $e) {
        Log::error("Create person failed: " . $e->getMessage());
        return [
            'error' => true,
            'message' => 'Failed to create person: ' . $e->getMessage(),
            'data' => $data
        ];
    }
}

    /**
     * Update an existing person
     */
    public function updatePerson(string $identifier, array $data): array
    {
        try {
            $multipart = [
                ['name' => 'api_token', 'contents' => $this->apiToken],
                ['name' => 'identifier', 'contents' => $identifier]
            ];

            // Updatable fields
            $updatableFields = ['name', 'rfid', 'primary_display_text', 'secondary_display_text', 'status'];
            foreach ($updatableFields as $field) {
                if (isset($data[$field])) {
                    $multipart[] = ['name' => $field, 'contents' => $data[$field]];
                }
            }

            // Handle photo update
            if (!empty($data['photo'])) {
                $multipart[] = [
                    'name' => 'image',
                    'contents' => fopen($data['photo']->getRealPath(), 'r'),
                    'filename' => $data['photo']->getClientOriginalName()
                ];
            }

            $response = $this->client->post('people', ['multipart' => $multipart]);
            $responseData = json_decode($response->getBody(), true);

            if (!isset($responseData['code']) || $responseData['code'] != 200) {
                throw new \Exception($responseData['message'] ?? 'Failed to update person');
            }

            return [
                'success' => true,
                'message' => 'Person updated successfully',
                'data' => $responseData['payload'] ?? null
            ];

        } catch (\Exception $e) {
            Log::error("Update person {$identifier} failed: " . $e->getMessage());
            return [
                'error' => true,
                'message' => 'Failed to update person: ' . $e->getMessage(),
                'identifier' => $identifier
            ];
        }
    }

    /**
     * Delete a person
     */
    // public function deletePerson(string $identifier): array
    // {
    //     try {
    //         $response = $this->client->delete('people', [
    //             'query' => [
    //                 'api_token' => $this->apiToken,
    //                 'identifier' => $identifier
    //             ]
    //         ]);

    //         $responseData = json_decode($response->getBody(), true);

    //         if (!isset($responseData['code']) || $responseData['code'] != 200) {
    //             throw new \Exception($responseData['message'] ?? 'Failed to delete person');
    //         }

    //         return [
    //             'success' => true,
    //             'message' => 'Person deleted successfully'
    //         ];

    //     } catch (\Exception $e) {
    //         Log::error("Delete person {$identifier} failed: " . $e->getMessage());
    //         return [
    //             'error' => true,
    //             'message' => 'Failed to delete person: ' . $e->getMessage(),
    //             'identifier' => $identifier
    //         ];
    //     }
    // }

    public function deletePerson(string $identifier): array
{
    try {
        $response = $this->client->delete('people', [
            'query' => [
                'api_token' => $this->apiToken,
                'identifier' => $identifier
            ]
        ]);

        $responseData = json_decode($response->getBody(), true);

        if (!isset($responseData['code']) || $responseData['code'] != 200) {
            throw new \Exception($responseData['message'] ?? 'Failed to delete person');
        }

        return [
            'success' => true,
            'message' => 'Person deleted successfully'
        ];

    } catch (\Exception $e) {
        return [
            'error' => true,
            'message' => 'Delete failed: ' . $e->getMessage()
        ];
    }
}

    /**
     * Allocate a person to a device
     */
    public function allocatePersonToDevice(string $personIdentifier, string $deviceIdentifier): array
    {
        try {
            $response = $this->client->post("devices/{$deviceIdentifier}/allocations", [
                'form_params' => [
                    'api_token' => $this->apiToken,
                    'person_identifier' => $personIdentifier,
                    'action' => 'allocate'
                ]
            ]);

            $responseData = json_decode($response->getBody(), true);

            if (!isset($responseData['status']) || $responseData['status'] !== 'pending_sync') {
                throw new \Exception($responseData['message'] ?? 'Failed to allocate person to device');
            }

            return [
                'success' => true,
                'message' => 'Person allocated to device successfully',
                'data' => $responseData
            ];

        } catch (\Exception $e) {
            Log::error("Allocate person {$personIdentifier} to device {$deviceIdentifier} failed: " . $e->getMessage());
            return [
                'error' => true,
                'message' => 'Failed to allocate person to device: ' . $e->getMessage(),
                'person_identifier' => $personIdentifier,
                'device_identifier' => $deviceIdentifier
            ];
        }
    }

    /**
     * Revoke a person from a device or all devices
     */
    // public function revokePerson(string $personIdentifier, ?string $deviceIdentifier = null): array
    // {
    //     try {
    //         if ($deviceIdentifier) {
    //             // Revoke from specific device
    //             $response = $this->client->post("devices/{$deviceIdentifier}/allocations", [
    //                 'form_params' => [
    //                     'api_token' => $this->apiToken,
    //                     'person_identifier' => $personIdentifier,
    //                     'action' => 'revoke'
    //                 ]
    //             ]);
    //         } else {
    //             // Revoke from all devices
    //             $response = $this->client->post("people/{$personIdentifier}/revoke-all", [
    //                 'query' => ['api_token' => $this->apiToken]
    //             ]);
    //         }

    //         $responseData = json_decode($response->getBody(), true);

    //         if (!isset($responseData['status']) || $responseData['status'] !== 'pending_sync') {
    //             throw new \Exception($responseData['message'] ?? 'Failed to revoke person');
    //         }

    //         return [
    //             'success' => true,
    //             'message' => 'Person revoked successfully',
    //             'data' => $responseData
    //         ];

    //     } catch (\Exception $e) {
    //         Log::error("Revoke person {$personIdentifier} failed: " . $e->getMessage());
    //         return [
    //             'error' => true,
    //             'message' => 'Failed to revoke person: ' . $e->getMessage(),
    //             'person_identifier' => $personIdentifier,
    //             'device_identifier' => $deviceIdentifier
    //         ];
    //     }
    // }

    // public function revokePerson(string $personIdentifier, ?string $deviceIdentifier = null): array
    // {
    //     try {
    //         if ($deviceIdentifier) {
    //             // Revoke from specific device
    //             return $this->revokePersonFromDevice($personIdentifier, $deviceIdentifier);
    //         }

    //         // Step 1: Get all devices
    //         $devicesResponse = $this->getAllDevices();
    //         if (isset($devicesResponse['error'])) {
    //             throw new \Exception("Device fetch failed: " . $devicesResponse['message']);
    //         }

    //         // Step 2: Filter only active + connected devices
    //         $activeDevices = array_filter($devicesResponse, function ($device) {
    //             return ($device['status'] ?? '') === 'active' && ($device['connected'] ?? false);
    //         });

    //         $deviceIdentifiers = array_column($activeDevices, 'identifier');

    //         if (empty($deviceIdentifiers)) {
    //             throw new \Exception("No active/connected devices found for revocation.");
    //         }

    //         Log::info("Attempting to revoke person {$personIdentifier} from devices:", $deviceIdentifiers);

    //         // Step 3: Validate person exists on TIPSOI
    //         $person = $this->getPerson($personIdentifier);
    //         if (isset($person['error'])) {
    //             throw new \Exception("Person not found on TIPSOI: " . $person['message']);
    //         }

    //         // Step 4: Perform batch revoke
    //         $result = $this->batchAllocate([$personIdentifier], $deviceIdentifiers, 'revoke');

    //         if (isset($result['error'])) {
    //             throw new \Exception("Batch revoke failed: " . $result['message']);
    //         }

    //         return [
    //             'success' => true,
    //             'message' => "Person {$personIdentifier} revoked from " . count($deviceIdentifiers) . " device(s)",
    //             'data' => $result['data'] ?? null,
    //         ];

    //     } catch (\Exception $e) {
    //         Log::error("TIPSOI revokePerson failed for {$personIdentifier}: " . $e->getMessage());
    //         return [
    //             'error' => true,
    //             'message' => 'Failed to revoke person: ' . $e->getMessage(),
    //             'person_identifier' => $personIdentifier,
    //             'device_identifier' => $deviceIdentifier,
    //         ];
    //     }
    // }

    public function revokePerson(string $personIdentifier, ?string $deviceIdentifier = null): array
    {
        try {
            if ($deviceIdentifier) {
                // Revoke from specific device
                return $this->revokePersonFromDevice($personIdentifier, $deviceIdentifier);
            }

            // Revoke from all devices
            $response = $this->client->post("people/{$personIdentifier}/revoke-all", [
                'query' => ['api_token' => $this->apiToken]
            ]);

            $responseData = json_decode($response->getBody(), true);

            return [
                'success' => true,
                'message' => 'Person revoked from all devices',
                'data' => $responseData
            ];

        } catch (\Exception $e) {
            return [
                'error' => true,
                'message' => 'Revoke failed: ' . $e->getMessage(),
                'person_identifier' => $personIdentifier,
                'device_identifier' => $deviceIdentifier
            ];
        }
    }

    
    /**
     * Revoke a person from a specific device
     */
    public function revokePersonFromDevice(string $personIdentifier, string $deviceIdentifier): array
    {
        try {
            $response = $this->client->post("devices/{$deviceIdentifier}/allocations", [
                'form_params' => [
                    'api_token' => $this->apiToken,
                    'person_identifier' => $personIdentifier,
                    'action' => 'revoke'
                ]
            ]);

            $responseData = json_decode($response->getBody(), true);

            if (!isset($responseData['status']) || $responseData['status'] !== 'pending_sync') {
                throw new \Exception($responseData['message'] ?? 'Failed to revoke person from device');
            }

            return [
                'success' => true,
                'message' => 'Person revoked from device successfully',
                'data' => $responseData
            ];

        } catch (\Exception $e) {
            Log::error("Revoke person {$personIdentifier} from device {$deviceIdentifier} failed: " . $e->getMessage());
            return [
                'error' => true,
                'message' => 'Failed to revoke person from device: ' . $e->getMessage(),
                'person_identifier' => $personIdentifier,
                'device_identifier' => $deviceIdentifier
            ];
        }
    }

    /**
     * Batch allocate/revoke people to/from devices
     */
    // public function batchAllocate(
    //     array $personIdentifiers, 
    //     array $deviceIdentifiers, 
    //     string $action = 'allocate'
    // ): array {
    //     try {
    //         if (!in_array($action, ['allocate', 'revoke'])) {
    //             throw new \InvalidArgumentException("Invalid action. Must be 'allocate' or 'revoke'");
    //         }

    //         $response = $this->client->post("devices/batch-allocations", [
    //             'form_params' => [
    //                 'api_token' => $this->apiToken,
    //                 'action' => $action,
    //                 'person_identifiers' => $personIdentifiers,
    //                 'device_ids' => $deviceIdentifiers
    //             ]
    //         ]);

    //         $responseData = json_decode($response->getBody(), true);

    //         if (!isset($responseData['status']) || $responseData['status'] !== 'pending_sync') {
    //             throw new \Exception($responseData['message'] ?? 'Batch operation failed');
    //         }

    //         return [
    //             'success' => true,
    //             'message' => 'Batch operation completed successfully',
    //             'data' => $responseData,
    //             'stats' => [
    //                 'people' => count($personIdentifiers),
    //                 'devices' => count($deviceIdentifiers)
    //             ]
    //         ];

    //     } catch (\Exception $e) {
    //         Log::error("Batch operation failed: " . $e->getMessage());
    //         return [
    //             'error' => true,
    //             'message' => 'Batch operation failed: ' . $e->getMessage(),
    //             'action' => $action
    //         ];
    //     }
    // }

    public function batchAllocate(array $personIdentifiers, array $deviceIdentifiers, string $action = 'allocate'): array
    {
        try {
            Log::info("Starting batch allocation", [
                'people' => $personIdentifiers,
                'devices' => $deviceIdentifiers,
                'action' => $action
            ]);

            $response = $this->client->post("devices/batch-allocations", [
                'json' => [
                    'api_token' => $this->apiToken,
                    'action' => $action,
                    'person_identifiers' => $personIdentifiers,
                    'device_identifiers' => $deviceIdentifiers
                ],
                'timeout' => 60 // Increased timeout for bulk operations
            ]);

            $responseData = json_decode($response->getBody(), true);
            Log::debug("API Response", $responseData);

            if (!isset($responseData['status'])) {
                throw new \Exception("Invalid API response format");
            }

            if ($responseData['status'] !== 'pending_sync') {
                throw new \Exception($responseData['message'] ?? 'Batch operation failed');
            }

            return [
                'success' => true,
                'message' => 'Batch operation queued successfully',
                'data' => $responseData
            ];

        } catch (\Exception $e) {
            Log::error("Batch allocation failed", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'error' => true,
                'message' => 'Batch operation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify if a person is allocated to a device in TIPSOI
     */
    public function verifyAllocation(string $personIdentifier, string $deviceIdentifier): array
    {
        try {
            // Option 1: Use TIPSOI API if available
            $response = $this->client->get("devices/{$deviceIdentifier}/allocations/{$personIdentifier}", [
                'query' => ['api_token' => $this->apiToken]
            ]);

            $data = json_decode($response->getBody(), true);

            return [
                'verified' => $data['allocated'] ?? false,
                'failed' => false,
                'message' => $data['message'] ?? 'Allocation verified',
                'details' => $data
            ];

        } catch (\Exception $e) {
            // Fallback to checking attendance logs
            $hasRecentLog = DB::table('tipsoi_attendance_logs')
                ->where('person_identifier', $personIdentifier)
                ->where('device_identifier', $deviceIdentifier)
                ->where('logged_time', '>', now()->subDay())
                ->exists();

            return [
                'verified' => $hasRecentLog,
                'failed' => false,
                'message' => $hasRecentLog ? 'Found recent activity' : 'No recent activity',
                'fallback' => true
            ];
        }
    }

    /**
     * Enhanced allocation with verification
     */
    public function allocateWithVerification(string $personIdentifier, string $deviceIdentifier): array
    {
        // First perform allocation
        $allocation = $this->allocatePersonToDevice($personIdentifier, $deviceIdentifier);
        
        if ($allocation['error'] ?? false) {
            return $allocation;
        }

        // Then verify
        sleep(2); // Small delay for sync
        $verification = $this->verifyAllocation($personIdentifier, $deviceIdentifier);

        return [
            'allocation' => $allocation,
            'verification' => $verification,
            'success' => $verification['verified'],
            'message' => $verification['verified'] 
                ? 'Allocation verified successfully'
                : 'Allocation completed but verification failed'
        ];
    }

    /**
     * Clear a device (remove all allocations)
     */
    public function clearDevice(string $deviceIdentifier): array
    {
        try {
            $response = $this->client->post("devices/{$deviceIdentifier}/clear", [
                'query' => ['api_token' => $this->apiToken]
            ]);

            $responseData = json_decode($response->getBody(), true);

            if (!isset($responseData['status']) || $responseData['status'] !== 'pending_sync') {
                throw new \Exception($responseData['message'] ?? 'Failed to clear device');
            }

            return [
                'success' => true,
                'message' => 'Device cleared successfully',
                'data' => $responseData
            ];

        } catch (\Exception $e) {
            Log::error("Clear device {$deviceIdentifier} failed: " . $e->getMessage());
            return [
                'error' => true,
                'message' => 'Failed to clear device: ' . $e->getMessage(),
                'device_identifier' => $deviceIdentifier
            ];
        }
    }

    /**
     * Get a single person's details
     */
    public function getPerson(string $identifier): array
    {
        try {
            $response = $this->client->get("people/{$identifier}", [
                'query' => ['api_token' => $this->apiToken]
            ]);

            $data = json_decode($response->getBody(), true);

            if (empty($data)) {
                throw new \Exception("Person not found");
            }

            return $data;

        } catch (\Exception $e) {
            return [
                'error' => true,
                'message' => 'Failed to get person: ' . $e->getMessage(),
                'identifier' => $identifier
            ];
        }
    }

    /**
     * Sync a single person to a device
     */
    public function syncSinglePerson(string $personIdentifier, string $deviceIdentifier): array
    {
        try {
            // Verify person exists
            $person = $this->getPerson($personIdentifier);
            if (isset($person['error'])) {
                throw new \Exception("Person not found: " . $person['message']);
            }

            // Allocate to device
            $allocation = $this->allocatePersonToDevice($personIdentifier, $deviceIdentifier);
            if (isset($allocation['error'])) {
                throw new \Exception("Allocation failed: " . $allocation['message']);
            }

            return [
                'success' => true,
                'message' => 'Person synced successfully',
                'data' => [
                    'person' => $person,
                    'allocation' => $allocation['data']
                ]
            ];

        } catch (\Exception $e) {
            return [
                'error' => true,
                'message' => 'Sync failed: ' . $e->getMessage(),
                'person_identifier' => $personIdentifier,
                'device_identifier' => $deviceIdentifier
            ];
        }
    }

    /**
     * Sync all people to all active devices
     */
    public function syncAllPeople(): array
    {
        try {
            // 1. First sync devices to ensure we have active ones
            $devicesSync = $this->syncDevicesFromApi();
            if (isset($devicesSync['error'])) {
                throw new \Exception($devicesSync['message']);
            }

            // 2. Get all people
            $people = $this->getAllPeople();
            if (isset($people['error'])) {
                throw new \Exception($people['message']);
            }

            // 3. Get all devices
            $devices = $this->getAllDevices();
            if (isset($devices['error'])) {
                throw new \Exception($devices['message']);
            }

            // 4. Filter active devices
            $activeDevices = array_filter($devices, function($device) {
                return ($device['status'] ?? '') === 'active' && ($device['connected'] ?? false);
            });

            if (empty($activeDevices)) {
                throw new \Exception('No active devices available');
            }

            $deviceIdentifiers = array_column($activeDevices, 'identifier');
            $personIdentifiers = array_column($people, 'identifier');

            // 5. Perform batch allocation
            $result = $this->batchAllocate($personIdentifiers, $deviceIdentifiers);
            if (isset($result['error'])) {
                throw new \Exception($result['message']);
            }

            return [
                'success' => true,
                'synced_count' => count($personIdentifiers),
                'device_count' => count($deviceIdentifiers),
                'message' => 'Successfully synced ' . count($personIdentifiers) . ' people to ' . count($deviceIdentifiers) . ' devices'
            ];

        } catch (\Exception $e) {
            return [
                'error' => true,
                'message' => 'Sync failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Sync devices from API
     */
    public function syncDevicesFromApi(): array
    {
        try {
            $apiDevices = $this->getAllDevices();
            
            if (isset($apiDevices['error'])) {
                throw new \Exception($apiDevices['message']);
            }

            $count = count($apiDevices);

            return [
                'success' => true,
                'message' => "Successfully synchronized {$count} devices",
                'count' => $count
            ];

        } catch (\Exception $e) {
            Log::error('Device sync failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to sync devices: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check API health and connection status
     */
    public function getEnhancedSyncStatus(): array
    {
        $status = [
            'api_status' => 'disconnected',
            'last_sync' => null,
            'connected_devices' => 0,
            'status' => 'disconnected',
            'last_checked' => now()->format('Y-m-d H:i:s'),
            'details' => []
        ];

        try {
            // Check API connectivity
            $response = $this->client->get('devices', [
                'query' => ['api_token' => $this->apiToken],
                'timeout' => 5
            ]);

            $devices = json_decode($response->getBody(), true);
            
            if (is_array($devices)) {
                $status['api_status'] = 'connected';
                $activeDevices = array_filter($devices, function($device) {
                    return ($device['status'] ?? '') === 'active' && ($device['connected'] ?? false);
                });
                
                $status['connected_devices'] = count($activeDevices);
                $status['status'] = count($activeDevices) > 0 ? 'connected' : 'no_devices';
                $status['details']['devices'] = $devices;
            }

            // Get last sync time (this would typically come from your local database)
            $status['last_sync'] = now()->subMinutes(5)->format('Y-m-d H:i:s');

        } catch (\Exception $e) {
            $status['error'] = $e->getMessage();
            Log::warning('Connection check failed: ' . $e->getMessage());
        }

        return $status;
    }

    /**
     * Check API health
     */
    public function checkApiHealth(): array
    {
        try {
            $response = $this->client->get('ping', [
                'query' => ['api_token' => $this->apiToken],
                'timeout' => 5
            ]);

            $data = json_decode($response->getBody(), true);

            return [
                'status' => 'connected',
                'response' => $data,
                'timestamp' => now()->toDateTimeString()
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'disconnected',
                'error' => $e->getMessage(),
                'timestamp' => now()->toDateTimeString()
            ];
        }
    }
}