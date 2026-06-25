import ftplib
import requests
import os

ftp_host = "cs3001.webhostbox.net"
ftp_user = "lalma87b"
ftp_pass = "Ait@9423~"
remote_path = "/ims.lalmaigc.edu.bd/public/a1.php"
local_file = "tmp_run_live.php"
url = "https://ims.lalmaigc.edu.bd/a1.php"

try:
    print(f"Connecting to {ftp_host}...")
    ftp = ftplib.FTP(ftp_host)
    ftp.login(ftp_user, ftp_pass)
    
    with open(local_file, 'rb') as f:
        ftp.storbinary(f'STOR {remote_path}', f)
    print(f"Uploaded {local_file} to {remote_path}")

    print(f"Requesting {url}...")
    ua = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36"
    r = requests.get(url, headers={'User-Agent': ua}, timeout=60)
    print("Response Body:")
    print(r.text)
    
    print(f"Deleting {remote_path}...")
    ftp.delete(remote_path)
    print("Deleted remote file.")
    
    ftp.quit()
    os.remove(local_file)
    print("Cleaned up local file.")

except Exception as e:
    print(f"An error occurred: {e}")
