# 🔧 Troubleshooting Guide

Common issues and solutions for the Network Monitoring System.

## 🚀 Installation Issues

### Composer Dependencies Fail
**Problem:** `composer install` fails with dependency conflicts.

**Solution:**
```bash
# Clear composer cache
composer clear-cache

# Install with ignore platform requirements
composer install --ignore-platform-reqs

# Or update dependencies
composer update
```

### Node.js Build Errors
**Problem:** `npm run build` fails with webpack errors.

**Solution:**
```bash
# Clear node_modules and reinstall
rm -rf node_modules
npm install

# Rebuild with verbose output
npm run build --verbose

# Check Node.js version compatibility
node --version
npm --version
```

### Database Connection Failed
**Problem:** Laravel cannot connect to database during installation.

**Solution:**
1. Verify database credentials in `.env` file
2. Ensure database server is running
3. Check firewall settings
4. Test connection manually:
   ```bash
   mysql -h localhost -u username -p
   ```

### Migration Errors
**Problem:** Database migrations fail during installation.

**Solution:**
```bash
# Check current migration status
php artisan migrate:status

# Reset and re-run migrations
php artisan migrate:fresh

# Run with verbose output
php artisan migrate --verbose
```

## 🔌 Connectivity Issues

### Devices Show Offline When They're Online
**Problem:** Monitored devices appear down despite being accessible.

**Diagnosis Steps:**
1. **Manual Ping Test:**
   ```bash
   ping DEVICE_IP_ADDRESS
   ```

2. **Check Device Configuration:**
   - Verify IP address in the system matches actual device IP
   - Ensure device allows ICMP echo replies
   - Check if device is behind firewall

3. **Review Monitoring Logs:**
   ```bash
   # Check Laravel logs
   tail -f storage/logs/laravel.log
   
   # Check monitoring script logs
   tail -f logs/monitor.log
   ```

4. **Test from Server:**
   ```bash
   # SSH to server running Laravel
   ping DEVICE_IP_ADDRESS
   ```

**Solutions:**
- Correct device IP address in system
- Configure device to allow ping requests
- Adjust firewall rules
- Modify monitoring script timeout values

### Python Script Cannot Communicate with API
**Problem:** Monitoring script fails to send data to Laravel API.

**Diagnosis Steps:**
1. **Check API Endpoint Accessibility:**
   ```bash
   curl -X GET http://your-domain.com/api/devices
   ```

2. **Verify Environment Variables:**
   ```bash
   echo $API_BASE_URL
   echo $API_TOKEN
   ```

3. **Test Direct API Call:**
   ```bash
   curl -X POST http://your-domain.com/api/devices/1/status \
        -H "Content-Type: application/json" \
        -d '{"status":"up","response_time":15.5}'
   ```

**Solutions:**
- Verify `API_BASE_URL` environment variable
- Check API authentication (if using tokens)
- Ensure web server allows POST requests
- Verify CORS configuration

## 🖥️ Web Interface Issues

### Dashboard Not Loading
**Problem:** Dashboard page loads blank or with errors.

**Diagnosis Steps:**
1. **Check Browser Console:**
   - Open Developer Tools (F12)
   - Look for JavaScript errors
   - Check network requests for failures

2. **Review Laravel Logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Clear Application Cache:**
   ```bash
   php artisan cache:clear
   php artisan view:clear
   php artisan config:clear
   ```

**Solutions:**
- Clear browser cache and hard refresh (Ctrl+F5)
- Check file permissions on storage directories
- Ensure all assets compiled successfully (`npm run build`)
- Verify database connectivity

### Authentication Failures
**Problem:** Unable to login or session expires unexpectedly.

**Diagnosis Steps:**
1. **Check Session Configuration:**
   - Verify `SESSION_DRIVER` in `.env`
   - Check session storage permissions

2. **Review Error Logs:**
   ```bash
   # Laravel logs
   tail -f storage/logs/laravel.log
   
   # Web server logs
   tail -f /var/log/apache2/error.log  # or nginx error log
   ```

