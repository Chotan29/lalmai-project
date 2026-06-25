from ftplib import FTP
import os

# FTP credentials
ftp_host = "cs3001.webhostbox.net"
ftp_user = "lalma87b"
ftp_pass = "Ait@9423~"

# Local file path
local_file = "tmp_verify_acc3.php"

# Remote path
remote_path = "/ims.lalmaigc.edu.bd/public/x1.php"

try:
    # Connect to FTP
    ftp = FTP(ftp_host)
    ftp.login(ftp_user, ftp_pass)
    print(f"✓ Connected to FTP")
    
    # Upload file
    with open(local_file, 'rb') as f:
        ftp.storbinary(f"STOR {remote_path}", f)
    print(f"✓ File uploaded")
    
    ftp.quit()
    
except Exception as e:
    print(f"✗ Error: {e}")
