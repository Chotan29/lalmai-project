# Live Deployment Checklist - ims.lalmaigc.edu.bd

**Deployment Date:** May 12, 2026
**Status:** LIVE ✅

---

## ১) Immediate Verification (Deploy করার পর প্রথম দিন)

### Website Access:
- [x] https://ims.lalmaigc.edu.bd/ - Load হয়
- [x] https://ims.lalmaigc.edu.bd/online-registration - কাজ করছে
- [x] https://ims.lalmaigc.edu.bd/student - কাজ করছে
- [x] https://ims.lalmaigc.edu.bd/login - কাজ করছে

### Database Connection:
- [x] Student data fetch হচ্ছে
- [x] Registration form submit হচ্ছে
- [x] Payment system working

### SSL Certificate:
- [x] HTTPS secure
- [x] No certificate warnings

---

## ২) Performance Optimization (করা হয়েছে/করতে হবে)

### Laravel Optimization:
```bash
# এই commands run করুন terminal থেকে:
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer dump-autoload --optimize
```

### Caching Strategy:
- [ ] Config cached
- [ ] Routes cached
- [ ] Views compiled
- [ ] Autoloader optimized

### Database Optimization:
- [ ] Indexes created
- [ ] Query optimization done
- [ ] Slow query log reviewed

---

## ৩) Security Checklist

### Files & Permissions:
- [x] storage/ folder writable (755/775)
- [x] bootstrap/cache/ writable
- [x] .env file protected (not accessible)
- [x] .htaccess properly configured

### Application Security:
- [x] APP_DEBUG = false (production)
- [x] APP_KEY generated
- [ ] CSRF protection enabled (check forms)
- [ ] SQL injection protection (use queries correctly)
- [ ] XSS protection enabled

### Database Security:
- [x] DB user has limited privileges
- [x] Strong password set
- [x] Root credentials not exposed

### Web Security:
- [x] SSL/HTTPS enforced
- [ ] HTTP → HTTPS redirect enabled
- [ ] Security headers configured
- [ ] CORS properly set if needed

---

## ৪) Monitoring & Logging

### Error Logging:
```bash
# Monitor live errors:
tail -f storage/logs/laravel.log

# Check for errors daily
# Location: storage/logs/laravel-YYYY-MM-DD.log
```

### Access Logging:
- [ ] Web server access logs checked
- [ ] Error patterns monitored
- [ ] Unusual activity flagged

### Performance Monitoring:
- [ ] Page load time < 2 seconds
- [ ] Database queries < 100ms
- [ ] Memory usage < 256MB

---

## ৫) Backup & Recovery

### Backup Schedule:
- [ ] Daily database backup at 2 AM
- [ ] Weekly full backup (files + DB)
- [ ] Monthly offsite backup

### cPanel Backup:
- [ ] AutoSSL backup enabled
- [ ] Scheduled backups set
- [ ] Backup location verified

### Recovery Test:
- [ ] Restoration tested (monthly)
- [ ] Backup integrity verified
- [ ] Recovery time documented

---

## ৬) Email Configuration

