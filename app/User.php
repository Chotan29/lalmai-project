<?php

namespace App;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Zizaco\Entrust\Entrust;
use Zizaco\Entrust\Traits\EntrustUserTrait;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Contracts\UserResolver;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements AuditableContract,JWTSubject
{
    //use HasApiTokens, Notifiable, EntrustUserTrait, Auditable;
    use HasApiTokens, Notifiable, EntrustUserTrait, Auditable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','last_login_at','last_login_ip', 'profile_image', 'contact_number', 'address',  'role_id', 'hook_id', 'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function userRole()
    {
        return $this->belongsToMany(Role::class);
    }

    public function userRoleId()
    {
        return $this->hasone(Entrust::class);
    }

    protected function roleCacheKey()
    {
        return 'entrust_roles_for_user_'.$this->getKey();
    }

    protected function supportsTaggedCache()
    {
        return method_exists(Cache::getStore(), 'tags');
    }

    public function cachedRoles()
    {
        $cacheKey = $this->roleCacheKey();
        $ttl = Config::get('cache.ttl');

        if ($this->supportsTaggedCache()) {
            return Cache::tags(Config::get('entrust.role_user_table'))->remember($cacheKey, $ttl, function () {
                return $this->roles()->get();
            });
        }

        return Cache::remember($cacheKey, $ttl, function () {
            return $this->roles()->get();
        });
    }

    protected function clearCachedRoles()
    {
        if ($this->supportsTaggedCache()) {
            Cache::tags(Config::get('entrust.role_user_table'))->flush();
            return;
        }

        Cache::forget($this->roleCacheKey());
    }

    public function save(array $options = [])
    {
        $result = parent::save($options);
        $this->clearCachedRoles();
        return $result;
    }

    public function delete(array $options = [])
    {
        $result = parent::delete($options);
        $this->clearCachedRoles();
        return $result;
    }

    public function restore()
    {
        $result = parent::restore();
        $this->clearCachedRoles();
        return $result;
    }

    public function getStatusAttribute($value)
    {
        return $value == 1?'active':'in-active';
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = $value == 'active'?1:0;
    }


    //activity tracking
    public static function resolveId()
    {
        return Auth::check() ? Auth::user()->getAuthIdentifier() : null;
    }

//api token
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }


}
