<?php
namespace App\Repositories;

use App\Models\Person;
use Illuminate\Support\Facades\Log;

class PersonRepository
{
    protected $model;

    public function __construct(Person $person)
    {
        $this->model = $person;
    }

    public function getActivePersons()
    {
        return $this->model->active()
            ->orderBy('name')
            ->get(['id', 'name', 'identifier']);
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function findByIdentifier($identifier)
    {
        return $this->model->where('identifier', $identifier)->first();
    }

    public function syncFromTipsoi(array $personData)
    {
        return $this->model->updateOrCreate(
            ['tipsoi_id' => $personData['id']],
            [
                'identifier' => $personData['identifier'],
                'name' => $personData['name'],
                'status' => $personData['status'] === 'active' ? 1 : 0,
                'last_sync_at' => now(),
                'sync_failed' => false
            ]
        );
    }

    public function getUnsyncedPersons($force = false)
    {
        $query = $this->model->query();
        
        if (!$force) {
            $query->needsSync();
        }
        
        return $query->get();
    }

    public function getFailedSyncs()
    {
        return $this->model->failedSyncs()->get();
    }

    public function markAsSynced(array $identifiers)
    {
        return $this->model->whereIn('identifier', $identifiers)
            ->update([
                'last_sync_at' => now(),
                'sync_failed' => false
            ]);
    }

    public function updateOrCreate(array $attributes, array $values)
    {
        return $this->model->updateOrCreate($attributes, $values);
    }

    public function getLastSyncTime()
    {
        return $this->model->max('last_sync_at');
    }

    public function create(array $data)
    {
        try {
            return $this->model->create($data);
        } catch (\Exception $e) {
            Log::error('Failed to create person: ' . $e->getMessage());
            return null;
        }
    }

    public function update($id, array $data)
    {
        try {
            $person = $this->findById($id);
            if ($person) {
                $person->update($data);
                return $person;
            }
            return null;
        } catch (\Exception $e) {
            Log::error("Failed to update person {$id}: " . $e->getMessage());
            return null;
        }
    }
}