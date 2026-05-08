<?php

namespace App\Repositories;

use App\Models\TipsoiDevice;
use Illuminate\Support\Facades\Log;

class DeviceRepository
{
    protected $model;

    public function __construct(TipsoiDevice $device)
    {
        $this->model = $device;
    }

    /**
     * Get all active devices with optional fallback
     */
    public function getActiveDevices($withFallback = false)
    {
        $devices = $this->model->active()->orderBy('name')->get();
        
        if ($withFallback && $devices->isEmpty()) {
            // Fallback to default device if configured
            $defaultDeviceId = config('tipsoi.default_device');
            if ($defaultDeviceId) {
                return $this->model->where('identifier', $defaultDeviceId)->get();
            }
        }
        
        return $devices;
    }

    /**
     * Find device by ID
     */
    public function findById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Find device by identifier
     */
    public function findByIdentifier($identifier)
    {
        return $this->model->where('identifier', $identifier)->first();
    }

    /**
     * Create a new device record
     */
    public function create(array $data)
    {
        try {
            return $this->model->create($data);
        } catch (\Exception $e) {
            Log::error('Failed to create device: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update an existing device
     */
    public function update($id, array $data)
    {
        try {
            $device = $this->findById($id);
            if ($device) {
                $device->update($data);
                return $device;
            }
            return null;
        } catch (\Exception $e) {
            Log::error("Failed to update device {$id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Sync devices from API data
     */
    // public function syncFromApiData(array $apiDevices)
    // {
    //     $syncedCount = 0;
        
    //     foreach ($apiDevices as $deviceData) {
    //         try {
    //             $device = $this->findByIdentifier($deviceData['identifier']);
                
    //             $data = [
    //                 'identifier' => $deviceData['identifier'],
    //                 'name' => $deviceData['name'] ?? 'Unknown Device',
    //                 'status' => ($deviceData['status'] ?? 'active') === 'active' ? 1 : 0,
    //                 'model' => $deviceData['model'] ?? null,
    //                 'ip_address' => $deviceData['ip_address'] ?? null,
    //                 'last_sync_at' => now(),
    //             ];

    //             if ($device) {
    //                 $device->update($data);
    //             } else {
    //                 $this->create($data);
    //             }
                
    //             $syncedCount++;
                
    //         } catch (\Exception $e) {
    //             Log::error("Failed to sync device {$deviceData['identifier']}: " . $e->getMessage());
    //         }
    //     }

    //     return $syncedCount;
    // }

    public function syncFromApiData(array $apiDevices)
    {
        $syncedCount = 0;
        
        foreach ($apiDevices as $deviceData) {
            try {
                $device = $this->model->updateOrCreate(
                    ['identifier' => $deviceData['identifier']],
                    [
                        'name' => $deviceData['name'] ?? 'Unknown Device',
                        'status' => ($deviceData['status'] ?? 'active') === 'active' ? 1 : 0,
                        'model' => $deviceData['model'] ?? null,
                        'ip_address' => $deviceData['ip_address'] ?? null,
                        'location' => $deviceData['location'] ?? null,
                        'last_sync_at' => now()
                    ]
                );
                
                $syncedCount++;
                
            } catch (\Exception $e) {
                Log::error("Failed to sync device {$deviceData['identifier']}: " . $e->getMessage());
            }
        }

        return $syncedCount;
    }

    /**
     * Get devices that need synchronization
     */
    public function getDevicesNeedingSync($hours = 24)
    {
        return $this->model->where('status', 1)
            ->where(function($query) use ($hours) {
                $query->whereNull('last_sync_at')
                      ->orWhere('last_sync_at', '<', now()->subHours($hours));
            })
            ->get();
    }

    /**
     * Get count of active devices
     */
    public function getActiveDevicesCount()
    {
        return $this->model->active()->count();
    }

    /**
     * Get devices by status
     */
    public function getDevicesByStatus($status = 'active')
    {
        return $this->model->where('status', $status === 'active' ? 1 : 0)
            ->orderBy('name')
            ->get();
    }
}