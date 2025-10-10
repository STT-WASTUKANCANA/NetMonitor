# 🚀 Installation Guide

Complete installation guide for the Network Monitoring System.

## 📋 System Requirements

### Server Requirements
- **Operating System:** Linux (Ubuntu 20.04+/22.04+, CentOS 8+, Debian 11+) or Windows Server
- **Web Server:** Apache 2.4+ or Nginx 1.18+
- **PHP:** 8.2 or higher
- **Database:** MySQL 8.0+ or MariaDB 10.6+
- **Memory:** 2GB RAM minimum (4GB recommended)
- **Storage:** 10GB free disk space minimum
- **Network:** Static IP address recommended

### Software Dependencies
- **Composer:** 2.2+ for PHP dependency management
- **Node.js:** 16+ for frontend asset compilation
- **npm:** 8+ for Node.js package management
- **Python:** 3.6+ for monitoring script
- **Git:** For version control and deployment

### Optional Components
- **Redis:** For queue processing and caching
- **Supervisor:** For process monitoring
- **SSL Certificate:** For HTTPS (recommended)

## 🛠️ Installation Steps

### Step 1: Prepare the Server

#### Update System Packages
```bash
# Ubuntu/Debian
sudo apt update && sudo apt upgrade -y

# CentOS/RHEL
sudo yum update -y
# or
sudo dnf update -y
```

#### Install Required Packages
```bash
# Ubuntu/Debian
sudo apt install -y git curl wget unzip software-properties-common

# Install PHP 8.2 and extensions
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2 php8.2-cli php8.2-common php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath php8.2-intl php8.2-soap

# Install other components
sudo apt install -y apache2 mysql-server nodejs npm python3 python3-pip
```

#### Install Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Step 2: Configure Database

#### Start MySQL Service
```bash
sudo systemctl start mysql
sudo systemctl enable mysql
```

#### Secure MySQL Installation
```bash
sudo mysql_secure_installation
```

#### Create Database and User
```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE monitoring_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'monitor_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';
GRANT ALL PRIVILEGES ON monitoring_system.* TO 'monitor_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 3: Deploy Application

#### Clone Repository
```bash
cd /var/www
sudo git clone https://github.com/your-repo/monitoring-konektivitas.git
sudo chown -R www-data:www-data monitoring-konektivitas
cd monitoring-konektivitas
```

#### Install PHP Dependencies
```bash
composer install --no-dev --optimize-autoloader
```

#### Install Node Dependencies
```bash
npm install
npm run build
```

### Step 4: Configure Application

#### Create Environment File
```bash
cp .env.example .env
```

#### Edit Environment Configuration
```bash
nano .env
```

Configure the following essential settings:
```env
APP_NAME="Network Monitoring System"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=monitoring_system
DB_USERNAME=monitor_user
DB_PASSWORD=YOUR_STRONG_PASSWORD

LOG_CHANNEL=stack
LOG_LEVEL=debug

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

#### Generate Application Key
```bash
php artisan key:generate
```

### Step 5: Database Migration and Seeding

#### Run Migrations
```bash
php artisan migrate
```

#### Seed Database
```bash
php artisan db:seed
```

### Step 6: Configure Web Server

#### Apache Configuration
Create virtual host file:
```bash
sudo nano /etc/apache2/sites-available/monitoring.conf
```

Add the following configuration:
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    ServerAlias www.your-domain.com
    DocumentRoot /var/www/monitoring-konektivitas/public

    <Directory /var/www/monitoring-konektivitas/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/monitoring_error.log
    CustomLog ${APACHE_LOG_DIR}/monitoring_access.log combined
</VirtualHost>
```

Enable the site and required modules:
```bash
sudo a2ensite monitoring.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### Nginx Configuration (Alternative)
Create configuration file:
```bash
sudo nano /etc/nginx/sites-available/monitoring
```

Add the following configuration:
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/monitoring-konektivitas/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/monitoring /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### Step 7: Set File Permissions

```bash
sudo chown -R www-data:www-data /var/www/monitoring-konektivitas
sudo chmod -R 755 /var/www/monitoring-konektivitas
sudo chmod -R 775 /var/www/monitoring-konektivitas/storage
sudo chmod -R 775 /var/www/monitoring-konektivitas/bootstrap/cache
```

