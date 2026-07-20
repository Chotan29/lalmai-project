"""
Deploy: ID card system + mark-ledger optional-twin fixes to live.
  1. git push origin master
  2. FTP upload all changed files (with per-file backup of the live copy)
  3. Ensure the "ID CARD" certificate template exists on live
  4. Run the optional-marks migration on live (dry-run first, then apply)
  5. view:clear
Run:   python deploy_idcard_markledger.py
Output -> deploy_idcard_markledger.log
"""
from ftplib import FTP, error_perm
import os, subprocess, sys, urllib.request, ssl
from datetime import datetime

FTP_HOST = "cs3001.webhostbox.net"
FTP_USER = "lalma87b"
FTP_PASS = "Ait@9423~"

LOCAL_BASE = os.path.dirname(os.path.abspath(__file__))
REMOTE_BASE = "/ims.lalmaigc.edu.bd"
SITE = "https://ims.lalmaigc.edu.bd"
KEY = "lgc7524x"

FILES = [
    "app/Http/Controllers/Examination/ExamMarkLedgerController.php",
    "app/Http/Controllers/PrintOut/CertificatePrintController.php",
    "app/Http/Controllers/Student/StudentController.php",
    "app/Http/Controllers/VerificationController.php",
    "app/Traits/CertificateScope.php",
    "public/images/idcard/college_logo.jpg",
    "public/images/idcard/govt_monogram.png",
    "public/images/idcard/principal_sign.png",
    "resources/views/examination/mark-ledger/add.blade.php",
    "resources/views/examination/mark-ledger/includes/student_tr.blade.php",
    "resources/views/examination/mark-ledger/includes/student_tr_rows.blade.php",
    "resources/views/print/certificate/id-card.blade.php",
    "resources/views/verification/id-card.blade.php",
    "routes/web.php",
    "scripts/migrate_optional_marks.php",
]

