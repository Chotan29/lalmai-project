# হাজিরা ডিভাইস — কোড কোথায় বসবে

Lalmai Govt. College IMS · ১৯ অগাস্ট ২০২৬

লক্ষ্য দুটো:

1. কোম্পানির cloud ছাড়া, ডিভাইস সরাসরি আমাদের অ্যাপের সাথে কথা বলবে
2. পরে অন্য কোম্পানির ডিভাইস এলে **শুধু একটা driver লিখলেই চলবে**, বাকি কিছু ছোঁয়া লাগবে না

---

## ১. মূল নিয়ম: ডিভাইস আমাদের ফোন করে

ডিভাইস কলেজের wifi-র ভেতরে, router-এর পেছনে। বাইরে থেকে ওর কাছে পৌঁছানো যায় না, কিন্তু
ও বাইরে যেতে পারে। তাই **প্রতিটা যোগাযোগ ডিভাইস শুরু করে** — আমরা কখনো ওকে ডাকি না।

এর ফলে শেয়ার্ড হোস্টিং যথেষ্ট: router-এ port খুলতে হয় না, স্থির IP লাগে না, কলেজে
আলাদা মেশিনও লাগে না।

---

## ২. পাঁচটা ঠিকানা (Fast_Face_python_demo থেকে পাওয়া)

`configure_callbacks.py` লাইন ১৯-২৮ অনুযায়ী ডিভাইসে পাঁচটা আলাদা ঠিকানা বসাতে হয়:

| ডিভাইসের সেটার | আমাদের path | প্যারামিটার |
|---|---|---|
| `/setDeviceHeartBeat` | `/api/face/uface5/v1` | `url` |
| `/setIdentifyCallBack` | `/api/face/uface5/v1/recog` | `callbackUrl` + `base64Enable:2` |
| `/setImgRegCallBack` | `/api/face/uface5/v1/img-reg` | `url` |
| `/setTaskInterfaceAddress` | `/api/face/uface5/v1/get-task` | `url` |
| `/setTaskProcessingResultsAddress` | `/api/face/uface5/v1/task-result` | `url` |

ডেমোর `DEFAULT_TUNNEL = "https://api-inovace360.com"` — **কোম্পানির cloud ঠিক এই path
গুলোই ব্যবহার করে**। তাই ডিভাইসে শুধু হোস্টনামটা বদলে `https://ims.lalmaigc.edu.bd`
বসালেই আমাদের অ্যাপ cloud-এর জায়গা নেয়।

---

## ৩. ফাইল কোথায় বসবে

আপনার এখনকার সাজানো অনুসরণ করে — `Services/Tipsoi/`, `Http/Controllers/API/`,
`Jobs/AttendanceJobs/`, `Repositories/`, `Providers/`।

```
app/
  Services/
    Devices/                       ← নতুন, vendor-নিরপেক্ষ
      DeviceDriver.php               interface — প্রতিটা কোম্পানিকে এটাই মানতে হবে
      DeviceDriverManager.php        vendor key দেখে driver বেছে দেয়
      PunchData.php                  একটা পাঞ্চের সাধারণ চেহারা
      EnrolmentTask.php              একটা কাজের সাধারণ চেহারা

    Tipsoi/                        ← আছে, এখানেই বাড়বে
      TipsoiDriver.php               নতুন — implements DeviceDriver
      SdkClient.php                  আছে
      CsApiService.php               আছে

    Attendance/                    ← নতুন, vendor-নিরপেক্ষ কেন্দ্র
      PunchIngestor.php              PunchData → tipsoi_attendance_logs → attendances
      DeviceRegistry.php             heartbeat → tipsoi_devices (connected, last_seen)
      EnrolmentQueue.php             EnrolmentTask → device_tasks

  Http/Controllers/API/
    DeviceCallbackController.php   ← নতুন base, সাধারণ ধাপগুলো এখানে
    TipsoiLanController.php        ← আছে; পাতলা হয়ে base-কে extend করবে

  Models/
    TipsoiDevice.php               আছে
    TipsoiAttendanceLog.php        আছে
    DeviceTask.php                 আছে (টেবিল নতুন বানানো হয়েছে)
    Attendance.php                 আছে

  Jobs/AttendanceJobs/
    SendAttendanceNotification.php আছে — হাত দেওয়া হবে না

  Providers/
    TipsoiServiceProvider.php      আছে — এখানে DeviceDriverManager bind হবে

config/
  devices.php                      ← নতুন, কোন vendor কোন driver
  tipsoi.php                       তৈরি হয়েছে, Tipsoi-র নিজস্ব সেটিং

routes/
  api.php                          পাঁচটা path-এর রুট গ্রুপ
```

---

## ৪. ভেতরে কী হবে

### ক) পাঞ্চ আসা

```
ডিভাইস → POST /api/face/uface5/v1/recog
              ↓
TipsoiLanController::recognition()        ← পাতলা, শুধু driver ডাকে
              ↓
TipsoiDriver::parsePunch($request)        ← Tipsoi-র json → PunchData
              ↓
PunchIngestor::ingest(PunchData $punch)   ← এখান থেকে সব কোম্পানির জন্য এক
   ├─ tipsoi_attendance_logs-এ কাঁচা রেকর্ড
   ├─ personId → students.id মেলানো
   ├─ attendances সারি (একই দিনে দ্বিতীয় পাঞ্চ হলে check_out)
   └─ SendAttendanceNotification::dispatch()  → SMS
```