### SMTP Settings (in .env):
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.ims.lalmaigc.edu.bd
MAIL_PORT=587
MAIL_USERNAME=your_email@lalmaigc.edu.bd
MAIL_PASSWORD=password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@lalmaigc.edu.bd
MAIL_FROM_NAME="Lalmai Govt College"
```

### Email Testing:
- [ ] Registration confirmation emails send
- [ ] Payment receipt emails send
- [ ] Admin notification emails send
- [ ] No email bounces

---

## ৭) Payment Gateway (যদি integrated থাকে)

### SSL Requirement:
- [x] HTTPS/SSL enabled (required for payments)

### Payment Testing:
- [ ] Test transaction completed
- [ ] Payment receipt generated
- [ ] Student marked as paid
- [ ] Admin notification sent

### Currency & Amount:
- [ ] Amount in BDT (টাকা)
- [ ] Conversion rates correct
- [ ] Tax/fee calculation verified

---

## ৮) User Management

### Admin Access:
- [ ] Admin login working
- [ ] Dashboard accessible
- [ ] All admin features working

### Student Access:
- [ ] Student login working
- [ ] Registration form accessible
- [ ] Profile view/edit working
- [ ] Payment history visible

### Guardian Access (যদি থাকে):
- [ ] Guardian login working
- [ ] Student view accessible

---

## ৯) Data Integrity

### Database Checks:
```bash
php artisan tinker
# Check data:
>>> DB::table('students')->count()
>>> DB::table('online_registrations')->count()
>>> DB::table('users')->count()
```

### Data Migration:
- [ ] Old data migrated correctly
- [ ] No data loss
- [ ] Foreign keys intact
- [ ] Student-related data linked properly

---

## ১০) Daily/Weekly Tasks

### Daily:
- [ ] Check error logs
- [ ] Verify backup completion
- [ ] Monitor server uptime
- [ ] Check for failed transactions

### Weekly:
- [ ] Review access logs
- [ ] Check disk space usage
- [ ] Verify all backups
- [ ] Test recovery procedures
- [ ] Update security patches (if needed)

### Monthly:
- [ ] Database optimization (OPTIMIZE TABLE)
- [ ] Performance review
- [ ] Security audit
- [ ] User activity report
- [ ] Full system backup test

---

## ১১) Critical Contacts

### Support Contacts:
```
Hosting Provider: [Your Provider Name]
Support Email: support@yourprovider.com
Support Phone: [Your Phone Number]

Database Admin: [Your Email]
Email: admin@ims.lalmaigc.edu.bd
```

### Emergency Contacts:
- [ ] Hosting support phone
- [ ] Database admin contact
- [ ] Development team contact

---

## ১২) Documentation

### Created Documents:
- [x] Online_Registration_Student_Guide_BN.doc
- [x] CPANEL_DEPLOYMENT_GUIDE.md
- [x] This checklist

### To Document:
- [ ] Admin user manual
- [ ] Database schema documentation
- [ ] API documentation (if applicable)
- [ ] Troubleshooting guide

---

## ১३) Post-Launch Optimizations

### Next Steps (Order of Priority):
1. [ ] Monitor first 24 hours closely
2. [ ] Collect user feedback
3. [ ] Fix any reported issues
4. [ ] Optimize slow queries
5. [ ] Add monitoring tools (e.g., New Relic, DataDog)
6. [ ] Implement caching layer (Redis if available)
7. [ ] Set up automated alerts

### Long-term Plan:
- [ ] Performance enhancement
- [ ] Feature additions
- [ ] User experience improvements
- [ ] Security hardening
- [ ] Scalability planning

---

## ১৪) Testing Scenarios (Manual Testing)

### Registration Flow:
- [ ] New Student Registration
- [ ] Old Student Registration
- [ ] Form validation working
- [ ] Image upload working
- [ ] Data saving to DB

### Payment Flow (if applicable):
- [ ] Payment initiation
- [ ] Gateway redirect
- [ ] Payment processing
- [ ] Receipt generation
- [ ] Database update

### Admin Operations:
- [ ] View students
- [ ] Edit student info
- [ ] View registrations
- [ ] Generate reports
- [ ] Delete/Archive data

---

## ১৫) Success Metrics

### Uptime Target:
- [ ] 99.5% monthly uptime
- [ ] Response time < 2s
- [ ] Zero data loss

### Usage Metrics (First Month):
- Total registrations: ______
- Successful payments: ______
- Failed transactions: ______
- Average session duration: ______

---

## Sign-Off

**Deployed By:** [Your Name]
**Deployment Date:** May 12, 2026
**Go-Live Date:** May 12, 2026
**Status:** ✅ LIVE & OPERATIONAL

**Next Review Date:** May 19, 2026 (1 week after launch)

---

## Important Commands (Keep Handy)

```bash
# Check error logs
tail -f storage/logs/laravel.log

# Clear caches if needed
php artisan cache:clear
php artisan view:clear

# Database backup
mysqldump -u lalmai_user -p lalmai_prod > backup.sql

# Check disk space
df -h

# Check memory usage
free -h

# Restart services (if needed)
# Contact hosting provider
```

---

**Good luck! The system is live. Monitor closely. 🚀**
