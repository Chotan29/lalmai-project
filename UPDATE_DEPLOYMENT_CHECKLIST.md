# Live Update Deployment Checklist
# Project: Lalmai Govt College IMS
# URL: https://ims.lalmaigc.edu.bd
# Date: May 12, 2026

---

## ০) প্রি-ডিপ্লয়মেন্ট - Local Testing

### সব কিছু test করুন local এ:
- [ ] Registration form submit test
- [ ] Payment process test
- [ ] Student profile view/edit test
- [ ] Admin panel access test
- [ ] Database migration test
- [ ] No errors in browser console
- [ ] No errors in terminal/logs

### Local Database:
- [ ] Migration রান করা হয়েছে: `php artisan migrate`
- [ ] Seed ডেটা (যদি নতুন থাকে): `php artisan db:seed`
- [ ] Cache clear করা হয়েছে: `php artisan cache:clear`

---

## ১) Live সার্ভারে Backup নিন

Before uploading, live server থেকে backup নিন:

```bash
# SSH করুন live server এ
ssh username@ims.lalmaigc.edu.bd

# Database backup নিন
cd ~/ims.lalmaigc.edu.bd
mysqldump -u lalmai_user -p lalmai_prod > backup_before_update_$(date +%Y%m%d_%H%M%S).sql

# File backup নিন (optional)
# tar -czf backup_files_$(date +%Y%m%d_%H%M%S).tar.gz .
```

---

## ২) Upload যোগ্য Files/Folders

### নিচের files/folders গুলো upload করুন (লোকাল থেকে লাইভ এ):

#### Core Application Files:
- [ ] `app/` - সম্পূর্ণ folder
- [ ] `routes/` - সব route files
- [ ] `config/` - configuration files (বাদ দিন: production-only configs)
- [ ] `resources/views/` - সব blade templates
- [ ] `resources/lang/` - language files
- [ ] `database/migrations/` - নতুন migrations
- [ ] `database/seeds/` - যদি নতুন seed থাকে
- [ ] `public/assets/` - CSS, JS, images (শুধু নতুন/update করা)
- [ ] `public/js/` - JavaScript files
- [ ] `public/css/` - CSS files

#### Configuration Files:
- [ ] `.env` - Update করুন live server এ (manually)
- [ ] `composer.json` - যদি নতুন dependency থাকে
- [ ] `.htaccess` - যদি routing change হয়েছে

#### Important NOT to Upload:
- [ ] ❌ `vendor/` folder (Composer install করবে)
- [ ] ❌ `node_modules/` folder
- [ ] ❌ `.env` (Manual edit করবেন live এ)
- [ ] ❌ `storage/logs/` (live logs preserve করতে)
- [ ] ❌ `storage/app/` (live user data আছে)
- [ ] ❌ `public/images/` (live images আছে)

---

## ৩) Upload Process

### Option A: FTP/SFTP ব্যবহার করে (FileZilla):

1. **Connect করুন:**
   - Host: ims.lalmaigc.edu.bd (অথবা server IP)
   - Username: FTP username
   - Password: FTP password
   - Port: 21

2. **Files upload করুন:**
   ```
   Local Path → Remote Path (~/public_html/)
   app/        → app/
   routes/     → routes/
   config/     → config/
   resources/  → resources/
   database/   → database/
   public/     → public/ (শুধু assets, না images)
   ```

3. **File Permissions:**
   ```
   Files: 644
   Folders: 755
   storage/: 775
   bootstrap/cache/: 775
   ```

### Option B: SSH/SCP ব্যবহার করে (Terminal):

