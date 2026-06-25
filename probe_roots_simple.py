import ftplib
import requests
import uuid

ftp_host = "cs3001.webhostbox.net"
ftp_user = "lalma87b"
ftp_pass = "Ait@9423~"

# Mapping paths to unique markers
path_markers = {
    "/public_html/": "ROOT_PUBLIC_HTML",
    "/ims.lalmaigc.edu.bd/public/": "ROOT_IMS_PUBLIC",
    "/ims.lalmaigc.edu.bd/": "ROOT_IMS_BASE"
}
probe_filename = "probe_id.txt"

try:
    ftp = ftplib.FTP(ftp_host)
    ftp.login(ftp_user, ftp_pass)
    print("FTP Connected")

    uploaded = []
    for path, marker in path_markers.items():
        try:
            from io import BytesIO
            bio = BytesIO(marker.encode('utf-8'))
            ftp.storbinary(f'STOR {path}{probe_filename}', bio)
            uploaded.append(path)
            print(f"Uploaded {marker} to {path}")
        except Exception as e:
            print(f"Failed {path}: {e}")

    urls = [
        "https://ims.lalmaigc.edu.bd/probe_id.txt",
        "https://ims.lalmaigc.edu.bd/public/probe_id.txt"
    ]

    # Try different common User-Agents to avoid 406
    ua = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36"
    
    print("\nTesting URLs:")
    for url in urls:
        try:
            r = requests.get(url, headers={'User-Agent': ua}, timeout=10)
            content = r.text.strip()
            print(f"URL: {url} | Status: {r.status_code} | Content: {content[:50]}")
            for path, marker in path_markers.items():
                if marker in content:
                    print(f"MATCH FOUND: {url} is served from FTP path: {path}")
        except Exception as e:
            print(f"Error {url}: {e}")

    for path in uploaded:
        try: ftp.delete(path + probe_filename)
        except: pass
    ftp.quit()
except Exception as e:
    print(f"Error: {e}")
