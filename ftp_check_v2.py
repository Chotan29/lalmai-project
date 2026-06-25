from ftplib import FTP

ftp_host = "cs3001.webhostbox.net"
ftp_user = "lalma87b"
ftp_pass = "Ait@9423~"

try:
    ftp = FTP(ftp_host)
    ftp.login(ftp_user, ftp_pass)
    
    path = "/ims.lalmaigc.edu.bd/public"
    print(f"Checking directory: {path}")
    ftp.cwd(path)
    files = ftp.nlst()
    print("Files in /ims.lalmaigc.edu.bd/public:")
    print(files)
    
    target = "deploy_migrate_once.php"
    if target in files:
        print(f"SUCCESS: {target} found in nlst output.")
        try:
            # Try to get size while in the directory
            size = ftp.size(target)
            print(f"Size of {target}: {size}")
        except Exception as e:
            print(f"Could not get size of {target}: {e}")
    else:
        print(f"FAILURE: {target} NOT found in nlst output.")

    ftp.quit()
except Exception as e:
    print(f"Error: {e}")
