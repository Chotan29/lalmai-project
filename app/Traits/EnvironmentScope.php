<?php
namespace App\Traits;


trait EnvironmentScope{


//https://devdojo.com/codebits/dynamically-change-env-file-in-laravel
    // private function setEnv($key, $value)
    // {
    //     $path = base_path('.env');
    //     $keyExist = env($key);
    //     $valueExist = getenv($key);

    //     if (file_exists($path)) {
    //         if(isset($keyExist) && $valueExist == ''){
    //             file_put_contents(app()->environmentFilePath(), str_replace(
    //                 $key . '=',
    //                 $key . '=' . $value,
    //                 file_get_contents(app()->environmentFilePath())
    //             ));
    //         }elseif($keyExist == '' && $valueExist == ''){
    //             file_put_contents($path, $key . '=' . $value.PHP_EOL , FILE_APPEND | LOCK_EX);
    //         }else{
    //             if($valueExist){
    //                 //dd($key . '='.env($key),$key . '=' . $value);
    //                 file_put_contents(app()->environmentFilePath(), str_replace(
    //                     $key . '='.env($key),
    //                     $key . '=' . $value,
    //                     file_get_contents(app()->environmentFilePath())
    //                 ));
    //             }else{

    //             }
    //         }

    //     }else{

    //     }

    //     /*file_put_contents(app()->environmentFilePath(), str_replace(
    //         $key . '='.env($key),
    //         $key . '=' . $value,
    //         file_get_contents(app()->environmentFilePath())
    //     ));*/
    // }

    private function setEnv($key, $value)
{
    $envPath = base_path('.env');
    if (!file_exists($envPath)) return;

    $env = file($envPath); // read as lines
    $keyFound = false;
    $quotedValue = '"' . trim($value) . '"';

    foreach ($env as $index => $line) {
        if (strpos($line, "{$key}=") === 0) {
            $env[$index] = "{$key}={$quotedValue}" . PHP_EOL;
            $keyFound = true;
            break;
        }
    }

    if (!$keyFound) {
        $env[] = "{$key}={$quotedValue}" . PHP_EOL;
    }

    file_put_contents($envPath, implode('', $env));
}


    /*$this->setEnvironmentValue('DEPLOY_SERVER', 'forge@122.11.244.10');*/

    /*public function setEnv1($envKey, $envValue)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        $oldValue = strtok($str, "{$envKey}=");

        if($oldValue) {
            $str = str_replace("{$envKey}={$oldValue}", "{$envKey}={$envValue}\n", $str);
        }else{
            $str = str_replace("{$envKey}={$oldValue}", "{$envKey}={$envValue}\n", $str);
        }

        $fp = fopen($envFile, 'w');
        fwrite($fp, $str);
        fclose($fp);
    }*/
}