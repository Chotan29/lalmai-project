from ftplib import FTP
import os

ftp_host = "cs3001.webhostbox.net"
ftp_user = "lalma87b"
ftp_pass = "Ait@9423~"

try:
    ftp = FTP(ftp_host)
    ftp.login(ftp_user, ftp_pass)
    
    print(f"PWD: {ftp.pwd()}")
    
    print("Files/Dirs in root:")
    print(ftp.nlst())
    
    paths_to_check = [
        "/ims.lalmaigc.edu.bd",
        "/public_html",
        "/ims.lalmaigc.edu.bd/public"
    ]
    
    for path in paths_to_check:
        try:
            ftp.cwd(path)
            print(f"Path exists (cwd success): {path}")
            ftp.cwd("/") # Reset
        except Exception as e:
            print(f"Path does NOT exist or cannot cwd: {path} ({e})")
            
    files_to_verify = [
        "/ims.lalmaigc.edu.bd/app/Http/Controllers/Academic/SubjectController.php",
        "/ims.lalmaigc.edu.bd/public/deploy_migrate_once.php"
    ]
    
    for file_path in files_to_verify:
        try:
            size = ftp.size(file_path)
            if size is not None:
                print(f"File exists: {file_path}, Size: {size}")
            else:
                print(f"File might NOT exist (size returned None): {file_path}")
        except Exception as e:
             print(f"File check failed: {file_path} ({e})")

    ftp.quit()
except Exception as e:
    print(f"General Error: {e}")
