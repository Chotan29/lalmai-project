<?php
namespace App\Http\Controllers\Attendance\Device;

use App\Services\Devices\DeviceDriver;
use App\Services\Tipsoi\TipsoiDriver;
use Illuminate\Http\Request;

/**
 * Tipsoi FastFace callbacks.
 *
 * Deliberately almost empty. Everything the device and the application say to each other is in
 * DeviceCallbackController, because it is the same conversation whoever made the device; all
 * that changes between brands is the wording, and the wording lives in the driver.
 *
 * The next manufacturer gets a class exactly like this one, naming its own driver, and a route
 * group with its own paths. Nothing else moves.
 *
 * Routes: routes/api.php, prefix face/uface5/v1 - the same paths the vendor's own cloud serves,
 * which is what lets the device be pointed at us instead of at them.
 *
 * It sits here rather than in app/Http/Controllers/API/, and the reason cost an afternoon. That
 * folder is spelled API while every namespace inside it says Api. Windows opens one when asked
 * for the other and nobody notices; Linux does not, so on the live server psr-4 asked for
 * app/Http/Controllers/Api/TipsoiLanController.php, found nothing, and the endpoint answered 500
 * while every other page was fine. The older files in that folder still work only because
 * composer's optimised classmap was built while they existed and records their real path - a
 * file added afterwards has no such entry. A folder whose spelling matches its namespace needs
 * no classmap and no composer run on the server.
 */
class TipsoiLanController extends DeviceCallbackController
{
    /** @return DeviceDriver */
    protected function driver()
    {
        return app(TipsoiDriver::class);
    }

    /*
     * The old LAN endpoints, kept so anything already pointed at them keeps working.
     * heartBeatCallback / tasks / taskResult were the names before the vendor's demo showed what
     * the device actually calls.
     */
    public function heartBeatCallback(Request $r) { return $this->heartbeat($r); }
    public function tasks(Request $r)             { return $this->getTask($r); }
    public function fingerRegCallback(Request $r) { return $this->imgReg($r); }
}
