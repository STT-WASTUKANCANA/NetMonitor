# 💻 Developer Documentation

Technical documentation for developers working on the Network Monitoring System.

## 🏗️ System Architecture

### Technology Stack
- **Backend:** Laravel 12 (PHP 8.2)
- **Frontend:** Blade templates with Tailwind CSS 4.0
- **Database:** MySQL/MariaDB
- **Monitoring:** Python 3.6+ script
- **Authentication:** Laravel Breeze + Spatie Laravel Permission
- **PDF Generation:** DomPDF
- **Queue System:** Redis (recommended) or Database

### High-Level Architecture
```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Web Browser   │◄──►│  Laravel (PHP)   │◄──►│    Database     │
└─────────────────┘    └──────────────────┘    └─────────────────┘
                              │                       ▲
                              │ API Calls             │
                              ▼                       │
                       ┌──────────────────┐           │
                       │ Python Monitoring│           │
                       │      Script      │◄──────────┘
                       └──────────────────┘
```

## 📁 Project Structure

```
app/
├── Console/Commands/        # Artisan commands
├── Http/
│   ├── Controllers/         # MVC controllers
│   │   └── Api/            # API controllers
│   └── Resources/          # API resources
├── Models/                  # Eloquent models
├── Services/               # Business logic services
└── Providers/              # Service providers

database/
├── migrations/             # Database schema changes
└── seeders/                # Sample data generators

resources/
├── views/                  # Blade templates
├── css/                    # Compiled CSS
└── js/                     # JavaScript files

routes/
├── web.php                 # Web routes
├── api.php                 # API routes
└── console.php             # Console routes

scripts/
└── monitor.py             # Python monitoring script
```

## 🧩 Core Components

### Models

#### Device Model
```php
// app/Models/Device.php
class Device extends Model
{
    protected $fillable = [
        'name', 'ip_address', 'type', 'hierarchy_level',
        'parent_id', 'location', 'description',
        'status', 'last_checked_at', 'is_active'
    ];
    
    // Relationships
    public function parent() { /* BelongsTo */ }
    public function children() { /* HasMany */ }
    public function logs() { /* HasMany */ }
    public function alerts() { /* HasMany */ }
}
```

#### DeviceLog Model
```php
// app/Models/DeviceLog.php
class DeviceLog extends Model
{
    protected $fillable = [
        'device_id', 'response_time', 'status', 'checked_at'
    ];
    
    protected $casts = [
        'response_time' => 'float',
        'checked_at' => 'datetime'
    ];
    
    // Relationships
    public function device() { /* BelongsTo */ }
}
```

#### Alert Model
```php
// app/Models/Alert.php
class Alert extends Model
{
    protected $fillable = [
        'device_id', 'message', 'status', 'resolved_at'
    ];
    
    protected $casts = [
        'resolved_at' => 'datetime'
    ];
    
    // Relationships
    public function device() { /* BelongsTo */ }
}
```

### Services

#### DeviceMonitoringService
```php
// app/Services/DeviceMonitoringService.php
class DeviceMonitoringService
{
    public function checkDeviceStatus(Device $device) { /* Implementation */ }
    public function checkAllDevices() { /* Implementation */ }
    private function checkForAlert(Device $device, string $newStatus) { /* Implementation */ }
}
```

### Controllers

#### API Device Controller
```php
// app/Http/Controllers/Api/DeviceController.php
class DeviceController extends Controller
{
    public function index() { /* Get all devices */ }
    public function show($id) { /* Get specific device */ }
    public function recordStatus(Request $request, $id) { /* Record device status */ }
}
```

## 🔄 Data Flow

### Device Monitoring Process
1. **Scheduler Trigger:** `monitor:devices` command runs
2. **Service Initialization:** DeviceMonitoringService instantiated
3. **Device Retrieval:** Get all active devices from database
4. **Connectivity Check:** Ping each device
5. **Status Recording:** Store results in DeviceLog
6. **Alert Generation:** Create alerts for status changes
7. **Hierarchical Update:** Cascade status to child devices
8. **Notification:** Send system notifications (future feature)

