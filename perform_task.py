from ftplib import FTP
import os
import requests

ftp_host = "cs3001.webhostbox.net"
ftp_user = "lalma87b"
ftp_pass = "Ait@9423~"

local_file = "public/deploy_migrate_once.php"
remote_path = "/ims.lalmaigc.edu.bd/public/deploy_migrate_once.php"
remote_dir = "/ims.lalmaigc.edu.bd/public"
filename = "deploy_migrate_once.php"

results = []

# Step 1 & 2: Upload and Verify
try:
    ftp = FTP(ftp_host)
    ftp.login(ftp_user, ftp_pass)
    print("Step 1: Uploading...")
    with open(local_file, 'rb') as f:
        ftp.storbinary(f'STOR {remote_path}', f)
    results.append(("Step 1: Upload local file", "pass"))
    
    print("Step 2: Verifying...")
    ftp.cwd(remote_dir)
    size = ftp.size(filename)
    if size > 0:
        results.append(("Step 2: Verify file existence and non-zero size", f"pass ({size} bytes)"))
    else:
        results.append(("Step 2: Verify file existence and non-zero size", "fail (size 0)"))
    ftp.quit()
except Exception as e:
    print(f"Error during FTP: {e}")
    results.append(("Step 1: Upload local file", f"fail ({e})"))
    results.append(("Step 2: Verify file existence", "fail"))

# Step 3: Run with token
url = "https://ims.lalmaigc.edu.bd/deploy_migrate_once.php?token=LALMAI_DEPLOY_20260519_SAFE"
try:
    print("Step 3: Executing URL with token...")
    resp = requests.get(url)
    print("--- First 120 lines of response ---")
    lines = resp.text.splitlines()[:120]
    for line in lines:
        print(line)
    print("--- End response ---")
    if resp.status_code == 200:
        results.append(("Step 3: Execute URL with token", "pass"))
    else:
        results.append(("Step 3: Execute URL with token", f"fail (status {resp.status_code})"))
except Exception as e:
    results.append(("Step 3: Execute URL with token", f"fail ({e})"))

# Step 4: Run without token
url_no_token = "https://ims.lalmaigc.edu.bd/deploy_migrate_once.php"
try:
    print("Step 4: Executing URL without token...")
    resp = requests.get(url_no_token)
    print(f"Status code without token: {resp.status_code}")
    results.append(("Step 4: Execute URL without token (report status)", f"Status Code: {resp.status_code}"))
except Exception as e:
    results.append(("Step 4: Execute URL without token", f"fail ({e})"))

# Step 5: Verify removal
try:
    print("Step 5: Verifying removal...")
    ftp = FTP(ftp_host)
    ftp.login(ftp_user, ftp_pass)
    ftp.cwd(remote_dir)
    files = ftp.nlst()
    if filename not in files:
        results.append(("Step 5: Verify script file is removed", "pass (file not found on FTP)"))
    else:
        results.append(("Step 5: Verify script file is removed", "fail (file still exists)"))
    ftp.quit()
except Exception as e:
     results.append(("Step 5: Verify script file is removed", f"fail ({e})"))

print("\nSummary:")
for task, status in results:
    print(f"{task}: {status}")

