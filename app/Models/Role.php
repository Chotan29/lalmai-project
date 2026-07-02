<?php
namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Zizaco\Entrust\EntrustRole;


class Role extends EntrustRole
{
    protected $fillable = [ 'created_at','updated_at','name', 'display_name', 'description'];

    protected function permissionCacheKey()
    {
        return 'entrust_permissions_for_role_'.$this->getKey();
    }

    protected function supportsTaggedCache()
    {
        return method_exists(Cache::getStore(), 'tags');
    }

    public function cachedPermissions()
    {
        $cacheKey = $this->permissionCacheKey();
        $ttl = Config::get('cache.ttl');

        if ($this->supportsTaggedCache()) {
            return Cache::tags(Config::get('entrust.permission_role_table'))->remember($cacheKey, $ttl, function () {
                return $this->perms()->get();
            });
        }

        return Cache::remember($cacheKey, $ttl, function () {
            return $this->perms()->get();
        });
    }

    protected function clearCachedPermissions()
    {
        if ($this->supportsTaggedCache()) {
            Cache::tags(Config::get('entrust.permission_role_table'))->flush();
            return;
        }

        Cache::forget($this->permissionCacheKey());
    }

    public function save(array $options = [])
    {
        $result = parent::save($options);
        $this->clearCachedPermissions();
        return $result;
    }

    public function delete(array $options = [])
    {
        $result = parent::delete($options);
        $this->clearCachedPermissions();
        return $result;
    }

    public function restore()
    {
        $result = parent::restore();
        $this->clearCachedPermissions();
        return $result;
    }

}