### API Communication
1. **Python Script:** Makes HTTP POST to `/api/devices/{id}/status`
2. **API Route:** Routes to DeviceController@recordStatus
3. **Validation:** Request data validated
4. **Database Update:** Device and DeviceLog records updated
5. **Alert Processing:** Check for status changes and create alerts
6. **Response:** JSON response with success/failure

## 🔐 Authentication & Authorization

### Roles
- **Admin:** Full system access
- **Petugas:** Limited operational access

### Permissions
Permissions are managed through Spatie Laravel Permission:
- `view devices`
- `create devices`
- `edit devices`
- `delete devices`
- `view alerts`
- `resolve alerts`
- `view reports`
- `generate reports`
- `view settings` (Admin only)
- `edit settings` (Admin only)

### Middleware
```php
// Route protection examples
Route::middleware(['auth', 'role:Admin'])->group(function () {
    // Admin-only routes
});

Route::middleware(['auth', 'permission:view devices'])->group(function () {
    // Device management routes
});
```

## 🎨 Frontend Development

### Blade Templates
Templates use Laravel Blade syntax with Tailwind CSS classes:
```blade
{{-- resources/views/devices/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="bg-white dark:bg-gray-800 shadow rounded-lg">
    <div class="p-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
            {{ __('Devices') }}
        </h2>
        <!-- Device listing -->
    </div>
</div>
@endsection
```

### Dark Mode Support
Dark mode is implemented with Tailwind's dark variant:
```html
<!-- Toggle button -->
<button id="dark-mode-toggle">
    <svg class="dark:hidden">...</svg>
    <svg class="hidden dark:block">...</svg>
</button>

<!-- Dark mode enabled element -->
<div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
    Content
</div>
```

### JavaScript Enhancements
Vanilla JavaScript and Alpine.js for interactivity:
```javascript
// Real-time dashboard updates
setInterval(() => {
    fetch('/dashboard/realtime')
        .then(response => response.json())
        .then(data => updateDashboard(data));
}, 30000);
```

## 🐍 Python Monitoring Script

### Script Architecture
```python
class NetworkMonitor:
    def __init__(self, api_base_url, api_token=None):
        self.api_base_url = api_base_url
        self.headers = {'Content-Type': 'application/json'}
    
    def get_devices(self):  # Fetch devices from API
    def ping_device(self, ip_address):  # Ping connectivity check
    def check_port(self, ip_address, port):  # Port availability check
    def check_device(self, device):  # Combined device check
    def report_status(self, device_id, status, response_time, message):  # Report to API
    def run_monitoring_cycle(self):  # Execute complete monitoring
```

### External Dependencies
```python
import requests      # HTTP client
import subprocess    # System command execution
import time          # Timing functions
import json          # JSON processing
```

## 📊 Database Schema

### Devices Table
```sql
CREATE TABLE devices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    type ENUM('router', 'switch', 'access_point', 'server', 'other') DEFAULT 'other',
    hierarchy_level ENUM('utama', 'sub', 'device') DEFAULT 'device',
    parent_id BIGINT UNSIGNED NULL,
    location VARCHAR(255) NULL,
    description TEXT NULL,
    status ENUM('up', 'down', 'unknown') DEFAULT 'unknown',
    last_checked_at TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES devices(id) ON DELETE SET NULL
);
```

### Device Logs Table
```sql
CREATE TABLE device_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    device_id BIGINT UNSIGNED NOT NULL,
    response_time FLOAT NULL,
    status ENUM('up', 'down') NOT NULL,
    checked_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (device_id) REFERENCES devices(id) ON DELETE CASCADE
);
```

### Alerts Table
```sql
CREATE TABLE alerts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    device_id BIGINT UNSIGNED NOT NULL,
    message TEXT NULL,
    status ENUM('active', 'resolved') DEFAULT 'active',
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (device_id) REFERENCES devices(id) ON DELETE CASCADE
);
```

## 🧪 Testing

### PHPUnit Tests
```php
// tests/Feature/DeviceApiTest.php
class DeviceApiTest extends TestCase
{
    public function test_can_get_devices()
    {
        $response = $this->get('/api/devices');
        $response->assertStatus(200);
    }
    
    public function test_can_record_device_status()
    {
        $device = Device::factory()->create();
        $response = $this->post("/api/devices/{$device->id}/status", [
            'status' => 'up',
            'response_time' => 15.5
        ]);
        $response->assertStatus(201);
    }
}
```

