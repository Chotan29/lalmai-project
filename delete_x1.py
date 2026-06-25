from ftplib import FTP

# FTP credentials
ftp_host = "cs3001.webhostbox.net"
ftp_user = "lalma87b"
ftp_pass = "Ait@9423~"

# Remote path
remote_path = "/ims.lalmaigc.edu.bd/public/x1.php"

try:
    # Connect to FTP
    ftp = FTP(ftp_host)
    ftp.login(ftp_user, ftp_pass)
    print(f"✓ Connected to FTP")
    
    # Delete file
    ftp.delete(remote_path)
    print(f"✓ File {remote_path} deleted")
    
    # Verify deletion
    try:
        ftp.size(remote_path)
        print("✗ Error: File still exists!")
    except:
        print("✓ Verified: File removed")
        
    ftp.quit()
    
except Exception as e:
    print(f"✗ Error: {e}")