HELPER_REMOTE = "public/__idc_deploy.php"
HELPER_CODE = r"""<?php
if (($_GET['key'] ?? '') !== 'lgc7524x') { http_response_code(403); exit('Forbidden'); }
$base = is_dir(__DIR__.'/vendor') ? __DIR__ : dirname(__DIR__);
require $base.'/vendor/autoload.php';
$app = require $base.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
header('Content-Type: text/plain');
$t = \App\Models\CertificateTemplate::where('certificate', 'ID CARD')->first();
if (!$t) {
    $t = \App\Models\CertificateTemplate::create([
        'certificate' => 'ID CARD',
        'template' => '<p>Student ID Card (designed layout - content generated automatically)</p>',
        'student_photo' => 1, 'print_institute_head' => 0, 'background_status' => 0,
        'public_verify' => 0, 'status' => 'active', 'created_by' => 1,
    ]);
    echo "ID CARD template CREATED (id ".$t->id.")\n";
} else {
    echo "ID CARD template exists (id ".$t->id.")\n";
}
\Illuminate\Support\Facades\Artisan::call('view:clear');
echo "view:clear OK\n";

/* Optional-marks migration: move Optional-mapped students' ledger rows from the
   main subject schedule to the Optional twin schedule. apply=yes to write. */
if (($_GET['migrate'] ?? '') === 'yes') {
    $apply = ($_GET['apply'] ?? '') === 'yes';
    echo "\n=== MIGRATION ".($apply ? "APPLY" : "DRY RUN")." ===\n";
    $norm = function ($c) { return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $c)); };
    $moved = 0;
    $optionals = \App\Models\Subject::where('sub_type', 'like', '%option%')->get(['id','code','title']);
    foreach ($optionals as $opt) {
        $nc = $norm($opt->code);
        if (strpos($nc, 'O') !== 0) continue;
        $mainCode = substr($nc, 1);
        if ($mainCode === '') continue;
        $main = \App\Models\Subject::whereRaw("UPPER(REPLACE(REPLACE(REPLACE(code,'-',''),' ',''),'_','')) = ?", [$mainCode])
            ->where('id','!=',$opt->id)
            ->where(function($q){ $q->whereNull('sub_type')->orWhere('sub_type','not like','%option%'); })
            ->first(['id','code','title']);
        if (!$main) continue;
        $optStudentIds = \App\Models\StudentSubject::where('subjects_id',$opt->id)->pluck('students_id')->all();
        if (!$optStudentIds) continue;
        foreach (\App\Models\ExamSchedule::where('subjects_id',$main->id)->get() as $ms) {
            $rows = \App\Models\ExamMarkLedger::where('exam_schedule_id',$ms->id)
                ->whereIn('students_id',$optStudentIds)->get(['id','students_id']);
            if ($rows->isEmpty()) continue;
            $os = \App\Models\ExamSchedule::where([
                ['years_id','=',$ms->years_id],['months_id','=',$ms->months_id],
                ['exams_id','=',$ms->exams_id],['faculty_id','=',$ms->faculty_id],
                ['semesters_id','=',$ms->semesters_id],['subjects_id','=',$opt->id],
            ])->first();
            echo sprintf("%s [%s] -> %s [%s] %s : %d row(s)\n", trim($main->title), $main->code,
                trim($opt->title), $opt->code, $os ? 'sched#'.$os->id : 'MISSING(will create)', $rows->count());
            if (!$apply) { $moved += $rows->count(); continue; }
            if (!$os) {
                $os = \App\Models\ExamSchedule::create([
                    'years_id'=>$ms->years_id,'months_id'=>$ms->months_id,'exams_id'=>$ms->exams_id,
                    'faculty_id'=>$ms->faculty_id,'semesters_id'=>$ms->semesters_id,'subjects_id'=>$opt->id,
                    'exam_date'=>$ms->exam_date,'start_time'=>$ms->start_time,'end_time'=>$ms->end_time,
                    'full_mark_theory'=>$ms->full_mark_theory,'pass_mark_theory'=>$ms->pass_mark_theory,
                    'full_mark_practical'=>$ms->full_mark_practical,'pass_mark_practical'=>$ms->pass_mark_practical,
                    'status'=>$ms->status ?? 'active','created_by'=>1,
                ]);
            }
            foreach ($rows as $rw) {
                $dup = \App\Models\ExamMarkLedger::where('exam_schedule_id',$os->id)->where('students_id',$rw->students_id)->exists();
                if ($dup) { \App\Models\ExamMarkLedger::where('id',$rw->id)->delete(); }
                else { \App\Models\ExamMarkLedger::where('id',$rw->id)->update(['exam_schedule_id'=>$os->id]); $moved++; }
            }
        }
    }
    echo ($apply ? "MOVED: " : "WOULD MOVE: ").$moved." row(s)\n";
}

if (($_GET['cleanup'] ?? '') === 'yes') {
    echo @unlink(__FILE__) ? "self-deleted\n" : "self-delete FAILED\n";
}
"""

LOG = open(os.path.join(LOCAL_BASE, "deploy_idcard_markledger.log"), "w", encoding="utf-8")
def log(m=""):
    print(m); LOG.write(str(m)+"\n"); LOG.flush()

def connect():
    f = FTP(FTP_HOST, timeout=120); f.login(FTP_USER, FTP_PASS); f.set_pasv(True); return f

def ensure_dir(f, remote):
    parts = remote.split("/")[1:-1]; f.cwd("/")
    for p in parts:
        try: f.cwd(p)
        except error_perm: f.mkd(p); f.cwd(p)

def upload(f, rel, backup_dir):
    local = os.path.join(LOCAL_BASE, rel.replace("/", os.sep))
    remote = REMOTE_BASE + "/" + rel
    try:
        bp = os.path.join(backup_dir, rel.replace("/", "__"))
        with open(bp, "wb") as fh:
            f.cwd("/"); f.retrbinary("RETR " + remote.lstrip("/"), fh.write)
        log("  backup: " + rel)
    except error_perm:
        log("  new (no remote yet): " + rel)
    ensure_dir(f, remote)
    with open(local, "rb") as fh:
        f.cwd("/"); f.storbinary("STOR " + remote.lstrip("/"), fh)
    log("UPLOADED: %s (%d bytes)" % (rel, os.path.getsize(local)))

