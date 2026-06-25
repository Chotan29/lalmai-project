"""
Deploy attendance SMS fix to live server via FTP (passive mode).
"""
from ftplib import FTP
import os, requests, time

FTP_HOST    = "cs3001.webhostbox.net"
FTP_USER    = "lalma87b"
FTP_PASS    = "Ait@9423~"
REMOTE_BASE = "/ims.lalmaigc.edu.bd"
LOCAL_BASE  = os.path.dirname(os.path.abspath(__file__))

FILES = [
    "app/Http/Controllers/Attendance/LiveAttendanceController.php",
    "app/Jobs/AttendanceJobs/SendAttendanceNotification.php",
    "app/Observers/AttendanceObserver.php",
    "app/Traits/SmsEmailScope.php",
    "app/Traits/SmsGateway/SslWirelessSMS.php",
    "app/Services/InovaceApi.php",
    "app/Http/Controllers/Setting/SmsSettingController.php",
    "app/Http/Controllers/Web/Admin/Info/SmsEmailController.php",
    "resources/views/attendance/live/students.blade.php",
    "resources/views/setting/sms/index.blade.php",
    "resources/views/web/admin/info/smsemail/individual/includes/form.blade.php",
    "resources/views/web/admin/info/smsemail/individual/index.blade.php",
]

def upload_file(ftp, local_path, remote_path):
    with open(local_path, "rb") as f:
        ftp.storbinary(f"STOR {remote_path}", f)

def connect():
    ftp = FTP()
    ftp.connect(FTP_HOST, 21, timeout=30)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    return ftp

def main():
    print(f"Connecting (PASV) to {FTP_HOST}...")
    ftp = connect()
    print("Connected.\n")

    ok, fail = 0, 0
    for rel in FILES:
        local  = os.path.join(LOCAL_BASE, rel.replace("/", os.sep))
        remote = f"{REMOTE_BASE}/{rel}"

        if not os.path.exists(local):
            print(f"  SKIP (not found locally): {rel}")
            continue

        try:
            upload_file(ftp, local, remote)
            size = ftp.size(remote)
            print(f"  OK  {rel}  ({size:,}b)")
            ok += 1
        except Exception as e:
            print(f"  ERR {rel}: {e}")
            try:
                ftp.quit()
            except: pass
            try:
                time.sleep(2)
                ftp = connect()
                upload_file(ftp, local, remote)
                size = ftp.size(remote)
                print(f"  OK  (retry) {rel}  ({size:,}b)")
                ok += 1
            except Exception as e2:
                print(f"  FAIL {rel}: {e2}")
                fail += 1

    print(f"\nUploaded: {ok}  Failed: {fail}")

    print("\nClearing caches...")
    cache_php = '<?php $b=dirname(__FILE__);exec("php $b/artisan cache:clear 2>&1",$o);exec("php $b/artisan config:cache 2>&1",$o);exec("php $b/artisan view:clear 2>&1",$o);exec("php $b/artisan route:cache 2>&1",$o);echo implode("\\n",$o)."\\nDONE";'
    with open("tmp_run_live.php", "w") as f:
        f.write(cache_php)

    try:
        upload_file(ftp, "tmp_run_live.php", f"{REMOTE_BASE}/public/a1.php")
    except:
        ftp = connect()
        upload_file(ftp, "tmp_run_live.php", f"{REMOTE_BASE}/public/a1.php")

    r = requests.get("https://ims.lalmaigc.edu.bd/a1.php",
                     headers={"User-Agent":"Mozilla/5.0"}, timeout=60)
    print(r.text)

    try:
        ftp.delete(f"{REMOTE_BASE}/public/a1.php")
    except: pass
    try:
        ftp.quit()
    except: pass
    print("\nDone!")

if __name__ == "__main__":
    main()

FILES = [
    "app/Http/Controllers/Attendance/LiveAttendanceController.php",
    "app/Jobs/AttendanceJobs/SendAttendanceNotification.php",
    "app/Observers/AttendanceObserver.php",
    "app/Traits/SmsEmailScope.php",
    "app/Traits/SmsGateway/SslWirelessSMS.php",
    "app/Services/InovaceApi.php",
    "app/Http/Controllers/Setting/SmsSettingController.php",
    "app/Http/Controllers/Web/Admin/Info/SmsEmailController.php",
    "resources/views/attendance/live/students.blade.php",
    "resources/views/setting/sms/index.blade.php",
    "resources/views/web/admin/info/smsemail/individual/includes/form.blade.php",
    "resources/views/web/admin/info/smsemail/individual/index.blade.php",
]

def ensure_remote_dir(ftp, remote_dir):
    parts = remote_dir.strip("/").split("/")
    path = ""
    for p in parts:
        path += "/" + p
        try:
            ftp.cwd(path)
        except:
            try:
                ftp.mkd(path)
            except:
                pass

def main():
    print(f"Connecting to {FTP_HOST}...")
    ftp = FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    print("✓ FTP connected\n")

    ok = 0
    fail = 0
    for rel in FILES:
        local  = os.path.join(LOCAL_BASE, rel.replace("/", os.sep))
        remote = f"{REMOTE_BASE}/{rel}"
        remote_dir = "/".join(remote.split("/")[:-1])

        if not os.path.exists(local):
            print(f"  ✗ LOCAL MISSING: {rel}")
            fail += 1
            continue

        try:
            ensure_remote_dir(ftp, remote_dir)
            with open(local, "rb") as f:
                ftp.storbinary(f"STOR {remote}", f)
            size = ftp.size(remote)
            print(f"  ✓ {rel}  ({size:,} bytes)")
            ok += 1
        except Exception as e:
            print(f"  ✗ FAILED {rel}: {e}")
            fail += 1

    ftp.quit()
    print(f"\n{'='*50}")
    print(f"Uploaded: {ok}  Failed: {fail}")

    # Clear caches via remote PHP execution
    print("\nClearing caches on live server...")
    cache_php = """<?php
$base = dirname(__FILE__);
$out = [];
exec('php ' . $base . '/artisan cache:clear  2>&1', $out);
exec('php ' . $base . '/artisan config:cache 2>&1', $out);
exec('php ' . $base . '/artisan view:clear   2>&1', $out);
exec('php ' . $base . '/artisan route:cache  2>&1', $out);
echo implode("\\n", $out);
echo "\\nDONE";
?>"""
    with open("tmp_run_live.php", "w") as f:
        f.write(cache_php)

    ftp2 = FTP(FTP_HOST)
    ftp2.login(FTP_USER, FTP_PASS)
    with open("tmp_run_live.php", "rb") as f:
        ftp2.storbinary(f"STOR {REMOTE_BASE}/public/a1.php", f)
    ftp2.quit()

    ua = "Mozilla/5.0 (Windows NT 10.0; Win64; x64)"
    r  = requests.get("https://ims.lalmaigc.edu.bd/a1.php",
                      headers={"User-Agent": ua}, timeout=60)
    print(r.text)

    ftp3 = FTP(FTP_HOST)
    ftp3.login(FTP_USER, FTP_PASS)
    try:
        ftp3.delete(f"{REMOTE_BASE}/public/a1.php")
        print("Temp file deleted.")
    except:
        pass
    ftp3.quit()
    print("\n✅ Deployment complete!")

if __name__ == "__main__":
    main()
