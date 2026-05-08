<?php
// app/Models/DeviceTask.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DeviceTask extends Model {
    protected $fillable = ['device_ip','action','payload','status','last_error','sent_at','done_at'];
    protected $casts = ['payload'=>'array','sent_at'=>'datetime','done_at'=>'datetime'];
}