def http_get(url, timeout=180):
    ctx = ssl.create_default_context(); ctx.check_hostname = False; ctx.verify_mode = ssl.CERT_NONE
    req = urllib.request.Request(url, headers={"User-Agent": "deploy"})
    with urllib.request.urlopen(req, timeout=timeout, context=ctx) as r:
        return r.status, r.read().decode("utf-8", "replace")

def main():
    log("Deploy started: " + datetime.now().strftime("%Y-%m-%d %H:%M:%S"))

    log("\n===== 1) git push =====")
    r = subprocess.run(["git", "push", "origin", "master"], cwd=LOCAL_BASE, capture_output=True, text=True)
    log((r.stdout or "") + (r.stderr or ""))
    log("git push: " + ("OK" if r.returncode == 0 else "FAILED (continuing with FTP)"))

    log("\n===== 2) FTP upload =====")
    backup_dir = os.path.join(LOCAL_BASE, "deploy", "backup_" + datetime.now().strftime("%Y%m%d_%H%M%S"))
    os.makedirs(backup_dir, exist_ok=True)
    f = connect(); log("Connected: " + f.getwelcome()[:50])
    for rel in FILES:
        if not os.path.exists(os.path.join(LOCAL_BASE, rel.replace("/", os.sep))):
            log("MISSING LOCAL: " + rel); continue
        for attempt in range(1, 4):
            try: upload(f, rel, backup_dir); break
            except Exception as e:
                log("  attempt %d failed: %s" % (attempt, e))
                try: f.quit()
                except Exception: pass
                f = connect()
        else:
            log("GIVE UP: " + rel)
    log("Backups in: " + backup_dir)

    log("\n===== 3) ID CARD template + view:clear =====")
    hp = os.path.join(LOCAL_BASE, "__idc_deploy.php")
    with open(hp, "w", encoding="utf-8") as fh: fh.write(HELPER_CODE)
    with open(hp, "rb") as fh:
        f.cwd("/"); f.storbinary("STOR " + (REMOTE_BASE + "/" + HELPER_REMOTE).lstrip("/"), fh)
    try:
        st, body = http_get(SITE + "/__idc_deploy.php?key=" + KEY)
        log("helper (%d):\n%s" % (st, body.strip()))
    except Exception as e:
        log("helper failed: %s" % e)

    log("\n===== 4) optional-marks migration (DRY RUN) =====")
    try:
        st, body = http_get(SITE + "/__idc_deploy.php?key=" + KEY + "&migrate=yes")
        log("migrate dry-run (%d):\n%s" % (st, body.strip()))
    except Exception as e:
        log("migrate dry-run failed: %s" % e)

    log("\n===== 5) optional-marks migration (APPLY) =====")
    try:
        st, body = http_get(SITE + "/__idc_deploy.php?key=" + KEY + "&migrate=yes&apply=yes")
        log("migrate apply (%d):\n%s" % (st, body.strip()))
    except Exception as e:
        log("migrate apply failed: %s" % e)

    log("\n===== 6) helper cleanup =====")
    try:
        st, body = http_get(SITE + "/__idc_deploy.php?key=" + KEY + "&cleanup=yes")
        log(body.strip())
    except Exception as e:
        log("cleanup failed: %s" % e)
    try: f.quit()
    except Exception: pass

    log("\n===== RESULT =====")
    log("DONE. Test: Student list -> Print Certificate -> ID CARD on live.")
    log("Reminder: Print with Margins=None and Background graphics ON.")

if __name__ == "__main__":
    try: main()
    except Exception as e:
        log("FATAL: %s" % e)
    LOG.close()
