# 🎓 ReportEase - University Issue Management System

[![Laravel](https://img.shields.io/badge/Laravel-8.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-7.3%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

A comprehensive web application for managing and tracking issues within a university environment. Built with Laravel 8, ReportEase provides role-based access control, image management, and real-time issue tracking capabilities.

## 📋 Table of Contents
- [Features](#-features)
- [Screenshots](#-screenshots)
- [User Roles](#-user-roles)
- [Tech Stack](#-tech-stack)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Environment Variables](#-environment-variables)
- [Usage](#-usage)
- [API Documentation](#-api-documentation)
- [File Structure](#-file-structure)
- [Testing](#-testing)
- [Deployment](#-deployment)
- [Contributing](#-contributing)
- [License](#-license)

## 🧰 Tech Stack

| Layer          | Technology                                   |
| -------------- | -------------------------------------------- |
| Backend        | Laravel 8 (PHP 7.3+ / 8.x)                   |
| Database       | MySQL 5.7+                                    |
| Front-end      | Blade templates, Bootstrap, Laravel Mix      |
| Image handling | [Intervention Image](http://image.intervention.io/) |
| Auth           | Laravel authentication + custom RBAC         |

## 🖼️ Screenshots

> Replace the placeholders below with real screenshots (place image files under `docs/screenshots/`).

| Login | Student Dashboard | Issue Details |
| ----- | ----------------- | ------------- |
| ![Login screen placeholder](docs/screenshots/login.png) | ![Dashboard placeholder](docs/screenshots/dashboard.png) | ![Issue details placeholder](docs/screenshots/issue.png) |

## ✨ Features

### 🔐 Authentication & Authorization
- **Multi-role Authentication**: Support for Students, Faculty Staff, Maintenance Department, and Admins
- **Secure Registration**: Email validation and role-based registration
- **Password Reset**: Email-based password recovery system
- **Session Management**: Secure user sessions with role persistence

### 📝 Issue Management
- **Create Issues**: Rich form with file upload capabilities
- **Issue Tracking**: Real-time status updates and progress tracking
- **Role-based Permissions**: Different access levels based on user roles
- **Status Management**: Comprehensive workflow (Pending → Under Review → Being Resolved → Resolved)

### 🖼️ Advanced Image Handling
- **Multiple Image Upload**: Up to 3 images per issue (JPG/PNG, max 2MB each)
- **Automatic Thumbnails**: 200x200px thumbnails generated automatically
- **Image Preview**: Drag-and-drop interface with live previews
- **Modal Gallery**: Full-screen image viewing with 4:3 aspect ratio containers
- **Secure Storage**: Images stored with random filenames for security

### 🎨 Modern User Interface
- **Responsive Design**: Mobile-first approach with Bootstrap integration
- **Role-based Dashboards**: Customized interfaces for each user type
- **Interactive Elements**: Hover effects, animations, and smooth transitions
- **Accessibility**: ARIA labels, keyboard navigation, and screen reader support

### 📊 Dashboard Features
- **Student Dashboard**: View and submit issues, track personal reports
- **Faculty Staff Dashboard**: Review and approve/reject issues
- **Maintenance Dashboard**: Manage and resolve technical issues
- **Admin Dashboard**: Complete system overview and user management

## 👥 User Roles

### 🎓 Student
- Submit new issues with evidence images
- View all active issues (excluding resolved ones)
- Track personal issue history
- Receive notifications on issue updates

### 👨‍🏫 Faculty Staff
- All student permissions
- Review and validate submitted issues
- Accept, reject, or mark issues under review
- Assign issues to maintenance department

### 🔧 Maintenance Department
- All faculty permissions
- Update issue status to "Being Resolved"
- Mark issues as "Resolved"
- Access maintenance-specific analytics

### 👑 Admin
- Complete system access
- User management and role assignment
- System configuration and monitoring
- Access to all pages and features

## 🚀 Installation

### Prerequisites
- **PHP**: Version 7.3 or higher
- **Composer**: Latest version
- **MySQL**: Version 5.7 or higher
- **Node.js**: Version 12 or higher (for asset compilation)
- **Git**: For version control

### Step 1: Clone Repository
```bash
git clone https://github.com/KavinduAluthwaththa/ReportEase.git
cd ReportEase
```

### Step 2: Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies and compile front-end assets
npm install
npm run dev
```

### Step 3: Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Database Setup
1. Create a MySQL database named `ReportEase`
2. Update your `.env` file with database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ReportEase
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Step 5: Run Migrations and Seeders
```bash
# Run database migrations
php artisan migrate

# Seed initial data (roles and sections)
php artisan db:seed
```

### Step 6: Storage Setup
```bash
# Create symbolic link for public storage
php artisan storage:link

# Set proper permissions
chmod -R 775 storage bootstrap/cache
```

### Step 7: Start Development Server
```bash
php artisan serve
```

Visit `http://localhost:8000` to access the application.

## ⚙️ Configuration

### Image Upload Settings
The system supports image uploads with the following specifications:
- **Maximum File Size**: 2MB per image
- **Allowed Formats**: JPG, PNG
- **Maximum Images**: 3 per issue
- **Thumbnail Size**: 200x200px (auto-generated)
- **Storage Location**: `storage/app/public/evidence/`

### Role Configuration
Default roles are seeded into the database (see `database/seeders/RoleSeeder.php`):
1. **Admin** (ID: 1)
2. **Faculty Staff** (ID: 2)
3. **Maintenance Department** (ID: 3)
4. **Student** (ID: 4)

### Email Configuration
Update your `.env` file for email functionality:
```env
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email@domain.com
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@reportease.com
MAIL_FROM_NAME="ReportEase"
```

## 🔑 Environment Variables

All configuration is driven by the `.env` file (copied from `.env.example`). The most important variables:

| Variable | Description | Example |
| -------- | ----------- | ------- |
| `APP_NAME` | Application name shown in UI/emails | `ReportEase` |
| `APP_ENV` | Environment (`local`, `production`, `testing`) | `local` |
| `APP_KEY` | Encryption key — generate with `php artisan key:generate` | *(auto-generated)* |
| `APP_DEBUG` | Verbose error output — set `false` in production | `false` |
| `APP_URL` | Base URL of the application | `http://localhost:8000` |
| `DB_CONNECTION` | Database driver | `mysql` |
| `DB_HOST` / `DB_PORT` | Database host and port | `127.0.0.1` / `3306` |
| `DB_DATABASE` | Database name | `ReportEase` |
| `DB_USERNAME` / `DB_PASSWORD` | Database credentials | `root` / *(secret)* |
| `MAIL_MAILER` / `MAIL_HOST` / `MAIL_PORT` | Mail transport for password resets & notifications | `smtp` / `smtp.mailtrap.io` / `2525` |
| `MAIL_USERNAME` / `MAIL_PASSWORD` | Mail credentials | *(secret)* |
| `MAIL_FROM_ADDRESS` / `MAIL_FROM_NAME` | Default sender | `noreply@reportease.com` / `ReportEase` |
| `QUEUE_CONNECTION` | Queue driver | `sync` |
| `SESSION_DRIVER` | Session storage | `file` |

> ⚠️ **Never commit a real `.env` file or secrets.** `.env` is git-ignored; only `.env.example` (with placeholders) is tracked.

## 📖 Usage

### For Students
1. **Register**: Create account with student email
2. **Login**: Access your student dashboard
3. **Report Issue**: Click "Report New Issue"
   - Fill in issue details
   - Upload evidence images (optional)
   - Submit for review
4. **Track Issues**: Monitor status in "View Issues" section

### For Faculty Staff
1. **Review Issues**: Access pending issues from dashboard
2. **Validate Reports**: Accept, reject, or request more information
3. **Assign to Maintenance**: Forward validated issues
4. **Monitor Progress**: Track resolution status

### For Maintenance Department
1. **Receive Assignments**: View issues assigned by faculty
2. **Update Status**: Mark as "Being Resolved" when starting work
3. **Complete Resolution**: Mark as "Resolved" when finished
4. **Add Updates**: Provide progress updates to reporters

### For Administrators
1. **User Management**: Create, edit, delete user accounts
2. **Role Assignment**: Manage user permissions
3. **System Monitoring**: View analytics and reports
4. **Configuration**: Adjust system settings

## 📡 API Documentation

### Authentication Endpoints
```
POST /login           - User authentication
POST /register        - New user registration
POST /logout          - User logout
POST /password/reset  - Password reset request
```

### Issue Management Endpoints
```
GET  /issues          - List all issues (filtered by role)
POST /issues          - Create new issue
GET  /issues/{id}     - View specific issue
PUT  /issues/{id}     - Update issue status
DELETE /issues/{id}   - Delete issue (admin only)
```

### Dashboard Endpoints
```
GET /dashboard        - Role-based dashboard redirect
GET /student-dashboard     - Student interface
GET /faculty-dashboard     - Faculty interface  
GET /maintenance-dashboard - Maintenance interface
GET /admin-dashboard       - Admin interface
```

## 📁 File Structure

```
WAD-Project/
├── app/
│   ├── Http/Controllers/
│   │   ├── IssueController.php      # Issue CRUD operations
│   │   ├── AuthController.php       # Authentication logic
│   │   └── DashboardController.php  # Dashboard routing
│   ├── Models/
│   │   ├── User.php                 # User model with roles
│   │   ├── Issue.php                # Issue model
│   │   ├── IssueImage.php           # Image attachment model
│   │   ├── Role.php                 # User role model
│   │   └── Section.php              # Issue categorization
├── database/
│   ├── migrations/                  # Database schema
│   └── seeders/                     # Initial data
├── public/
│   ├── css/                         # Compiled stylesheets
│   ├── js/                          # Compiled JavaScript  
│   └── storage/                     # Public file storage
├── resources/
│   └── views/
│       ├── auth/                    # Authentication pages
│       ├── shared/                  # Reusable components
│       └── dashboard/               # Role-specific dashboards
└── storage/
    └── app/public/evidence/         # Uploaded images
```

## 🧪 Testing

The project uses [PHPUnit](https://phpunit.de/) (configured in `phpunit.xml`).

```bash
# Run the full test suite
php artisan test
# or
./vendor/bin/phpunit
```

Tests run against the `testing` environment (array cache/session/mail). To use an
in-memory SQLite database, uncomment the `DB_CONNECTION`/`DB_DATABASE` lines in
`phpunit.xml`.

Code style is checked with [Laravel Pint](https://laravel.com/docs/pint):

```bash
# Check code style (no changes written)
./vendor/bin/pint --test

# Auto-fix code style
./vendor/bin/pint
```

## 🚀 Deployment

1. Provision a server with PHP, Composer, MySQL and a web server (Nginx/Apache).
2. Clone the repository and install production dependencies:
   ```bash
   composer install --optimize-autoloader --no-dev
   npm install && npm run prod
   ```
3. Configure `.env` for production (`APP_ENV=production`, `APP_DEBUG=false`, real DB/mail credentials) and run `php artisan key:generate`.
4. Run migrations and seeders: `php artisan migrate --seed --force`.
5. Link storage and cache config/routes/views:
   ```bash
   php artisan storage:link
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
6. Point the web server document root to the `public/` directory and ensure `storage/` and `bootstrap/cache/` are writable.

## 🛠️ Development

### Adding New Features
1. Create feature branch: `git checkout -b feature/new-feature`
2. Implement changes following Laravel conventions
3. Add appropriate tests
4. Update documentation
5. Submit pull request

### Database Modifications
```bash
# Create new migration
php artisan make:migration create_new_table

# Create model with migration
php artisan make:model NewModel -m

# Run migrations
php artisan migrate
```

## 🤝 Contributing

We welcome contributions from the community! Please follow these guidelines:

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/amazing-feature`)
3. **Commit** your changes (`git commit -m 'Add amazing feature'`)
4. **Push** to the branch (`git push origin feature/amazing-feature`)
5. **Open** a Pull Request

### Code Standards
- Follow PSR-12 coding standards for PHP
- Use meaningful variable and function names
- Add comments for complex logic
- Write tests for new features
- Update documentation as needed

### Bug Reports
When reporting bugs, please include:
- PHP and Laravel version
- Steps to reproduce the issue
- Expected vs actual behavior
- Screenshots (if applicable)
- Error messages or logs

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- **Laravel Framework**: For the robust foundation
- **Bootstrap**: For responsive UI components
- **Intervention Image**: For advanced image processing
- **Contributors**: Everyone who helped build and improve this system

## 📞 Support

For support and questions:
- **Email**: support@reportease.com
- **Documentation**: Check this README and inline comments
- **Issues**: Open a GitHub issue for bugs or feature requests

---

Built and maintained by **Kavindu Aluthwaththa**. Powered by the [Laravel](https://laravel.com) framework.