### Step 8: Configure SSL (Recommended)

#### Install Certbot
```bash
# Ubuntu/Debian
sudo apt install certbot python3-certbot-apache -y
# or for Nginx
sudo apt install certbot python3-certbot-nginx -y
```

#### Obtain SSL Certificate
```bash
# For Apache
sudo certbot --apache -d your-domain.com -d www.your-domain.com

# For Nginx
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

### Step 9: Configure Monitoring Script

#### Install Python Dependencies
```bash
cd /var/www/monitoring-konektivitas/scripts
pip3 install requests
```

#### Test Script Execution
```bash
python3 monitor.py
```

#### Make Script Executable
```bash
chmod +x /var/www/monitoring-konektivitas/scripts/monitor.py
```

### Step 10: Set Up Scheduled Tasks

#### Configure Cron Job
```bash
sudo crontab -e
```

Add the following lines:
```bash
# Laravel scheduler
* * * * * cd /var/www/monitoring-konektivitas && php artisan schedule:run >> /dev/null 2>&1

# Network monitoring every 5 minutes
*/5 * * * * cd /var/www/monitoring-konektivitas && python3 scripts/monitor.py >> /var/log/monitor.log 2>&1
```

#### Set Up Log Rotation
```bash
sudo nano /etc/logrotate.d/monitoring
```

Add the following configuration:
```
/var/log/monitor.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
}
```

### Step 11: Verify Installation

#### Check Application Health
Visit your domain in a web browser:
```
http://your-domain.com
```

#### Test API Endpoints
```bash
curl -X GET http://your-domain.com/api/devices
```

#### Check Monitoring Script
```bash
cd /var/www/monitoring-konektivitas
python3 scripts/monitor.py
```

## 🔧 Post-Installation Configuration

### Configure Admin Account
1. Visit the application in your browser
2. Click "Login" and use default credentials:
   - Email: `admin@sttwastukancana.ac.id`
   - Password: `password`
3. Change password immediately after login

### Configure Devices
1. Navigate to "Devices" section
2. Add your network devices:
   - Routers
   - Switches
   - Access Points
   - Servers
3. Set up device hierarchy:
   - Parent-child relationships
   - Device types and locations

### Configure Notifications (Optional)
1. Set up email configuration in `.env`:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.your-provider.com
   MAIL_PORT=587
   MAIL_USERNAME=your-email@domain.com
   MAIL_PASSWORD=your-email-password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=monitoring@your-domain.com
   MAIL_FROM_NAME="Network Monitoring"
   ```

### Configure Backup (Recommended)
Set up automated backups for both database and application files.

## 🛡️ Security Hardening

### File Permissions
Ensure proper file permissions:
```bash
# Application files
sudo chown -R www-data:www-data /var/www/monitoring-konektivitas
sudo find /var/www/monitoring-konektivitas -type d -exec chmod 755 {} \;
sudo find /var/www/monitoring-konektivitas -type f -exec chmod 644 {} \;

# Storage directories
sudo chmod -R 775 /var/www/monitoring-konektivitas/storage
sudo chmod -R 775 /var/www/monitoring-konektivitas/bootstrap/cache
```

### Hide Sensitive Information
1. Disable debug mode in production:
   ```env
   APP_DEBUG=false
   ```

2. Configure proper error pages:
   ```bash
   # Apache
   sudo a2dissite 000-default
   sudo systemctl reload apache2
   
   # Nginx
   sudo rm /etc/nginx/sites-enabled/default
   sudo systemctl reload nginx
   ```

### Configure Firewall
```bash
# UFW (Ubuntu)
sudo ufw allow ssh
sudo ufw allow 'Apache Full'  # or 'Nginx Full'
sudo ufw --force enable

# Or with specific ports
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp     # HTTP
sudo ufw allow 443/tcp    # HTTPS
sudo ufw --force enable
```

## 📊 Performance Optimization

### Enable OPcache
Edit PHP configuration:
```bash
sudo nano /etc/php/8.2/apache2/php.ini
# or for Nginx with FPM
sudo nano /etc/php/8.2/fpm/php.ini
```

Add or modify OPcache settings:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=12
opcache.max_accelerated_files=4000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

