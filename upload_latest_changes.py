from ftplib import FTP
import os

# FTP credentials
ftp_host = "cs3001.webhostbox.net"
ftp_user = "lalma87b"
ftp_pass = "Ait@9423~"
remote_root = "/ims.lalmaigc.edu.bd"

files_to_upload = [
    "app/Http/Controllers/Examination/ExamController.php",
    "app/Http/Controllers/Student/StudentController.php",
    "app/Http/Controllers/UserStudent/HomeController.php",
    "resources/views/student/detail/includes/profile.blade.php",
    "resources/views/user-student/dashboard/includes/student-card.blade.php",
    "resources/views/user-student/dashboard/index.blade.php",
    "resources/views/user-student/detail/includes/profile.blade.php",
    "resources/views/user-student/layouts/includes/menu.blade.php",
    "resources/views/user-student/registration/register.blade.php",
]

def ensure_remote_dir(ftp, remote_path):
    parts = remote_path.strip('/').split('/')
    ftp.cwd('/')
    for part in parts[:-1]:
        try:
            ftp.cwd(part)
        except:
            ftp.mkd(part)
            ftp.cwd(part)

try:
    ftp = FTP(ftp_host)
    ftp.login(ftp_user, ftp_pass)
    print(f"✓ Connected to FTP: {ftp_host}")

    success_count = 0
    for local_file in files_to_upload:
        if not os.path.exists(local_file):
            print(f"✗ Local file not found: {local_file}")
            continue

        remote_path = os.path.join(remote_root, local_file).replace('\\', '/')
        ensure_remote_dir(ftp, remote_path)

        with open(local_file, 'rb') as f:
            ftp.storbinary(f"STOR {os.path.basename(remote_path)}", f)
        print(f"✓ {local_file}")
        success_count += 1

    ftp.quit()
    print(f"\nSummary: {success_count}/{len(files_to_upload)} files uploaded successfully.")

except Exception as e:
    print(f"✗ Error: {e}")
