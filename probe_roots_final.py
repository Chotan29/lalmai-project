import ftplib
import requests
import uuid
import time

ftp_host = "cs3001.webhostbox.net"
ftp_user = "lalma87b"
ftp_pass = "Ait@9423~"

paths_to_test = ["/public_html/", "/ims.lalmaigc.edu.bd/public/", "/ims.lalmaigc.edu.bd/"]
# Try a filename that almost certainly won't be blocked
probe_filename = "index_probe.php"
unique_content = str(uuid.uuid4())
php_content = f"<?php echo '{unique_content}'; ?>"

try:
    ftp = ftplib.FTP(ftp_host)
    ftp.login(ftp_user, ftp_pass)
    print("Connected")

    uploaded_paths = []
    for path in paths_to_test:
        try:
            from io import BytesIO
            bio = BytesIO(php_content.encode('utf-8'))
            ftp.storbinary(f'STOR {path}{probe_filename}', bio)
            uploaded_paths.append(path)
            print(f"Uploaded to {path}")
        except: pass

    urls = [
        "https://ims.lalmaigc.edu.bd/index_probe.php",
        "https://ims.lalmaigc.edu.bd/public/index_probe.php"
    ]

    headers = {'User-Agent': 'Mozilla/5.0'}
    for url in urls:
        try:
            r = requests.get(url, headers=headers, timeout=10)
            print(f"URL: {url} | Status: {r.status_code} | Match: {unique_content in r.text}")
        except Exception as e: print(f"Error {url}: {e}")

    for path in uploaded_paths:
        try: ftp.delete(path + probe_filename)
        except: pass
    ftp.quit()
except Exception as e: print(f"Error: {e}")
