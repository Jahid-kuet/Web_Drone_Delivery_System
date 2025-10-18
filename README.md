# 🚁 Drone Delivery System

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://www.php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

A comprehensive web-based drone delivery management system for medical supply delivery in Bangladesh, with a focus on emergency priority handling, real-time GPS tracking, and secure delivery verification.

---

## 📋 Table of Contents

- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [System Requirements](#-system-requirements)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Database Setup](#-database-setup)
- [Running the Application](#-running-the-application)
- [Testing](#-testing)
- [User Roles & Credentials](#-user-roles--credentials)
- [API Documentation](#-api-documentation)
- [Project Structure](#-project-structure)
- [Key Features Details](#-key-features-details)
- [Scheduled Tasks](#-scheduled-tasks)
- [Deployment](#-deployment)
- [Troubleshooting](#-troubleshooting)
- [Contributing](#-contributing)
- [License](#-license)

---

## ✨ Features

### Core Functionality
- 🏥 **Multi-Role System**: Admin, Hospital Admin, Hospital Staff, Drone Operator
- 🚨 **Emergency Priority Queue**: Automatic delivery assignment based on urgency
- 📍 **Real-Time GPS Tracking**: Live drone position monitoring with altitude, speed, and heading
- 🔐 **OTP Verification**: Secure 6-digit one-time password for delivery confirmation
- 📸 **Digital Proof of Delivery**: Photo upload and digital signature capture
- 🔋 **Battery Management**: Automatic battery monitoring and low-battery alerts
- 🛠️ **Maintenance Scheduling**: Drone maintenance tracking and scheduling
- 📊 **Comprehensive Dashboard**: Role-based dashboards with real-time statistics
- 🔔 **Notification System**: Multi-user notification system with read/unread status
- 📱 **Public Tracking**: Track deliveries via tracking number without login
- 🇧🇩 **Bangladesh Localization**: Khulna-specific hub system and phone validation

### Advanced Features
- ⚡ **Auto-Assignment**: Deliveries automatically assigned every 5 minutes
- 🎯 **Smart Priority Scoring**: Emergency=100, Urgent=50, Normal=10
- 🏢 **Hub-Based Operations**: 3 operational hubs in Khulna (Central, Daulatpur, Khalishpur)
- 📦 **Inventory Management**: Hub-based medical supply inventory tracking
- 🔒 **Strong Validation**: Password strength, name validation, Bangladesh phone format
- 📜 **Audit Trail**: Complete tracking of who did what and when
- 🌐 **RESTful API**: Comprehensive API with 8+ endpoints for mobile integration

---

## 🛠️ Tech Stack

### Backend
- **Framework**: Laravel 12.x
- **Language**: PHP 8.2+
- **Database**: SQLite (development) / MySQL (production)
- **Queue**: Laravel Queue with database driver
- **Scheduler**: Laravel Task Scheduler

### Frontend
- **CSS Framework**: Tailwind CSS 3.x
- **JavaScript**: Alpine.js, Vanilla JS
- **Icons**: Font Awesome 6.x
- **Charts**: Chart.js
- **Build Tool**: Vite

### Additional Libraries
- **Pusher PHP Server**: Real-time broadcasting support
- **Laravel Tinker**: Interactive REPL
- **Faker**: Test data generation

---

## 💻 System Requirements

- **PHP**: 8.2 or higher
- **Composer**: 2.x
- **Node.js**: 18.x or higher
- **NPM**: 9.x or higher
- **Web Server**: Apache/Nginx (or PHP built-in server for development)
- **Database**: SQLite (dev) / MySQL 8.0+ (production)
- **Extensions**: 
  - BCMath
  - Ctype
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - Tokenizer
  - XML

---

## 📦 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/Jahid-kuet/Web_Drone_Delivery_System.git
cd Web_Drone_Delivery_System
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install JavaScript Dependencies
```bash
npm install
```

### 4. Environment Setup
```bash
# Copy the example environment file
copy .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Build Frontend Assets
```bash
# Development build
npm run dev

# Production build
npm run build
```

---

## ⚙️ Configuration

### 1. Edit `.env` File

```env
APP_NAME="Drone Delivery System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration (SQLite for development)
DB_CONNECTION=sqlite

# For MySQL (production)
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=drone_delivery
# DB_USERNAME=root
# DB_PASSWORD=your_password

# Queue Configuration
QUEUE_CONNECTION=database

# Cache Configuration
CACHE_STORE=database

# Session Configuration
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Mail Configuration (for production)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@dronedelivery.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 2. Storage Permissions
```bash
# Windows (Git Bash or PowerShell)
mkdir storage\app\public\delivery-proofs
mkdir storage\app\public\delivery-signatures
mkdir storage\framework\cache
mkdir storage\framework\sessions
mkdir storage\framework\views
mkdir storage\logs
```

### 3. Create Storage Symlink
```bash
php artisan storage:link
```

---

## 🗄️ Database Setup

### 1. Create Database File (SQLite)
```bash
# The file should already exist, but if not:
type nul > database\database.sqlite
```

### 2. Run Migrations
```bash
php artisan migrate
```

### 3. Seed Database
```bash
# Seed all data (users, hospitals, drones, hubs, etc.)
php artisan db:seed

# Or seed specific seeders
php artisan db:seed --class=HubSeeder
php artisan db:seed --class=DatabaseSeeder
```

### Database Schema Overview
- **users**: System users with roles
- **hospitals**: Hospital information and locations
- **drones**: Drone fleet with specifications
- **delivery_requests**: Hospital delivery requests
- **deliveries**: Active and completed deliveries
- **delivery_tracking**: Real-time GPS tracking data
- **hubs**: Operational hubs in Khulna
- **hub_inventories**: Medical supply inventory per hub
- **notifications**: User notification system

---

## 🚀 Running the Application

### Quick Start (Windows)
```bash
# Use the quick-start script
quick-start.bat
```

### Manual Start

#### Terminal 1: Laravel Development Server
```bash
php artisan serve
```

#### Terminal 2: Build Assets (if developing)
```bash
npm run dev
```

#### Terminal 3: Queue Worker (for background jobs)
```bash
php artisan queue:work
```

#### Terminal 4: Task Scheduler (for auto-assignment)
```bash
# In production, add to cron:
# * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1

# For development, run manually:
php artisan schedule:work
```

### Access the Application
- **Main URL**: http://127.0.0.1:8000
- **Login Page**: http://127.0.0.1:8000/login
- **Public Tracking**: http://127.0.0.1:8000/track

---

## 🧪 Testing

### Quick Test Script
```bash
# Run interactive testing menu
test-features.bat
```

### Manual Testing

#### Test Auto-Assignment
```bash
php artisan deliveries:auto-assign
```

#### Test OTP System
```bash
php artisan tinker

$delivery = App\Models\Delivery::first();
$otp = $delivery->generateOTP();
echo "Generated OTP: $otp\n";
$result = $delivery->verifyOTP($otp, 'Test User');
print_r($result);

exit
```

#### Run PHPUnit Tests
```bash
php artisan test
```

### API Testing (cURL Examples)

#### Generate OTP
```bash
curl -X POST http://127.0.0.1:8000/api/v1/deliveries/1/otp/generate ^
  -H "Authorization: Bearer YOUR_TOKEN" ^
  -H "Content-Type: application/json"
```

#### Upload Photo
```bash
curl -X POST http://127.0.0.1:8000/api/v1/deliveries/1/photo ^
  -H "Authorization: Bearer YOUR_TOKEN" ^
  -F "photo=@test_photo.jpg" ^
  -F "recipient_name=Dr. Ahmed Rahman" ^
  -F "recipient_phone=01711123456"
```

#### Track Delivery (Public)
```bash
curl -X GET http://127.0.0.1:8000/api/v1/public/track/TRK-2025-001
```

---

## 👥 User Roles & Credentials

### Default Test Accounts (After Seeding)

| Role | Email | Password | Access Level |
|------|-------|----------|--------------|
| **System Admin** | admin@drone.com | password123 | Full system access, user management, all operations |
| **Hospital Admin** | hospital@drone.com | password123 | Hospital operations, delivery requests, tracking |
| **Drone Operator** | operator@drone.com | password123 | Drone operations, delivery execution, status updates |

### Role Permissions

#### Admin
- Manage users, hospitals, drones
- Approve/reject delivery requests
- View all deliveries and statistics
- Send notifications to users
- Access system-wide reports

#### Hospital Admin/Staff
- Create delivery requests
- Track deliveries
- View delivery history
- Generate receipts
- Request emergency deliveries

#### Drone Operator
- View assigned deliveries
- Update delivery status
- Report drone battery levels
- Update GPS positions
- Complete deliveries with OTP/photo

---

## 📚 API Documentation

### Base URL
```
http://127.0.0.1:8000/api/v1
```

### Public Endpoints (No Authentication)

#### Track Delivery
```http
GET /public/track/{trackingNumber}
```

#### Real-Time Position
```http
GET /public/track/{trackingNumber}/realtime
```

### Authenticated Endpoints (Require Bearer Token)

#### OTP Management
```http
POST   /deliveries/{id}/otp/generate     # Generate OTP
POST   /deliveries/{id}/otp/verify       # Verify OTP
GET    /deliveries/{id}/otp/status       # Check OTP status
POST   /deliveries/{id}/otp/resend       # Resend OTP
```

#### Delivery Proof
```http
POST   /deliveries/{id}/photo            # Upload photo
POST   /deliveries/{id}/signature        # Upload signature
POST   /deliveries/{id}/confirm          # Complete confirmation
GET    /deliveries/{id}/confirmation     # Get confirmation details
```

#### Drone Management
```http
GET    /drones/{id}/status               # Get drone status
POST   /drones/{id}/battery              # Update battery level
POST   /drones/{id}/position             # Update GPS position
```

**Full API Documentation**: See [API_DOCUMENTATION.md](API_DOCUMENTATION.md)

---

## 📁 Project Structure

```
Drone_Delivery_System/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── AutoAssignDeliveries.php    # Auto-assignment command
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/                      # Admin controllers
│   │   │   ├── Api/                        # API controllers
│   │   │   ├── DeliveryConfirmationController.php
│   │   │   ├── HospitalPortalController.php
│   │   │   └── OperatorPortalController.php
│   │   └── Middleware/
│   ├── Models/
│   │   ├── Delivery.php                    # Core delivery model
│   │   ├── DeliveryRequest.php             # Request model
│   │   ├── DeliveryTracking.php            # GPS tracking model
│   │   ├── Drone.php                       # Drone fleet model
│   │   ├── Hospital.php                    # Hospital model
│   │   ├── Hub.php                         # Hub system model
│   │   ├── HubInventory.php                # Inventory model
│   │   └── User.php                        # User authentication
│   ├── Rules/
│   │   ├── BangladeshLocation.php          # Location validation
│   │   ├── StrongPassword.php              # Password rules
│   │   └── ValidName.php                   # Name validation
│   └── Services/
│       ├── BangladeshLocationService.php   # Location service
│       └── DeliveryPriorityQueue.php       # Priority queue system
├── database/
│   ├── migrations/                         # Database migrations
│   ├── seeders/
│   │   ├── DatabaseSeeder.php              # Main seeder
│   │   └── HubSeeder.php                   # Hub data seeder
│   └── database.sqlite                     # SQLite database file
├── public/                                 # Public assets
├── resources/
│   ├── css/                                # Stylesheets
│   ├── js/                                 # JavaScript files
│   └── views/                              # Blade templates
│       ├── admin/                          # Admin views
│       ├── hospital/                       # Hospital views
│       ├── operator/                       # Operator views
│       └── tracking/                       # Public tracking views
├── routes/
│   ├── web.php                             # Web routes
│   ├── api.php                             # API routes
│   └── console.php                         # Scheduled tasks
├── storage/
│   ├── app/
│   │   └── public/
│   │       ├── delivery-proofs/            # Delivery photos
│   │       └── delivery-signatures/        # Digital signatures
│   ├── framework/                          # Framework cache
│   └── logs/                               # Application logs
├── tests/                                  # PHPUnit tests
├── .env.example                            # Environment template
├── artisan                                 # Laravel CLI
├── composer.json                           # PHP dependencies
├── package.json                            # NPM dependencies
├── quick-start.bat                         # Quick start script
├── test-features.bat                       # Testing script
└── README.md                               # This file
```

---

## 🎯 Key Features Details

### 1. Emergency Priority Queue System

**Automatic Assignment Every 5 Minutes**

The system automatically assigns pending deliveries to available drones based on:
- **Priority Score**: Emergency=100, Urgent=50, Normal=10
- **Supply Type Weight**: Blood/Plasma gets 2.0x multiplier
- **Wait Time**: Older requests get priority boost
- **Drone Selection**: Best battery level, payload capacity, closest distance

**Emergency Alerts**: If an emergency delivery waits >15 minutes, alerts are logged.

```bash
# Manual trigger
php artisan deliveries:auto-assign

# Check only emergencies
php artisan deliveries:auto-assign --check-alerts
```

### 2. OTP Verification System

**Secure 6-digit one-time passwords for delivery confirmation**

- **Generation**: Random 6-digit code
- **Expiration**: 10 minutes
- **Resend**: Can request new OTP if expired
- **Audit**: Tracks who verified and when

### 3. Digital Proof of Delivery

**Photo + Signature + Recipient Info**

- Photo upload (JPEG/PNG, max 5MB)
- Digital signature capture (base64)
- Recipient name and phone
- Optional notes
- Organized storage structure

### 4. Real-Time GPS Tracking

**Comprehensive flight data tracking**

- Latitude/Longitude (8-11 decimal precision)
- Altitude, speed, heading
- Battery level monitoring
- Flight mode (manual, autopilot, GPS-guided, emergency)
- Sensor data (temperature, humidity)
- Weather data
- Signal strength and GPS lock status

### 5. Hub-Based Operations

**3 Operational Hubs in Khulna**

1. **Khulna Central Hub** (KHN-CENTRAL)
   - City: Khulna
   - Location: 22.8456, 89.5403
   
2. **Daulatpur Hub** (KHN-DAULATPUR)
   - City: Khulna
   - Location: 22.8670, 89.5289

3. **Khalishpur Hub** (KHN-KHALISHPUR)
   - City: Khulna
   - Location: 22.8100, 89.5600

Each hub maintains medical supply inventory and serves specific zones.

---

## ⏰ Scheduled Tasks

The system runs automatic tasks via Laravel Scheduler:

### Task Schedule

```php
// Auto-assign deliveries every 5 minutes
Schedule::command('deliveries:auto-assign')
    ->everyFiveMinutes()
    ->withoutOverlapping();

// Check emergency alerts every minute
Schedule::command('deliveries:auto-assign --check-alerts')
    ->everyMinute()
    ->withoutOverlapping();
```

### Setup Cron Job (Production)

Add this to your crontab:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### Development (Manual)
```bash
php artisan schedule:work
```

---

## 🚢 Deployment

### Production Checklist

#### 1. Environment Configuration
```bash
# Set production environment
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourproductiondomain.com

# Configure production database
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=drone_delivery
DB_USERNAME=db_user
DB_PASSWORD=secure_password

# Configure mail for OTP/notifications
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

#### 2. Optimize Application
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

#### 3. Build Assets
```bash
npm run build
```

#### 4. File Permissions
```bash
# Storage and cache directories must be writable
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 5. Web Server Configuration

**Nginx Example**:
```nginx
server {
    listen 80;
    server_name yourproductiondomain.com;
    root /var/www/drone-delivery/public;

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

    client_max_body_size 6M;
}
```

#### 6. Queue Worker (Supervisor)
```ini
[program:drone-delivery-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/drone-delivery/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/drone-delivery/storage/logs/worker.log
stopwaitsecs=3600
```

#### 7. SSL Certificate
```bash
# Using Let's Encrypt
certbot --nginx -d yourproductiondomain.com
```

#### 8. Setup Cron Job
```bash
* * * * * cd /var/www/drone-delivery && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔧 Troubleshooting

### Common Issues

#### 1. "Class not found" Error
```bash
composer dump-autoload
```

#### 2. Storage Permission Denied
```bash
# Windows (Run as Administrator)
icacls storage /grant Users:F /t
icacls bootstrap\cache /grant Users:F /t

# Linux/Mac
chmod -R 775 storage bootstrap/cache
```

#### 3. Migration Errors
```bash
# Reset database
php artisan migrate:fresh --seed
```

#### 4. Assets Not Loading
```bash
# Rebuild assets
npm run build

# Clear cache
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

#### 5. Queue Jobs Not Processing
```bash
# Restart queue worker
php artisan queue:restart
php artisan queue:work
```

#### 6. Schedule Not Running
```bash
# Test schedule manually
php artisan schedule:run

# Or run in foreground
php artisan schedule:work
```

### Logs
Check logs for detailed error information:
```bash
# Windows
type storage\logs\laravel.log

# Linux/Mac
tail -f storage/logs/laravel.log
```

---

## 📖 Additional Documentation

- **[API_DOCUMENTATION.md](API_DOCUMENTATION.md)**: Complete API reference
- **[TESTING_NEW_FEATURES.md](TESTING_NEW_FEATURES.md)**: Comprehensive testing guide
- **[NEW_FEATURES_QUICK_START.md](NEW_FEATURES_QUICK_START.md)**: Quick start guide
- **[HOW_TO_LOGIN_AND_TEST.md](HOW_TO_LOGIN_AND_TEST.md)**: Login credentials and testing
- **[OPTION_A_COMPLETE.md](OPTION_A_COMPLETE.md)**: Implementation details
- **[IMPLEMENTATION_SUMMARY_OCT_16.md](IMPLEMENTATION_SUMMARY_OCT_16.md)**: Recent changes

---

## 🤝 Contributing

### Development Workflow

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Code Standards

- Follow PSR-12 coding standards
- Write descriptive commit messages
- Add comments for complex logic
- Write tests for new features
- Update documentation

### Running Code Style Fixer
```bash
./vendor/bin/pint
```

---

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👨‍💻 Author

**Jahid-kuet**
- GitHub: [@Jahid-kuet](https://github.com/Jahid-kuet)
- Repository: [Web_Drone_Delivery_System](https://github.com/Jahid-kuet/Web_Drone_Delivery_System)

---

## 🙏 Acknowledgments

- Laravel Framework
- Tailwind CSS
- Alpine.js
- Font Awesome
- Chart.js
- All contributors and testers

---

## 📞 Support

For issues, questions, or suggestions:
1. Check the [documentation files](.)
2. Review [troubleshooting section](#-troubleshooting)
3. Open an issue on GitHub
4. Check existing issues for solutions

---

## 🗺️ Roadmap

### Phase 2 (Planned)
- [ ] Real-time WebSocket updates
- [ ] Live map view with drone positions
- [ ] SMS integration for OTP delivery
- [ ] Email notifications
- [ ] QR code scanning for deliveries
- [ ] Multi-language support (Bengali + English)

### Phase 3 (Future)
- [ ] Progressive Web App (PWA)
- [ ] Mobile app (React Native/Flutter)
- [ ] Push notifications
- [ ] Voice commands
- [ ] AI-powered route optimization
- [ ] Predictive maintenance
- [ ] AR delivery preview

---

## 📊 Project Statistics

- **Total Files Created**: 20+
- **Total Lines of Code**: 11,500+
- **Production Code**: 3,000+ lines
- **Documentation**: 8,500+ lines
- **API Endpoints**: 8 delivery confirmation + 15+ management
- **Database Tables**: 12 main tables
- **Roles**: 4 (Admin, Hospital Admin, Hospital Staff, Operator)
- **Test Accounts**: 3 default accounts
- **Hubs**: 3 operational in Khulna

---

<div align="center">

**Built with ❤️ for better healthcare delivery in Bangladesh**

⭐ Star this repository if you find it helpful!

</div>
