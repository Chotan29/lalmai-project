from ftplib import FTP
import os

# FTP credentials
ftp_host = "cs3001.webhostbox.net"
ftp_user = "lalma87b"
ftp_pass = "Ait@9423~"
remote_root = "/ims.lalmaigc.edu.bd"

files_to_upload = [
    "app/Http/Controllers/Academic/SubjectController.php",
    "app/Models/Subject.php",
    "database/migrations/2026_05_19_120000_add_missing_foreign_keys_to_semester_subject_table.php",
    "database/migrations/2026_05_19_191237_add_foreign_keys_to_semester_subject_table.php",
    "database/migrations/2026_05_19_191244_create_deletion_logs_table.php",
    "database/migrations/2026_05_19_203000_fix_subject_fk_delete_rule_on_semester_subject_table.php"
]

def ensure_remote_dir(ftp, remote_path):
    parts = remote_path.strip('/').split('/')
    ftp.cwd('/')
    for part in parts[:-1]: # exclude the filename
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
        print(f"✓ {local_file} -> {remote_path}")
        success_count += 1
    
    ftp.quit()
    print(f"\nFinal Summary: {success_count}/{len(files_to_upload)} files uploaded successfully.")

except Exception as e:
    print(f"✗ Fatal Error: {e}")
