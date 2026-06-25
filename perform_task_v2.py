from ftplib import FTP
import os
import requests

ftp_host = "cs3001.webhostbox.net"
ftp_user = "lalma87b"
ftp_pass = "Ait@9423~"

remote_dir = "/ims.lalmaigc.edu.bd/public"
filename = "deploy_migrate_once.php"
remote_path = f"{remote_dir}/{filename}"

try:
    ftp = FTP(ftp_host)
    ftp.login(ftp_user, ftp_pass)
    print(f"Connecting and deleting {remote_path}...")
    ftp.delete(remote_path)
    print("Deleted successfully.")
    ftp.quit()
except Exception as e:
    print(f"Error: {e}")