### Python Script Tests
```python
# tests/test_monitor.py
import unittest
from unittest.mock import patch, MagicMock
from scripts.monitor import NetworkMonitor

class TestNetworkMonitor(unittest.TestCase):
    def setUp(self):
        self.monitor = NetworkMonitor("http://localhost:8000")
    
    @patch('requests.get')
    def test_get_devices_success(self, mock_get):
        mock_response = MagicMock()
        mock_response.status_code = 200
        mock_response.json.return_value = [{'id': 1, 'ip_address': '192.168.1.1'}]
        mock_get.return_value = mock_response
        
        devices = self.monitor.get_devices()
        self.assertEqual(len(devices), 1)
```

## 🚀 Deployment

### Server Requirements
- PHP 8.2+
- MySQL 5.7+ or MariaDB 10.2+
- Apache 2.4+ or Nginx
- Composer
- Node.js and npm
- Python 3.6+ (for monitoring script)

### Environment Configuration
Create `.env` file:
```env
APP_NAME="Network Monitoring"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=monitoring
DB_USERNAME=your_username
DB_PASSWORD=your_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

### Deployment Steps
1. Clone repository
2. Install PHP dependencies: `composer install --no-dev`
3. Install Node dependencies: `npm install`
4. Build assets: `npm run build`
5. Generate application key: `php artisan key:generate`
6. Run migrations: `php artisan migrate`
7. Seed database: `php artisan db:seed`
8. Set up cron job for monitoring
9. Configure web server (Apache/Nginx)

### Cron Job Setup
```bash
# Add to crontab for monitoring every 5 minutes
*/5 * * * * cd /path/to/project && php artisan monitor:devices >> /dev/null 2>&1
```

## 🔧 Maintenance

### Database Optimization
```bash
# Optimize database tables
php artisan optimize

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Database maintenance
php artisan migrate:status
php artisan migrate:fresh --seed
```

### Log Management
```bash
# Rotate logs
logrotate /etc/logrotate.d/laravel-monitoring

# Monitor logs
tail -f storage/logs/laravel.log
```

### Performance Monitoring
Key metrics to monitor:
- Database query performance
- API response times
- Memory usage
- Disk space utilization
- CPU usage during monitoring cycles

## 🆕 Future Enhancements

### Planned Features
1. **Real-time Notifications:** WebSocket-based live updates
2. **Multi-tenancy:** Support for multiple organizations
3. **Advanced Analytics:** Machine learning-based anomaly detection
4. **Mobile App:** Native mobile application
5. **SNMP Integration:** Protocol-based device monitoring
6. **SLA Tracking:** Service level agreement compliance monitoring
7. **Integration Hub:** Connectors for third-party systems

### API Improvements
1. **Versioning:** API version management
2. **Rate Limiting:** Enhanced throttling controls
3. **Pagination:** Standardized pagination across endpoints
4. **Filtering:** Advanced query filtering capabilities
5. **Webhooks:** Event-driven notification system

### Security Enhancements
1. **OAuth2:** Third-party authentication
2. **Audit Logging:** Comprehensive system activity tracking
3. **Data Encryption:** At-rest encryption for sensitive data
4. **Compliance:** GDPR/HIPAA compliance features

## 🤝 Contributing

### Development Workflow
1. Fork the repository
2. Create feature branch
3. Make changes
4. Write/update tests
5. Submit pull request

### Code Standards
Follow PSR-12 coding standards:
```bash
# Code formatting
./vendor/bin/phpcs --standard=PSR12 app/

# Code fixing
./vendor/bin/phpcbf --standard=PSR12 app/
```

### Pull Request Guidelines
1. Include comprehensive description
2. Reference related issues
3. Include tests for new functionality
4. Update documentation as needed
5. Follow semantic versioning

## 📞 Support

For development questions or issues:
1. Check existing documentation
2. Search issue tracker
3. Create new issue with detailed description
4. Include steps to reproduce
5. Provide system/environment information