```bash
# Local machine থেকে:
cd /path/to/lalmai/local/project

# Upload app folder
scp -r app/ username@ims.lalmaigc.edu.bd:~/ims.lalmaigc.edu.bd/

# Upload routes
scp -r routes/ username@ims.lalmaigc.edu.bd:~/ims.lalmaigc.edu.bd/

# Upload resources
scp -r resources/ username@ims.lalmaigc.edu.bd:~/ims.lalmaigc.edu.bd/

# Upload config
scp -r config/ username@ims.lalmaigc.edu.bd:~/ims.lalmaigc.edu.bd/

# Upload database
scp -r database/ username@ims.lalmaigc.edu.bd:~/ims.lalmaigc.edu.bd/

# Update public assets
scp -r public/js/ username@ims.lalmaigc.edu.bd:~/ims.lalmaigc.edu.bd/public/
scp -r public/css/ username@ims.lalmaigc.edu.bd:~/ims.lalmaigc.edu.bd/public/
```

---

## ৪) Live সার্ভারে Post-Upload Tasks

Live সার্ভারে SSH করে এই commands run করুন:

### Step 1: Directory থেকে যান
```bash
ssh username@ims.lalmaigc.edu.bd
cd ~/ims.lalmaigc.edu.bd
```

### Step 2: Composer dependencies update
```bash
composer install --no-dev --optimize-autoloader
```

### Step 3: Migration run করুন
```bash
php artisan migrate --force
```

### Step 4: Seed run করুন (যদি নতুন seeder থাকে)
```bash
php artisan db:seed --force
```

### Step 5: Cache clear করুন
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Step 6: Cache rebuild করুন (production optimize)
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 7: Permissions set করুন
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 644 .env
```

### Step 8: Application restart (optional, যদি supervisor আছে)
```bash
# Check if Laravel Horizon/Queue running
ps aux | grep artisan

# Restart if needed
php artisan horizon:terminate
# অথবা queue workers restart করুন
```

---

## ৫) .env File Update (Live Server এ Manual)

Live server এ `.env` file edit করুন (পুরাতন values preserve করুন):

```bash
nano ~/ims.lalmaigc.edu.bd/.env
```

Check করুন এই values গুলো:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ims.lalmaigc.edu.bd

DB_HOST=localhost
DB_DATABASE=lalmai_prod
DB_USERNAME=lalmai_user
DB_PASSWORD=[existing_password]

MAIL_MAILER=smtp
MAIL_HOST=[existing_mail_host]
MAIL_USERNAME=[existing_mail_user]
MAIL_PASSWORD=[existing_mail_pass]
```

---

## ৬) Database Changes (যদি migrations নতুন থাকে)

যদি নতুন migration files যোগ করা হয়েছে:

```bash
# Live server এ
cd ~/ims.lalmaigc.edu.bd
php artisan migrate --force
```

**Important:** Migration roll back করতে:
```bash
php artisan migrate:rollback
```

---

## ৭) Testing After Upload

### Website Access:
- [ ] https://ims.lalmaigc.edu.bd - Load হয়?
- [ ] https://ims.lalmaigc.edu.bd/online-registration - কাজ করছে?
- [ ] https://ims.lalmaigc.edu.bd/student - কাজ করছে?
- [ ] https://ims.lalmaigc.edu.bd/login - Login page কাজ করছে?

### Database Connectivity:
```bash
# Live সার্ভারে SSH করে:
php artisan tinker

# Terminal এ:
>>> DB::connection()->getPdo()
# Null return না হলে OK
>>> DB::table('students')->count()
# Number return হলে OK
>>> exit()
```

### Error Logs Check:
```bash
tail -f ~/ims.lalmaigc.edu.bd/storage/logs/laravel.log
```

### Key Features Test:
- [ ] Student registration form submit
- [ ] New student registration working
- [ ] Old student registration working
- [ ] Payment gateway working (যদি থাকে)
- [ ] Admin panel access working
- [ ] Database save working

---

## ৮) Rollback Plan (যদি problem হয়)

যদি কিছু ভুল হয়, এই steps follow করুন:

```bash
# Live সার্ভারে:
ssh username@ims.lalmaigc.edu.bd
cd ~/ims.lalmaigc.edu.bd

# 1. Previous app files restore করুন (যদি backup নিয়েছিলেন)
# From FTP: Re-upload previous app/ folder
# Or from backup: cp -r app.backup/ app/

# 2. Database rollback করুন (যদি migration problem হয়েছে)
php artisan migrate:rollback

# 3. Cache clear করুন
php artisan cache:clear

# 4. Website এ যান এবং check করুন
```

