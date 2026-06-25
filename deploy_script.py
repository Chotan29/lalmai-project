from ftplib import FTP
import os

# FTP credentials
ftp_host = "cs3001.webhostbox.net"
ftp_user = "lalma87b"
ftp_pass = "Ait@9423~"

# Local file path
local_file = "public/deploy_migrate_once.php"

# Remote path
remote_path = "/ims.lalmaigc.edu.bd/public/deploy_migrate_once.php"

try:
    # Connect to FTP
    ftp = FTP(ftp_host)
    ftp.login(ftp_user, ftp_pass)
    print(f"✓ Connected to FTP: {ftp_host}")
    
    # Upload file
    with open(local_file, 'rb') as f:
        ftp.storbinary(f'STOR {remote_path}', f)
    print(f"✓ File uploaded successfully to {remote_path}")
    
    ftp.quit()
    print("✓ FTP connection closed")
    
except Exception as e:
    print(f"✗ Error: {e}")
