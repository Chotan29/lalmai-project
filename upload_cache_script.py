from ftplib import FTP

ftp_host = "cs3001.webhostbox.net"
ftp_user = "lalma87b"
ftp_pass = "Ait@9423~"

local_file = "deploy_cache_clear.php"
remote_path = "/ims.lalmaigc.edu.bd/deploy_cache_clear.php"

try:
    ftp = FTP(ftp_host)
    ftp.login(ftp_user, ftp_pass)
    print("✓ Connected to FTP")
    
    with open(local_file, 'rb') as f:
        ftp.storbinary(f'STOR {remote_path}', f)
    print(f"✓ Cache clear script uploaded")
    
    ftp.quit()
except Exception as e:
    print(f"✗ Error: {e}")