---

## ৯) Performance Check Post-Deployment

Upload হওয়ার পর performance check করুন:

```bash
# Page load time test
curl -I https://ims.lalmaigc.edu.bd
# HTTP/2 200 OK দেখা যাওয়া উচিত

# Database query test
php artisan tinker
>>> \DB::enableQueryLog();
>>> DB::table('students')->limit(10)->get();
>>> dd(DB::getQueryLog());
>>> exit()
```

---

## ১০) Deployment Checklist Summary

### Pre-Deployment:
- [x] Local testing complete
- [x] All files ready
- [x] Backup taken

### Upload Phase:
- [ ] Files uploaded via FTP/SCP
- [ ] Permissions set correctly
- [ ] `.env` verified on live

### Post-Upload:
- [ ] Composer installed
- [ ] Migrations run
- [ ] Cache cleared & rebuilt
- [ ] Website accessible
- [ ] Features tested
- [ ] Error logs checked

### Monitoring:
- [ ] Error log monitored (24 hours)
- [ ] User feedback collected
- [ ] Performance metrics recorded

---

## ১১) Files Changed Summary

নিচের files গুলো change হয়েছে (update করতে হবে):

### Must Upload:
```
✓ app/Http/Controllers/ - [Changed files list]
✓ app/Models/ - [Changed files list]
✓ resources/views/ - [Changed files list]
✓ routes/web.php - [Changed files list]
✓ config/ - [Changed files list]
✓ database/migrations/ - [New migrations list]
```

### Check করুন:
```
? public/js/ - কোনো নতুন file?
? public/css/ - কোনো নতুন file?
? package.json - কোনো নতুন npm package?
? composer.json - কোনো নতুন dependency?
```

---

## ১२) Critical Commands (রেডি রাখুন)

Deploy করার সময় এই commands copy করে রাখুন:

```bash
# SSH login
ssh username@ims.lalmaigc.edu.bd

# Go to project
cd ~/ims.lalmaigc.edu.bd

# Install/update composer
composer install --no-dev --optimize-autoloader

# Database migration
php artisan migrate --force

# Clear caches
php artisan cache:clear && php artisan config:clear && php artisan view:clear && php artisan route:clear

# Build caches
php artisan config:cache && php artisan route:cache && php artisan view:cache

# Check status
tail -f storage/logs/laravel.log
```

---

## ১३) Deployment Timeline

**Estimated Time Required:**
- Upload files: 5-10 minutes
- Composer install: 5 minutes
- Database migration: 2-3 minutes
- Testing: 10-15 minutes
- **Total: 25-40 minutes**

**Best Time to Deploy:**
- Off-peak hours (রাত ২-৪ টা)
- Weekend mornings
- Avoid: ব্যস্ত student registration time

---

## ১४) Post-Deployment Monitoring (24-48 hours)

Deploy করার পর কমপক্ষে ২৪ ঘন্টা monitor করুন:

### Hourly (প্রথম 6 ঘন্টা):
- [ ] Error logs check
- [ ] Website accessibility
- [ ] Database connection

### 4-Hourly (পরবর্তী 24 ঘন্টা):
- [ ] Performance metrics
- [ ] User reports
- [ ] Payment transactions (যদি থাকে)

### Daily (পরবর্তী 48 ঘন্টা):
- [ ] Error log analysis
- [ ] Database backups
- [ ] System health

---

## Emergency Contact

যদি deployment এ কোনো সমস্যা হয়:

```
1. Error log check করুন
2. Rollback করুন (যদি critical issue হয়)
3. Support team যোগাযোগ করুন
4. Database backup restore করুন (যদি ডেটা issue হয়)
```

---

## Sign-Off

**Ready for Upload:** ✅ YES / ❌ NO

**Reviewed By:** [Your Name]
**Approved By:** [Admin Name]
**Expected Go-Live:** [Date & Time]

---

**Note:** এই checklist ফলো করলে deployment smooth হবে এবং কোনো downtime থাকবে না। 🚀
