# 🗄️ Database Setup - NetMonitor

Panduan setup database MySQL/MariaDB untuk **NetMonitor**.

---

## 📋 Prerequisites

- MySQL 8.0+ atau MariaDB 10.6+
- User dengan priviledge CREATE DATABASE

---

## 🛠️ Setup Database

### 1. Login ke MySQL

```bash
mysql -u root -p
```

### 2. Create Database

```sql
CREATE DATABASE netmonitor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Create User (Optional)

```sql
CREATE USER 'netmonitor_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON netmonitor.* TO 'netmonitor_user'@'localhost';
FLUSH PRIVILEGES;
```

### 4. Create Tables

```sql
USE netmonitor;

-- Users table
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petugas') NOT NULL DEFAULT 'petugas',
  `profile_photo` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `idx_users_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Devices table
CREATE TABLE `devices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `type` enum('router','switch','access_point','server','firewall','other') NOT NULL,
  `hierarchy_level` enum('utama','sub','device') NOT NULL DEFAULT 'device',
  `parent_id` bigint unsigned DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `description` text,
  `port` int DEFAULT NULL,
  `status` enum('up','down','unknown') NOT NULL DEFAULT 'unknown',
  `last_checked_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `devices_ip_address_unique` (`ip_address`),
  KEY `idx_devices_status` (`status`),
  KEY `idx_devices_parent_id` (`parent_id`),
  KEY `idx_devices_hierarchy` (`hierarchy_level`),
  KEY `idx_devices_type` (`type`),
  KEY `fk_devices_created_by` (`created_by`),
  CONSTRAINT `fk_devices_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_devices_parent` FOREIGN KEY (`parent_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Device logs table
CREATE TABLE `device_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `device_id` bigint unsigned NOT NULL,
  `status` enum('up','down') NOT NULL,
  `response_time` decimal(8,2) DEFAULT NULL,
  `packet_loss` decimal(5,2) DEFAULT NULL,
  `checked_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_logs_device_checked` (`device_id`,`checked_at`),
  KEY `idx_logs_checked_at` (`checked_at`),
  CONSTRAINT `fk_logs_device` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Alerts table
CREATE TABLE `alerts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `device_id` bigint unsigned NOT NULL,
  `message` text NOT NULL,
  `severity` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `status` enum('active','acknowledged','resolved') NOT NULL DEFAULT 'active',
  `resolved_at` timestamp NULL DEFAULT NULL,
  `resolved_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_alerts_device_status` (`device_id`,`status`),
  KEY `idx_alerts_severity` (`severity`),
  KEY `idx_alerts_status` (`status`),
  KEY `fk_alerts_resolved_by` (`resolved_by`),
  CONSTRAINT `fk_alerts_device` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_alerts_resolved_by` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 5. Create Admin User

```sql
-- Insert admin user with bcrypt hashed password
-- Password: password123 (you should change this!)
INSERT INTO users (first_name, last_name, email, password, role, created_at, updated_at)
VALUES (
  'Admin',
  'NetMonitor',
  'admin@netmonitor.local',
  '$2b$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4/GvKqJGQvFYzJYa',
  'admin',
  NOW(),
  NOW()
);
```

> **Note**: Password hash di atas adalah untuk password `password123`. Untuk generate hash baru:

```python
from passlib.context import CryptContext
pwd = CryptContext(schemes=["bcrypt"])
print(pwd.hash("your_password_here"))
```

### 6. Insert Sample Device (Optional)

```sql
INSERT INTO devices (name, ip_address, type, hierarchy_level, location, status, created_by, created_at, updated_at)
VALUES (
  'Router Utama',
  '192.168.1.1',
  'router',
  'utama',
  'Ruang Server',
  'unknown',
  1,
  NOW(),
  NOW()
);
```

---

## ⚙️ Configure .env

Update `.env` file dengan database credentials:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=netmonitor
DB_USER=root
DB_PASSWORD=your_password
```

---

## ✅ Verify Connection

```bash
# Test database connection
cd /path/to/NetMonitor
source venv/bin/activate
python -c "from app.database import check_database_connection; print('OK' if check_database_connection() else 'FAIL')"
```

---

## 📝 Notes

- Jika sudah punya database dari Laravel, **skip langkah Create Tables** - schema sama
- Password hash bcrypt kompatibel antara Laravel dan Python (passlib)
- Pastikan timezone MySQL sesuai (Asia/Jakarta untuk WIB)

---

**Versi**: 2.0  
**Terakhir Diperbarui**: 11 Desember 2025
