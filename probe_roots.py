import ftplib
import requests
import uuid
import time

ftp_host = "cs3001.webhostbox.net"
ftp_user = "lalma87b"
ftp_pass = "Ait@9423~"

paths_to_test = ["/public_html/", "/ims.lalmaigc.edu.bd/public/", "/ims.lalmaigc.edu.bd/", "/www/"]
probe_filename = "probe_live_root.txt"
unique_content = str(uuid.uuid4())

try:
    ftp = ftplib.FTP(ftp_host)
    ftp.login(ftp_user, ftp_pass)
    print(f"Connected to FTP: {ftp_host}")

    uploaded_paths = []
    for path in paths_to_test:
        try:
            # Ensure the directory exists or just try to STO in it
            remote_file_path = path + probe_filename
            from io import BytesIO
            bio = BytesIO(unique_content.encode('utf-8'))
            ftp.storbinary(f'STOR {remote_file_path}', bio)
            print(f"Uploaded to {remote_file_path}")
            uploaded_paths.append(path)
        except Exception as e:
            print(f"Failed to upload to {path}: {e}")

    # URLs to test
    urls = [
        "https://ims.lalmaigc.edu.bd/probe_live_root.txt",
        "https://ims.lalmaigc.edu.bd/public/probe_live_root.txt"
    ]

    print("\nTesting URLs:")
    time.sleep(2) # Give it a moment
    for url in urls:
        try:
            r = requests.get(url, timeout=10)
            status = r.status_code
            match = r.text.strip() == unique_content.strip()
            print(f"URL: {url} | Status: {status} | Content Match: {match}")
            if match:
                print(f"Found mapping for: {url}")
        except Exception as e:
            print(f"Error fetching {url}: {e}")

    print("\nCleaning up:")
    for path in uploaded_paths:
        try:
            ftp.delete(path + probe_filename)
            print(f"Deleted {path + probe_filename}")
        except Exception as e:
            print(f"Failed to delete {path + probe_filename}: {e}")

    ftp.quit()
except Exception as e:
    print(f"FTP Error: {e}")
