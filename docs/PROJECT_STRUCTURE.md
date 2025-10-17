# 📁 Documentation: Project Structure

## 📁 Directory Structure Overview

```
monitoring-konektivitas/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── MonitorDevices.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   └── DeviceController.php
│   │   │   ├── AlertController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── DeviceController.php
│   │   │   ├── ReportController.php
│   │   │   └── ...
│   │   └── Resources/
│   │       └── DeviceResource.php
│   ├── Models/
│   │   ├── Alert.php
│   │   ├── Device.php
│   │   ├── DeviceLog.php
│   │   └── User.php
│   ├── Providers/
│   └── Services/
│       └── DeviceMonitoringService.php
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── DeviceSeeder.php
│       └── RolePermissionSeeder.php
├── public/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│       ├── alerts/
│       ├── auth/
│       ├── devices/
│       ├── layouts/
│       ├── profile/
│       ├── reports/
│       ├── dashboard.blade.php
│       └── welcome.blade.php
├── routes/
│   ├── api.php
│   ├── console.php
│   └── web.php
├── scripts/
│   ├── monitor.py
│   └── README.md
├── storage/
├── tests/
└── vendor/
```

## 📁 Key Files and Directories

### app/Console/Commands/MonitorDevices.php
Command Artisan untuk menjalankan monitoring perangkat secara berkala.

### app/Http/Controllers/Api/DeviceController.php
API controller untuk interaksi dengan script monitoring Python.

### app/Http/Controllers/DashboardController.php
Controller untuk halaman dashboard dengan statistik real-time.

### app/Http/Controllers/DeviceController.php
Controller untuk manajemen perangkat (CRUD).

### app/Http/Controllers/AlertController.php
Controller untuk manajemen peringatan.

### app/Http/Controllers/ReportController.php
Controller untuk laporan dan ekspor PDF.

### app/Models/Device.php
Model Eloquent untuk perangkat jaringan dengan relasi hirarkis.

### app/Models/DeviceLog.php
Model Eloquent untuk log status perangkat.

### app/Models/Alert.php
Model Eloquent untuk peringatan sistem.

### app/Services/DeviceMonitoringService.php
Service class untuk logika monitoring perangkat.

### database/migrations/
Berisi migrasi database untuk tabel devices, device_logs, alerts, dan users.

### database/seeders/RolePermissionSeeder.php
Seeder untuk membuat role dan permission default (Admin dan Petugas).

### database/seeders/DeviceSeeder.php
Seeder untuk membuat contoh data perangkat.

### resources/views/
Berisi template Blade untuk semua halaman aplikasi.

### routes/
Definisi rute untuk web dan API.

### scripts/monitor.py
Script Python untuk monitoring jaringan yang berinteraksi dengan API Laravel.

## 🔧 Architecture Overview

### MVC Pattern
Mengikuti pola arsitektur Model-View-Controller untuk pemisahan tanggung jawab.

### API-First Approach
Backend dirancang dengan pendekatan API-first sehingga mudah diintegrasikan dengan berbagai client.

### Role-Based Access Control
Menggunakan Spatie Laravel Permission untuk manajemen role dan permission pengguna.

### Service Layer
Logika bisnis dipisahkan ke dalam service class untuk meningkatkan maintainability.

### Resource API
Menggunakan Laravel API Resources untuk transformasi data yang konsisten.

## 🔄 Data Flow

1. **User Interface** → Laravel Controllers → Models → Database
2. **Python Script** → API Endpoints → Laravel Controllers → Models → Database
3. **Scheduled Jobs** → Artisan Commands → Service Classes → Models → Database

## 📊 Database Relationships

### Devices Table
- Self-referencing relationship untuk struktur hirarkis
- One-to-many relationship dengan device_logs
- One-to-many relationship dengan alerts

### Users Table
- Many-to-many relationship dengan roles (Spatie Laravel Permission)
- Many-to-many relationship dengan permissions (Spatie Laravel Permission)

## 🛡️ Security Implementation

### Authentication
- Laravel Breeze untuk autentikasi dasar
- Session-based authentication untuk web interface

### Authorization
- Role-based access control dengan Spatie Laravel Permission
- Policy-based authorization untuk kontrol akses granular

### API Security
- CSRF protection untuk endpoint web
- Rate limiting untuk mencegah abuse
- Input validation untuk semua request

## 🎨 Frontend Architecture

### Blade Templates
- Master layout dengan sections untuk content dinamis
- Component-based approach untuk reusable UI elements

### Tailwind CSS
- Utility-first CSS framework untuk styling konsisten
- Responsive design untuk semua ukuran layar
- Dark mode support dengan toggle

### JavaScript Enhancement
- Alpine.js untuk interaktivitas minimal
- Vanilla JavaScript untuk AJAX requests
- Chart.js untuk visualisasi data

## 📈 Monitoring Architecture

### Python Monitoring Script
- Ping-based device checking
- HTTP fallback for unreachable devices
- RESTful API communication with Laravel backend

### Laravel Command Scheduler & Real-time Monitoring
- Artisan command scheduling untuk monitoring berkala
- Per-second monitoring jobs for true real-time updates
- Queue-based processing untuk scalability
- WebSocket broadcasting untuk UI updates instan
- Support untuk status UP/DOWN/UNKNOWN
- Logging dan error handling yang komprehensif

## 📤 Reporting System

### PDF Generation
- DomPDF untuk laporan PDF
- Template-based reports dengan styling konsisten

### Data Export
- CSV export untuk data mentah
- Filterable reports berdasarkan tanggal dan perangkat

## 🧪 Testing Strategy

### Unit Tests
- Model factories untuk data test
- Service class unit tests
- API endpoint tests

### Feature Tests
- Browser-based tests dengan Laravel Dusk (opsional)
- Integration tests untuk alur kerja kompleks

### Monitoring Tests
- Python script test suite
- API integration tests

## 🚀 Deployment Considerations

### Server Requirements
- PHP 8.2+ dengan extensions yang diperlukan
- Database server (MySQL/MariaDB)
- Web server (Apache/Nginx)
- Python 3.6+ untuk monitoring script

### Environment Configuration
- .env file untuk konfigurasi lingkungan
- Separate configurations untuk development, staging, dan production

### Scaling Considerations
- Database indexing untuk query performance
- Caching untuk data yang sering diakses
- Queue workers untuk proses background

### Backup Strategy
- Database backup scheduling
- File storage backup
- Configuration version control

## 📦 Package Dependencies

### Core Laravel Packages
- laravel/framework: Main framework
- laravel/tinker: Artisan REPL
- laravel/sanctum: API authentication

### Third-Party Packages
- spatie/laravel-permission: Role and permission management
- barryvdh/laravel-dompdf: PDF generation
- guzzlehttp/guzzle: HTTP client for API requests

### Development Packages
- fakerphp/faker: Test data generation
- mockery/mockery: Mocking framework
- phpunit/phpunit: Testing framework

## 🔄 CI/CD Pipeline

### Development Workflow
1. Feature branches for new development
2. Pull requests with code review
3. Automated testing on each commit
4. Staging deployment for QA
5. Production deployment with rollback capability

### Deployment Automation
- Deployment scripts untuk setup awal
- Database migration automation
- Configuration management
- Health checks post-deployment