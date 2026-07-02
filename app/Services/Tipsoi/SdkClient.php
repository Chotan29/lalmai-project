<?php
// app/Services/Tipsoi/SdkClient.php
namespace App\Services\Tipsoi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class SdkClient
{
    protected $pass;
    protected $port;
    protected $timeout;

    public function __construct(){
        $this->pass    = config('tipsoi.sdk.device_password');
        $this->port    = (int) config('tipsoi.sdk.port', 8090);
        $this->timeout = (int) config('tipsoi.sdk.timeout', 8);
    }

    protected function clientFor(string $ip): Client {
        return new Client([
            'base_uri'    => "http://{$ip}:{$this->port}/",
            'timeout'     => $this->timeout,
            'http_errors' => false,
        ]);
    }

    protected function form(string $ip, string $path, array $body){
        $body['pass'] = $body['pass'] ?? $this->pass;
        try {
            $res = $this->clientFor($ip)->post($path, [
                'form_params' => $body,
                'headers' => ['Content-Type'=>'application/x-www-form-urlencoded'],
            ]);
            $json = json_decode($res->getBody(), true);
            return ['status'=>$res->getStatusCode(),'json'=>$json];
        } catch (GuzzleException $e){
            Log::error('SDK POST error', ['ip'=>$ip,'path'=>$path,'err'=>$e->getMessage()]);
            return ['status'=>0,'error'=>$e->getMessage()];
        }
    }

    protected function getQ(string $ip, string $path, array $q){
        $q['pass'] = $q['pass'] ?? $this->pass;
        try {
            $res = $this->clientFor($ip)->get($path, ['query'=>$q]);
            $json = json_decode($res->getBody(), true);
            return ['status'=>$res->getStatusCode(),'json'=>$json];
        } catch (GuzzleException $e){
            Log::error('SDK GET error', ['ip'=>$ip,'path'=>$path,'err'=>$e->getMessage()]);
            return ['status'=>0,'error'=>$e->getMessage()];
        }
    }

    // === Heartbeat callback address on device ===
    public function setDeviceHeartbeat(string $ip, string $callbackUrl = null, int $intervalSec = null){
        // POST /setDeviceHeartBeat (device calls platform every minute; we set 'url' here). :contentReference[oaicite:11]{index=11}
        $payload = [
            'url'     => $callbackUrl ?: config('tipsoi.callbacks.heartbeat'),
        ];
        if ($intervalSec !== null){ $payload['interval'] = $intervalSec; }
        return $this->form($ip, 'setDeviceHeartBeat', $payload);
    }

    // === Person ops ===
    public function personCreate(string $ip, array $person, array $faces = []){
        // POST /person/create — 'person' JSON + optional face1/face2/face3. :contentReference[oaicite:12]{index=12}
        $payload = ['person'=>json_encode($person)];
        foreach ($faces as $idx => $face){
            $payload['face'.($idx+1)] = json_encode($face);
        }
        return $this->form($ip, 'person/create', $payload);
    }

    public function faceUpdateBase64(string $ip, string $personId, string $faceId, string $imgBase64, bool $easy=false){
        // POST /face/update with personId, faceId, imgBase64. :contentReference[oaicite:13]{index=13}
        return $this->form($ip, 'face/update', [
            'personId'=>$personId,'faceId'=>$faceId,'imgBase64'=>$imgBase64,'isEasyWay'=>$easy ? 'true':'false'
        ]);
    }

    public function personDelete(string $ip, $personIds){
        // POST /person/delete (id may be '-1' for all). :contentReference[oaicite:14]{index=14}
        return $this->form($ip, 'person/delete', ['id'=>is_array($personIds)?implode(',',$personIds):$personIds]);
    }

    public function getPersonList(string $ip, $personId = -1, int $length=1000, int $index=0){
        // GET /person/find or /person/findByPage. :contentReference[oaicite:15]{index=15}
        return $this->getQ($ip, 'person/findByPage', ['personId'=>$personId,'length'=>$length,'index'=>$index]);
    }

    public function getRecognitionRecords(string $ip, $personId = -1, $start='0', $end='0', int $length=1000, int $model=-1, $order=null, $index=0){
        // GET /newFindRecords query. :contentReference[oaicite:16]{index=16}
        $q = compact('personId','start','end','length','model','index');
        if ($order !== null){ $q['order'] = $order; }
        return $this->getQ($ip, 'newFindRecords', $q);
    }

    public function setFingerRegCallback(string $ip, string $url){
        // POST /device/setFingerRegCallback. :contentReference[oaicite:17]{index=17}
        return $this->form($ip, 'device/setFingerRegCallback', ['url'=>$url]);
    }

    public function fingerRegist(string $ip, string $personId){
        // POST /face/fingerRegist to enter enroll mode. :contentReference[oaicite:18]{index=18}
        return $this->form($ip, 'face/fingerRegist', ['personId'=>$personId]);
    }
}