3. **Test Database Connection:**
   ```bash
   php artisan tinker
   >>> DB::connection()->getPdo();
   ```

**Solutions:**
- Ensure `storage` directory is writable
- Verify database credentials
- Check session lifetime configuration
- Clear browser cookies for the domain

### Slow Page Load Times
**Problem:** Pages take too long to load.

**Diagnosis Steps:**
1. **Enable Debug Mode:**
   ```env
   # In .env file
   APP_DEBUG=true
   ```

2. **Check Database Queries:**
   - Install Laravel Debugbar
   - Review slow queries in logs

3. **Monitor System Resources:**
   ```bash
   # Check system resources
   top
   free -h
   df -h
   
   # Check database performance
   mysqladmin processlist
   ```

**Solutions:**
- Add database indexes to frequently queried columns
- Enable query caching
- Optimize images and assets
- Use CDN for static assets
- Implement pagination for large datasets

## 📊 Reporting Issues

### PDF Reports Not Generating
**Problem:** PDF report generation fails or produces blank documents.

**Diagnosis Steps:**
1. **Check DomPDF Installation:**
   ```bash
   composer show barryvdh/laravel-dompdf
   ```

2. **Review Error Logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Test PDF Generation:**
   ```php
   // In tinker
   $pdf = \ Barryvdh\DomPDF\Facade\Pdf::loadHTML('<h1>Test</h1>');
   $pdf->save('/tmp/test.pdf');
   ```

**Solutions:**
- Ensure `gd` and `imagick` PHP extensions are installed
- Check write permissions on storage directory
- Increase PHP memory limit
- Verify HTML template syntax

### Missing Data in Reports
**Problem:** Reports show incomplete or missing data.

**Diagnosis Steps:**
1. **Check Date Filters:**
   - Verify selected date range
   - Ensure timezone settings match

2. **Review Data Availability:**
   ```sql
   SELECT COUNT(*) FROM device_logs 
   WHERE checked_at BETWEEN 'start_date' AND 'end_date';
   ```

3. **Check Device Selection:**
   - Verify selected devices have data
   - Check device active status

**Solutions:**
- Adjust date range selection
- Ensure devices are marked as active
- Verify monitoring script is running regularly
- Check for data gaps in logs

## 🛡️ Security Issues

### Unauthorized Access Attempts
**Problem:** Suspicious login attempts or unauthorized access.

**Diagnosis Steps:**
1. **Review Access Logs:**
   ```bash
   # Web server access logs
   tail -f /var/log/apache2/access.log
   
   # Laravel logs
   grep "unauthorized\|forbidden" storage/logs/laravel.log
   ```

2. **Check Failed Login Attempts:**
   ```sql
   SELECT * FROM failed_jobs WHERE name LIKE '%login%';
   ```

3. **Monitor User Sessions:**
   ```sql
   SELECT user_id, ip_address, user_agent, last_activity 
   FROM sessions ORDER BY last_activity DESC LIMIT 10;
   ```

**Solutions:**
- Implement rate limiting
- Enable two-factor authentication
- Set up IP whitelisting
- Configure firewall rules
- Review user permissions regularly

### Vulnerability Scanning Alerts
**Problem:** Security scanners detect potential vulnerabilities.

**Diagnosis Steps:**
1. **Run Security Scan:**
   ```bash
   # Install Enlightn security checker
   composer require enlightn/security-checker
   
   # Run security check
   php artisan security:check
   ```

2. **Check Package Versions:**
   ```bash
   composer outdated
   ```

3. **Review Configuration:**
   - Check exposed debug information
   - Verify secure headers
   - Review CORS settings

**Solutions:**
- Update vulnerable packages
- Apply security patches
- Configure secure headers
- Implement content security policy
- Regular security audits

## ⏰ Scheduled Task Issues

### Monitoring Not Running Automatically
**Problem:** Scheduled monitoring tasks not executing.

**Diagnosis Steps:**
1. **Check Cron Configuration:**
   ```bash
   crontab -l
   ```

2. **Test Artisan Command Manually:**
   ```bash
   php artisan monitor:devices
   ```

