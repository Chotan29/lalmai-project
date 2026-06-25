import ftplib
import requests
import uuid
import time

ftp_host = "cs3001.webhostbox.net"
ftp_user = "lalma87b"
ftp_pass = "Ait@9423~"

paths_to_test = ["/public_html/", "/ims.lalmaigc.edu.bd/public/", "/ims.lalmaigc.edu.bd/"]
probe_filename = "probe_test.html"
unique_content = str(uuid.uuid4())
html_content = f"<html><body>{unique_content}</body></html>"

try:
    ftp = ftplib.FTP(ftp_host)
    ftp.login(ftp_user, ftp_pass)
    print(f"Connected to FTP")

    uploaded_paths = []
    for path in paths_to_test:
        try:
            remote_file_path = path + probe_filename
            from io import BytesIO
            bio = BytesIO(html_content.encode('utf-8'))
            ftp.storbinary(f'STOR {remote_file_path}', bio)
            print(f"Uploaded to {remote_file_path}")
            uploaded_paths.append(path)
        except Exception as e:
            print(f"Failed to upload to {path}: {e}")

    urls = [
        "https://ims.lalmaigc.edu.bd/probe_test.html",
        "https://ims.lalmaigc.edu.bd/public/probe_test.html"
    ]

    print("\nTesting URLs:")
    headers = {'User-Agent': 'Mozilla/5.0'}
    for url in urls:
        try:
            r = requests.get(url, headers=headers, timeout=10)
            print(f"URL: {url} | Status: {r.status_code} | Match: {unique_content in r.text}")
            if unique_content in r.text:
                print(f"FOUND: {url} is live!")
        except Exception as e:
            print(f"Error {url}: {e}")

    for path in uploaded_paths:
        try:
            ftp.delete(path + probe_filename)
        except:
            pass
    ftp.quit()
except Exception as e:
    print(f"Error: {e}")