Restart web server:
```bash
# Apache
sudo systemctl restart apache2

# Nginx with PHP-FPM
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

### Configure Database Optimization
Edit MySQL configuration:
```bash
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

Add optimization settings:
```ini
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
query_cache_type = 1
query_cache_size = 128M
```

Restart MySQL:
```bash
sudo systemctl restart mysql
```

## 🔄 Maintenance Schedule

### Daily Tasks
```bash
# Check system resources
df -h
free -h
top -b -n 1 | head -20

# Check logs for errors
tail -n 100 /var/log/apache2/error.log | grep -i error
tail -n 100 /var/log/mysql/error.log | grep -i error
tail -n 100 /var/www/monitoring-konektivitas/storage/logs/laravel.log | grep -i error
```

### Weekly Tasks
```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Check disk usage
du -sh /var/www/monitoring-konektivitas/storage/logs/

# Optimize database
mysql -u monitor_user -p monitoring_system -e "OPTIMIZE TABLE devices, device_logs, alerts;"
```

### Monthly Tasks
```bash
# Update application
cd /var/www/monitoring-konektivitas
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm install
npm run build
php artisan optimize:clear
php artisan optimize

# Restart services
sudo systemctl restart apache2  # or nginx and php-fpm
```

## 🆘 Troubleshooting Common Issues

### Application Not Loading
1. Check web server status:
   ```bash
   sudo systemctl status apache2  # or nginx
   ```

2. Check PHP-FPM (if using Nginx):
   ```bash
   sudo systemctl status php8.2-fpm
   ```

3. Check file permissions:
   ```bash
   ls -la /var/www/monitoring-konektivitas/public/
   ```

### Database Connection Failed
1. Check database service:
   ```bash
   sudo systemctl status mysql
   ```

2. Test database connection:
   ```bash
   mysql -u monitor_user -p monitoring_system
   ```

3. Verify database configuration in `.env`

### Monitoring Script Issues
1. Check script execution:
   ```bash
   cd /var/www/monitoring-konektivitas/scripts
   python3 monitor.py --debug
   ```

2. Check script logs:
   ```bash
   tail -f /var/log/monitor.log
   ```

3. Verify API connectivity:
   ```bash
   curl -X GET http://localhost/api/devices
   ```

## 📞 Support and Updates

### Getting Help
For issues not covered in this guide:
1. Check the [Troubleshooting Guide](TROUBLESHOOTING.md)
2. Review application logs:
   - `/var/log/apache2/error.log` or `/var/log/nginx/error.log`
   - `/var/log/mysql/error.log`
   - `/var/www/monitoring-konektivitas/storage/logs/laravel.log`
3. Consult community forums and documentation

### Keeping Updated
Regularly update the system:
```bash
# Update application code
cd /var/www/monitoring-konektivitas
git pull origin main

# Update dependencies
composer update
npm update

# Run migrations if needed
php artisan migrate --force

# Recompile assets
npm run build

# Clear caches
php artisan optimize:clear
php artisan optimize
```

## 📋 Checklist Summary

Before going live, ensure all these items are completed:

### ✅ Pre-Installation
- [ ] Server meets system requirements
- [ ] Required software packages installed
- [ ] Database server configured and running
- [ ] SSL certificate obtained (if using HTTPS)

### ✅ Installation
- [ ] Application files deployed
- [ ] PHP dependencies installed
- [ ] Node.js dependencies installed and assets compiled
- [ ] Environment configuration set up
- [ ] Database migrated and seeded
- [ ] Web server configured and running
- [ ] SSL configured (if applicable)

### ✅ Post-Installation
- [ ] File permissions set correctly
- [ ] Monitoring script tested and working
- [ ] Scheduled tasks configured
- [ ] Admin account configured
- [ ] Initial devices added to system
- [ ] Security hardening applied
- [ ] Performance optimizations implemented
- [ ] Backup strategy configured
- [ ] Monitoring verified as working

### ✅ Going Live
- [ ] Final testing completed
- [ ] Documentation reviewed and understood
- [ ] Support contacts established
- [ ] Maintenance schedule planned
- [ ] Monitoring alerts configured

Congratulations! Your Network Monitoring System is now installed and ready for use.