3. **Review Cron Logs:**
   ```bash
   # Check system cron logs
   tail -f /var/log/cron
   
   # Check Laravel scheduled task logs
   tail -f storage/logs/laravel.log | grep schedule
   ```

4. **Verify Cron Service:**
   ```bash
   systemctl status cron  # or crond on some systems
   ```

**Solutions:**
- Ensure cron daemon is running
- Verify cron job syntax
- Check file permissions on artisan command
- Set proper PATH in crontab
- Add logging to cron jobs for debugging

### Task Execution Timeouts
**Problem:** Monitoring tasks exceed execution time limits.

**Diagnosis Steps:**
1. **Check PHP Configuration:**
   ```bash
   php -i | grep max_execution_time
   ```

2. **Review Task Performance:**
   ```bash
   # Time the command execution
   time php artisan monitor:devices
   ```

3. **Check Process Limits:**
   ```bash
   ulimit -a
   ```

**Solutions:**
- Increase `max_execution_time` in php.ini
- Optimize database queries
- Implement batch processing for large device sets
- Use queue workers for asynchronous processing
- Add progress indicators to long-running tasks

## 🐍 Python Script Issues

### Script Fails to Start
**Problem:** Python monitoring script crashes on startup.

**Diagnosis Steps:**
1. **Check Python Version:**
   ```bash
   python3 --version
   ```

2. **Verify Dependencies:**
   ```bash
   pip list | grep requests
   ```

3. **Run with Verbose Output:**
   ```bash
   python3 scripts/monitor.py --debug
   ```

4. **Check Syntax:**
   ```bash
   python3 -m py_compile scripts/monitor.py
   ```

**Solutions:**
- Install required Python packages
- Verify Python version compatibility
- Check file permissions
- Ensure script is executable

### Network Connectivity Problems
**Problem:** Python script cannot reach network devices.

**Diagnosis Steps:**
1. **Test Network Access:**
   ```bash
   # From server running script
   ping DEVICE_IP_ADDRESS
   telnet DEVICE_IP_ADDRESS PORT
   ```

2. **Check Firewall Rules:**
   ```bash
   iptables -L
   ```

3. **Verify Network Configuration:**
   ```bash
   ip route
   ```

**Solutions:**
- Adjust firewall rules
- Configure routing
- Check VLAN configurations
- Verify network segmentation policies

## 📱 Mobile/Browser Issues

### Interface Not Responsive on Mobile
**Problem:** Mobile interface appears broken or unusable.

**Diagnosis Steps:**
1. **Check Viewport Meta Tag:**
   ```html
   <meta name="viewport" content="width=device-width, initial-scale=1">
   ```

2. **Review CSS Media Queries:**
   ```css
   @media (max-width: 768px) {
     /* Mobile styles */
   }
   ```

3. **Test on Multiple Devices:**
   - Various screen sizes
   - Different browsers
   - iOS and Android platforms

**Solutions:**
- Implement responsive design principles
- Add mobile-specific CSS
- Test with browser developer tools
- Optimize touch targets for mobile

### Feature Compatibility Issues
**Problem:** Certain features don't work in specific browsers.

**Diagnosis Steps:**
1. **Check Browser Support:**
   - Use CanIUse.com to verify feature support
   - Test in multiple browsers

2. **Review JavaScript Compatibility:**
   ```javascript
   // Check for modern JavaScript features
   if (typeof Promise !== 'undefined') {
     // Feature supported
   }
   ```

3. **Use Polyfills:**
   ```html
   <script src="https://polyfill.io/v3/polyfill.min.js"></script>
   ```

**Solutions:**
- Add browser compatibility polyfills
- Implement graceful degradation
- Provide fallback implementations
- Use progressive enhancement techniques

## 🔧 Maintenance Issues

### Disk Space Exhaustion
**Problem:** System runs out of disk space.

**Diagnosis Steps:**
1. **Check Disk Usage:**
   ```bash
   df -h
   ```

2. **Find Large Files:**
   ```bash
   du -ah /var/www | sort -rh | head -20
   ```