`PunchData`-তে যা থাকবে: `deviceKey`, `personId`, `at` (Carbon), `isPass`, `raw`।
অন্য কোম্পানির ডিভাইস অন্য নামে ফিল্ড পাঠালে **শুধু ওই কোম্পানির driver বদলাবে**।

### খ) এনরোলমেন্ট যাওয়া

```
Admin screen → EnrolmentQueue::queueStudent($student)
                     ↓
              device_tasks সারি (queued)
                     ↓
ডিভাইস → POST /api/face/uface5/v1  (heartbeat, প্রতি ৬০ সেকেন্ড)
         ← {"result": true}   কাজ থাকলে
ডিভাইস → POST /api/face/uface5/v1/get-task
         ← TipsoiDriver::formatTask($task)   ← একটাই task object
ডিভাইস → POST /api/face/uface5/v1/task-result
         ← {"result": true}   আরও কাজ থাকলে, নইলে false
```

Tipsoi-র task-এর চেহারা (ডেমো থেকে):

```json
{ "taskNo": "create-a1b2c3d4",
  "interfaceName": "person/create",
  "result": true,
  "person": { "id": "4786", "name": "KOTHA RANI SINGHA", "facePermission": 2 } }
```

ছবির জন্য `face/createByUrl` — **ডিভাইস নিজেই ছবি নামিয়ে নেয়**:

```json
{ "taskNo": "photo-...", "interfaceName": "face/createByUrl",
  "personId": "4786",
  "imgUrl": "https://ims.lalmaigc.edu.bd/images/studentProfile/6784.jpeg" }
```

base64 করার দরকার নেই, ২ MB সীমা নিয়েও ভাবতে হবে না।

---

## ৫. অন্য কোম্পানির ডিভাইস যোগ করা

চারটে জিনিস, আর কিছু না:

1. `app/Services/<Vendor>/<Vendor>Driver.php` — `DeviceDriver` implement করবে
2. `app/Http/Controllers/API/<Vendor>LanController.php` — কয়েক লাইন, base extend করবে
3. `routes/api.php`-এ ওই কোম্পানির path-এর একটা গ্রুপ
4. `config/devices.php`-এ একটা ব্লক

**যা ছোঁয়া লাগবে না:** `PunchIngestor`, `attendances`, `SendAttendanceNotification`,
SMS, রিপোর্ট, ড্যাশবোর্ড — একটা অক্ষরও না।

### interface যা মানতে হবে

```php
interface DeviceDriver
{
    public function vendor(): string;

    /* ডিভাইসের callback → আমাদের সাধারণ চেহারা */
    public function parsePunch(Request $request): ?PunchData;
    public function parseHeartbeat(Request $request): array;

    /* আমাদের কাজ → ডিভাইসের নিজের ফরম্যাট */
    public function formatTask(DeviceTask $task): array;

    /* ডিভাইস কী উত্তর আশা করে */
    public function heartbeatReply(bool $hasTasks): array;
    public function taskResultReply(bool $hasMore): array;

    /* ডিভাইসে ঠিকানা বসানো (একবারের কাজ, LAN থেকে) */
    public function callbackPaths(): array;
}
```

Tipsoi-র `heartbeatReply()` ফেরত দেবে `['result' => $hasTasks]`। অন্য কোম্পানি অন্য
কিছু চাইলে সেটা ওদের driver-এ থাকবে, কেন্দ্রে নয়।

---

## ৬. এখন পর্যন্ত যা হয়েছে

| কাজ | অবস্থা |
|---|---|
| `device_tasks` টেবিল | মাইগ্রেশন লেখা, লাইভে আপলোড — টেবিল বানানো বাকি |
| `TipsoiLanController` | heartbeat ডিভাইস লিখে রাখে, কাঁচা callback লগ করে |
| `config/tipsoi.php` | তৈরি |
| ডিভাইসে পৌঁছানো | ✅ `192.168.0.100:8090`, পাসওয়ার্ড `123456` |
| পাঞ্চের ফরম্যাট | ✅ ডিভাইস থেকে পড়া হয়েছে |

**তবে এখনকার কন্ট্রোলারের path আর task ফরম্যাট ভুল** — ডেমো দেখার আগে অনুমান করে লেখা।
উপরের নকশা অনুযায়ী আবার লিখতে হবে।

---

## ৭. যে ঝুঁকিগুলো মনে রাখতে হবে

- **cron ছাড়া SMS যাবে না।** `QUEUE_CONNECTION=database`, আর queue চালায় scheduler।
  cPanel-এ `* * * * * php artisan schedule:run` আছে কি না নিশ্চিত করতে হবে।
- **হারানো পাঞ্চ ফেরানো যায় না।** ডিভাইস একবার পাঠায়; সার্ভার সাড়া না দিলে ওই পাঞ্চ
  হারায়। দিনে একবার `newFindRecords` দিয়ে মিলিয়ে নেওয়ার ব্যবস্থা রাখা উচিত — সেটা
  LAN থেকে চালাতে হবে।
- **ডেটাবেস কানেকশন ২৫।** সকালে অল্প সময়ে হাজার পাঞ্চ এলে চাপ পড়বে।
- **ডিভাইসের ঘড়ি ~৪ ঘণ্টা এগিয়ে আছে** (১৮ অগাস্ট মাপা)। ঠিক না করলে হাজিরা ভুল
  সময়ে, এমনকি ভুল দিনে পড়বে।
- **ডিভাইস কতগুলো মুখ ধরে** — SDK-তে লেখা নেই, কোম্পানিকে জিজ্ঞেস করতে হবে।
