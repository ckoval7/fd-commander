# Field Day Logging Database (v2)

A modern, web-based ham radio Field Day logging application built with Laravel 12 and Mary UI. Designed for easy deployment in air-gapped environments with simple hardware requirements.

## Features

### ✅ Implemented

#### Equipment Inventory Management
Complete equipment tracking and management system for Field Day operations.

**For All Operators:**
- Create and manage personal equipment catalog
- Track equipment by make, model, serial number, and type
- Upload equipment photos and add searchable tags
- Commit equipment to Field Day events
- View equipment status and commitments
- Search and filter equipment by type, status, and owner
- Prevent overlapping equipment commitments
- Receive notifications for equipment status changes

**For Event Managers:**
- Manage club-owned equipment with assigned managers
- Track equipment through full event lifecycle (Committed → Delivered → In Use → Returned)
- Mark equipment as Lost or Damaged with audit trail
- Assign equipment to specific operating stations
- View equipment dashboard with real-time status
- Generate equipment utilization and commitment reports
- Override status changes during event operations

**Technical Highlights:**
- Dual ownership model (User and Organization equipment)
- State machine for equipment status transitions
- Comprehensive permission system (manage-own-equipment, view-all-equipment, edit-any-equipment, manage-event-equipment)
- Automated notifications for equipment owners
- Soft deletes for data preservation
- Full test coverage with 38 comprehensive tests

#### User Management
- Role-based access control (Admin, Event Manager, Operator)
- User profile management with activity tracking
- Session management and security monitoring
- Callsign and email validation
- Last login tracking

#### System Features
- Setup wizard for initial configuration
- Organization management
- Comprehensive audit logging
- Admin audit log viewer with filtering and export

### 🚧 In Development

- QSO Logging
- Real-time scoring
- Station management
- Band/mode tracking

### 📋 Planned

- 2025 Field Day Rules Compliance
- Interactive ARRL/RAC section maps
- Rig interface agents (hamlib integration)
- Callsign lookup (callook.info integration)
- Winter Field Day support

## Technology Stack

- **Backend**: Laravel 12 (PHP 8.4+)
- **Frontend**: Mary UI (Livewire 4 + Tailwind CSS + Alpine.js)
- **Database**: MySQL 8.0+ / MariaDB 10.6+
- **Real-time**: Laravel Reverb (WebSockets)
- **Testing**: Pest 4
- **Code Quality**: Laravel Pint

## Requirements

### Minimum System Requirements
- PHP 8.4 or higher
- Composer 2.x
- Node.js 18+ and npm
- MySQL 8.0+ or MariaDB 10.6+

### Tested Hardware
- Raspberry Pi 4 (4GB RAM)
- Intel NUC
- Standard LAMP server

### Air-Gapped Deployment
All dependencies are bundled and can run without internet connectivity:
- No CDN dependencies
- All assets compiled locally
- Self-contained deployment

## Installation

### Development Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd fdlogdb/fdlogdb
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   Edit `.env` and set your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=fdlogdb
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Build assets**
   ```bash
   npm run build
   ```

7. **Start development server**
   ```bash
   php artisan serve
   ```

8. **Run setup wizard**
   Navigate to `http://localhost:8000/setup` and complete the setup wizard.

### Production Deployment

#### Docker Compose (Recommended)
```bash
docker-compose up -d
```

#### Traditional LAMP Stack
1. Configure Apache/Nginx to serve from `public/` directory
2. Set appropriate file permissions
3. Configure environment variables
4. Run migrations
5. Build production assets: `npm run build`

## Testing

Run the full test suite:
```bash
php artisan test
```

Run specific test files:
```bash
php artisan test tests/Feature/Equipment/EquipmentPermissionTest.php
php artisan test --filter=EquipmentEvent
```

Run with coverage:
```bash
php artisan test --coverage
```

## Documentation

- **User Guide**: See `/docs/equipment-inventory.md` for equipment inventory user documentation
- **Project Tracking**: See `CLAUDE_DOCS/PROJECT_TRACKER.md` for development status
- **Security**: See `CLAUDE_DOCS/SECURITY_AUDIT.md` for security considerations
- **Architecture**: See `CLAUDE_DOCS/IMPLEMENTATION_ROADMAP.md` for technical details

## Project Structure

```
fdlogdb/
├── app/
│   ├── Http/
│   │   ├── Controllers/      # HTTP controllers
│   │   └── Requests/         # Form request validation
│   ├── Livewire/             # Livewire components
│   ├── Models/               # Eloquent models
│   ├── Policies/             # Authorization policies
│   └── Observers/            # Model observers
├── database/
│   ├── factories/            # Model factories
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
├── resources/
│   ├── css/                  # Tailwind CSS
│   ├── js/                   # JavaScript/Alpine.js
│   └── views/                # Blade templates
│       ├── components/       # Blade components
│       ├── livewire/         # Livewire views
│       └── layouts/          # Layout templates
├── tests/
│   ├── Feature/              # Feature tests
│   └── Unit/                 # Unit tests
├── docs/                     # User documentation
└── CLAUDE_DOCS/              # Development documentation
```

## Development Workflow

1. **Check Project Status**: Always start by reading `CLAUDE_DOCS/PROJECT_TRACKER.md`
2. **Update Tracker**: Update the project tracker as changes are made
3. **Test First**: Write tests before implementation
4. **Code Style**: Run `vendor/bin/pint` before committing
5. **Commit Messages**: Use conventional commits format

## Guiding Principles

- **Easy Deployment**: Simple setup for non-technical users
- **Air-Gapped Ready**: No internet required during operation
- **Open Source**: All dependencies are open source compatible
- **Lightweight**: Runs on modest hardware (Raspberry Pi)
- **Security First**: Modern security practices, no legacy vulnerabilities

## Contributing

This is a modernization of a 10-year-old Field Day logging application. Contributions are welcome!

1. Fork the repository
2. Create a feature branch
3. Write tests for new features
4. Ensure all tests pass
5. Run Laravel Pint for code formatting
6. Submit a pull request

## Security

If you discover a security vulnerability, please send an email to the project maintainer. All security vulnerabilities will be promptly addressed.

**Note**: The legacy v1 application (in `../html/`) contained 7 critical vulnerabilities including SQL injection and hardcoded credentials. v2 addresses all known security issues using Laravel's built-in protections.

## License

This project is open-sourced software licensed under the MIT license.

## Field Day Rules

This application aims to comply with:
- ARRL Field Day Rules 2025 (see `Field-Day-Rules-2025.pdf`)
- Winter Field Day Rules (see `winter-Field-Day.pdf`)

## Acknowledgments

- Built with [Laravel](https://laravel.com)
- UI components by [Mary UI](https://mary-ui.com)
- Icons by [Heroicons](https://heroicons.com)
- Testing with [Pest PHP](https://pestphp.com)

## Support

For questions or issues:
- Check documentation in `/docs/` directory
- Review `CLAUDE_DOCS/` for development information
- Submit issues on the project repository

---

**Original Project Proposal**: See `FDLDB_proposal.md` for the original 2016 vision behind this project.