3. **Review Log Sizes:**
   ```bash
   ls -lh storage/logs/
   ```

**Solutions:**
- Implement log rotation
- Clean up old backups
- Archive historical data
- Increase disk space
- Configure log levels appropriately

### Database Performance Degradation
**Problem:** Database queries become progressively slower.

**Diagnosis Steps:**
1. **Check Query Performance:**
   ```sql
   SHOW PROCESSLIST;
   EXPLAIN SELECT * FROM devices WHERE status = 'down';
   ```

2. **Review Index Usage:**
   ```sql
   SHOW INDEX FROM devices;
   ANALYZE TABLE devices;
   ```

3. **Monitor Slow Queries:**
   ```bash
   # Enable slow query log in MySQL
   # Check /var/log/mysql/slow.log
   ```

**Solutions:**
- Add missing database indexes
- Optimize complex queries
- Implement query caching
- Partition large tables
- Regular database maintenance

## 🆘 Emergency Procedures

### System Completely Unresponsive
**Problem:** Entire system is inaccessible.

**Immediate Actions:**
1. **Check Server Status:**
   ```bash
   ping SERVER_IP
   ssh SERVER_USER@SERVER_IP
   ```

2. **Restart Services:**
   ```bash
   sudo systemctl restart apache2  # or nginx
   sudo systemctl restart mysql
   sudo systemctl restart redis
   ```

3. **Check System Resources:**
   ```bash
   top
   free -h
   df -h
   ```

4. **Review Recent Changes:**
   - Check deployment logs
   - Review configuration changes
   - Identify recent updates

**Recovery Steps:**
- Rollback to previous working state
- Restore from recent backup
- Revert problematic changes
- Implement fix in development first

### Critical Data Loss
**Problem:** Important monitoring data has been lost.

**Recovery Actions:**
1. **Check Backups:**
   ```bash
   # List available backups
   ls -la /backup/
   
   # Check database dumps
   ls -la /backup/mysql/
   ```

2. **Restore from Backup:**
   ```bash
   # Restore database
   mysql -u username -p database_name < backup_file.sql
   ```

3. **Recreate Lost Data:**
   - Re-add devices
   - Restore user accounts
   - Reconfigure settings

**Prevention:**
- Implement regular automated backups
- Test backup restoration procedures
- Store backups in multiple locations
- Document recovery procedures

## 📞 Getting Help

### Community Resources
- **Laravel Documentation:** https://laravel.com/docs
- **Tailwind CSS Docs:** https://tailwindcss.com/docs
- **Python Official Docs:** https://docs.python.org
- **Stack Overflow:** Search for specific error messages

### Professional Support
- **Laravel Forge:** Server management
- **Laravel Envoyer:** Deployment tool
- **Consulting Partners:** For enterprise support

### Issue Reporting
When reporting issues, include:
1. **Detailed Description:** What happened and when
2. **Steps to Reproduce:** Exact steps that cause the issue
3. **Error Messages:** Complete error text
4. **Environment Information:** 
   - OS version
   - PHP version
   - Database version
   - Browser information
5. **Screenshots:** If applicable
6. **Log Excerpts:** Relevant portions of log files

## 📚 Additional Resources

### Useful Commands
```bash
# System maintenance
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize

# Database maintenance
php artisan migrate:status
php artisan migrate:fresh --seed

# Monitoring
php artisan monitor:devices
php artisan schedule:run

# Logs
tail -f storage/logs/laravel.log
```

### Configuration Files to Check
- `.env` - Environment configuration
- `config/database.php` - Database settings
- `config/logging.php` - Logging configuration
- `config/mail.php` - Email settings
- `php.ini` - PHP configuration
- Web server config (Apache/Nginx)

### Monitoring Checklist
Regular maintenance tasks:
- [ ] Check disk space weekly
- [ ] Review logs daily
- [ ] Update dependencies monthly
- [ ] Test backups quarterly
- [ ] Review security monthly
- [ ] Performance tuning as needed
- [ ] Database optimization quarterly