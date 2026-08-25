<?php

/*
 * FastFace attendance device.
 *
 * This file was missing, which is why the device screen always showed nothing. Twelve places in
 * the code read config('tipsoi.*') - CsApiService for the device list, SdkClient for every device
 * call - and with no config file every one of them received null. A null base_url makes the HTTP
 * client point at nothing, so the device list came back empty and every device read as inactive,
 * however healthy the device actually was.
 *
 * The cloud credentials fall back to the INOVACE_* pair, because they are the same vendor and the
 * same endpoint (api-inovace360.com) and those are the values that are actually filled in on live.
 * TIPSOI_CS_API_TOKEN is still the string "replace-with-..." there, so reading it first and
 * falling back second is what keeps this working rather than the other way round.
 */

return [

    /* Which of the two base urls below to use. */
    'api_mode' => env('TIPSOI_API_MODE', 'production'),

    'base_url' => [
        'test'       => env('TIPSOI_BASE_URL_TEST', 'https://test.api-inovace360.com/api/v1'),
        'production' => env('TIPSOI_BASE_URL_PROD', 'https://api-inovace360.com/api/v1'),
    ],

    'token' => env('TIPSOI_API_TOKEN', env('INOVACE_API_TOKEN', '')),

    'default_device' => env('TIPSOI_DEFAULT_DEVICE'),

    /*
     * The vendor's cloud service. Only used to list devices and push people through their
     * platform - not needed at all when the device is reached directly over the network.
     */
    'cs' => [
        'base_url'  => env('TIPSOI_CS_BASE_URL', env('INOVACE_BASE_URL', 'https://api-inovace360.com/api/v1')),
        'api_token' => env('TIPSOI_CS_API_TOKEN', env('INOVACE_API_TOKEN', '')),
        'timeout'   => (int) env('TIPSOI_CS_TIMEOUT', 12),
    ],

    /*
     * Talking to the device itself, on the local network. Every interface in the FastFace SDK
     * lives at http://<device ip>:8090/ and is authenticated by a single device password.
     */
    'sdk' => [
        'device_password' => env('TIPSOI_SDK_DEVICE_PASSWORD', '123456'),
        'port'            => (int) env('TIPSOI_SDK_DEFAULT_PORT', 8090),
        'timeout'         => (int) env('TIPSOI_SDK_TIMEOUT', 8),
    ],

    /*
     * Where the device calls us. setDeviceHeartBeat writes this address into the device, and from
     * then on the device posts to it every minute. It must be reachable from the device, so a
     * public https address rather than localhost.
     */
    'callbacks' => [
        'heartbeat'  => env('TIPSOI_CB_HEARTBEAT', env('APP_URL') . '/api/tipsoi/lan/heartBeatCallback'),
        'tasks'      => env('TIPSOI_CB_TASKS', env('APP_URL') . '/api/tipsoi/lan/tasks'),
        'taskResult' => env('TIPSOI_CB_TASK_RESULT', env('APP_URL') . '/api/tipsoi/lan/task-result'),
        'fingerReg'  => env('TIPSOI_CB_FINGER_REG', env('APP_URL') . '/api/tipsoi/lan/finger-reg-callback'),
    ],

];
