<?php

/*
 * Attendance devices.
 *
 * One entry per brand. Everything that differs between manufacturers lives here or in that
 * brand's driver - the callback paths it posts to, the class that understands its json. The rest
 * of the application never learns which brand is on the wall.
 *
 * To add another company later: write its driver, add a block here, add its route group. Nothing
 * in App\Services\Attendance changes.
 */

return [

    /* Used when nothing says otherwise - the brand the college runs today. */
    'default' => env('DEVICE_VENDOR', 'tipsoi'),

    'vendors' => [

        'tipsoi' => [

            'label'  => 'Tipsoi FastFace',
            'driver' => App\Services\Tipsoi\TipsoiDriver::class,

            /*
             * What to write in attendances.source. That column is an enum -
             * manual, usb_scanner, qr_webcam, barcode_webcam, tipsoi_api, tipsoi_sdk - and MySQL
             * quietly stores an empty string for anything not on the list rather than complaining.
             * tipsoi_sdk is right here: the device is speaking its own SDK protocol straight to
             * us, not coming through the vendor's cloud api.
             *
             * A second brand will need its own value added to the enum by migration before it
             * can be listed here.
             */
            'attendance_source' => 'tipsoi_sdk',

            /*
             * The five addresses the device is told to call, from the vendor's own demo
             * (Fast_Face_python_demo/configure_callbacks.py). Their cloud uses exactly these
             * paths under api-inovace360.com, which is why pointing the device at our host
             * instead is all it takes to leave their cloud behind.
             */
            'callback_base' => '/api/face/uface5/v1',
            'callbacks' => [
                'heartbeat'   => ['setter' => '/setDeviceHeartBeat',                 'param' => 'url',         'path' => ''],
                'recognition' => ['setter' => '/setIdentifyCallBack',                'param' => 'callbackUrl', 'path' => '/recog',       'extra' => ['base64Enable' => 2]],
                'imgReg'      => ['setter' => '/setImgRegCallBack',                  'param' => 'url',         'path' => '/img-reg'],
                'getTask'     => ['setter' => '/setTaskInterfaceAddress',            'param' => 'url',         'path' => '/get-task'],
                'taskResult'  => ['setter' => '/setTaskProcessingResultsAddress',    'param' => 'url',         'path' => '/task-result'],
            ],

            /*
             * Reaching the device directly, over the local network. Only needed to write those
             * addresses into it once, and to pull records back if a push is ever missed.
             */
            'sdk' => [
                'port'     => (int) env('TIPSOI_SDK_DEFAULT_PORT', 8090),
                'password' => env('TIPSOI_SDK_DEVICE_PASSWORD', '123456'),
                'timeout'  => (int) env('TIPSOI_SDK_TIMEOUT', 8),
            ],
        ],

    ],

    /*
     * A second punch from the same person inside this many seconds is the same arrival - somebody
     * standing in front of the camera for a moment, not a second event. Ignored rather than
     * written, so one student cannot produce twenty rows and twenty text messages.
     */
    'punch_debounce_seconds' => (int) env('DEVICE_PUNCH_DEBOUNCE', 120),

    /*
     * Where the device should fetch student photos from.
     *
     * The device downloads the image itself, so this address has to work from where the device
     * is standing - not from the office and not from the server. Left empty it falls back to
     * APP_URL, which is right on live; on a development machine APP_URL is often a tunnel the
     * device cannot resolve, so it is set to the plain LAN address instead.
     */
    'photo_base_url' => env('DEVICE_PHOTO_BASE_URL', ''),

    /*
     * The size of photo the device is willing to download.
     *
     * The photos on file are portraits from a phone or a studio - a third of them are over 1080
     * pixels tall and a quarter are over half a megabyte. The device refuses anything taller than
     * 1080 outright, and on the college wifi it gives up on the big ones halfway through:
     * LAN_EXP-4044, "File download timeout". Both failures look identical from the screen - the
     * student is simply not on the device.
     *
     * So a smaller copy is made for the device and the original is left alone, because the
     * original is what prints on the ID card. 640 pixels is far more than face recognition needs
     * and lands around 50 KB, which crosses the wifi without complaint.
     */
    'photo' => [
        'max_height' => (int) env('DEVICE_PHOTO_MAX_HEIGHT', 640),
        'quality'    => (int) env('DEVICE_PHOTO_QUALITY', 85),

        /* Where the smaller copies live, under public/. Served as plain files: the device asks
           for 1,200 of them, and a static file costs the server nothing. */
        'cache_dir'  => env('DEVICE_PHOTO_CACHE_DIR', 'images/devicePhoto'),
    ],

    /*
     * A device reports in every minute. After this much silence it is treated as offline on the
     * device screen - long enough to ride out a slow network, short enough that a reader which
     * died this morning is not still shown as healthy this afternoon.
     */
    'offline_after_minutes' => (int) env('DEVICE_OFFLINE_AFTER', 5),

    /*
     * How long after check-in a further punch counts as leaving rather than arriving.
     */
    'checkout_after_minutes' => (int) env('DEVICE_CHECKOUT_AFTER', 180),

];
