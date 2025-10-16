# 🎯 SalonPro - Professional Salon Management System

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=flat&logo=vue.js)](https://vuejs.org)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=flat&logo=php)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

Enterprise-grade SaaS application for managing beauty salons, barbershops, spas, and wellness centers.

## 🌟 Key Features

- 📅 **Smart Appointment Management** - Intelligent scheduling with conflict detection
- 👥 **Complete CRM System** - 360° customer relationship management
- 💼 **Employee Management** - Performance tracking, commission calculation, scheduling
- 💰 **Financial Management** - Multi-currency, multi-payment methods, invoicing
- 📦 **Inventory Control** - Real-time stock tracking, auto-reorder, transfers
- 📊 **Advanced Analytics** - Real-time dashboards, predictive analytics, reports
- 🎁 **Marketing Suite** - Campaigns, loyalty program, gift vouchers
- 🌍 **Multi-Language** - Turkish & English support
- 💱 **Multi-Currency** - TRY & USD with automatic conversion
- 🏢 **Multi-Branch** - Complete multi-tenant architecture

**Total Features:** 600+ across 13 modules
**Roles:** 11 predefined roles with 150+ granular permissions

## 🚀 Quick Start

### Prerequisites

- PHP 8.3 or higher
- Composer 2.x
- Node.js 18.x or higher
- MySQL 8.0+ or MariaDB 10.6+
- Redis 7+
- Meilisearch 1.0+

### Installation

1. **Clone the repository**
```bash
git clone https://github.com/yourusername/salonpro.git
cd salonpro
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install Node.js dependencies**
```bash
npm install
```

4. **Configure environment**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure database in `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=salonpro
DB_USERNAME=root
DB_PASSWORD=
```

6. **Run migrations and seeders**
```bash
php artisan migrate --seed
```

7. **Build frontend assets**
```bash
npm run build
# or for development
npm run dev
```

8. **Start the development server**
```bash
php artisan serve
```

9. **Start queue worker** (in separate terminal)
```bash
php artisan queue:work
```

Visit `http://localhost:8000` in your browser.

### Default Login Credentials

**Super Admin:**
- Email: `admin@salonpro.com`
- Password: `Admin123!`

**Branch Manager:**
- Email: `manager@salonpro.com`
- Password: `Manager123!`

**Receptionist:**
- Email: `receptionist@salonpro.com`
- Password: `Receptionist123!`

> ⚠️ **Security Warning:** Change these credentials immediately after first login!

## 📚 Documentation

- [Project Overview](./project-docs/project-overview.md) - High-level project summary
- [Roadmap](./project-docs/roadmap.md) - 46-week development roadmap
- [Architecture](./project-docs/architecture.md) - System architecture & patterns
- [Tech Stack](./project-docs/tech-stack.md) - Technology choices & justifications
- [Database Design](./project-docs/database-design.md) - Schema & relationships
- [API Documentation](./project-docs/api-documentation.md) - RESTful API reference
- [User Roles](./project-docs/user-roles.md) - Roles & permissions matrix
- [Security Guidelines](./project-docs/security-guidelines.md) - Security best practices
- [Testing Strategy](./project-docs/testing-strategy.md) - Test approach & coverage
- [Deployment Plan](./project-docs/deployment-plan.md) - Deployment procedures
- [Business Rules](./project-docs/business-rules.md) - Business logic & policies
- [Features & Modules](./project-docs/features-modules.md) - Complete feature list

### For Developers

- [CLAUDE.md](./CLAUDE.md) - AI development guidelines & coding standards
- [CONTRIBUTING.md](./CONTRIBUTING.md) - Contribution guidelines

## 🏗️ Architecture

SalonPro follows **Clean Architecture** principles with clear separation of concerns:

```
┌─────────────────────────────────────────┐
│         Presentation Layer              │
│   (Controllers, API Resources, Views)   │
├─────────────────────────────────────────┤
│         Application Layer               │
│   (Services, Actions, Jobs, Events)     │
├─────────────────────────────────────────┤
│           Domain Layer                  │
│    (Models, Repositories, DTOs)         │
├─────────────────────────────────────────┤
│       Infrastructure Layer              │
│  (Database, Cache, External APIs)       │
└─────────────────────────────────────────┘
```

### Design Patterns Used

- **Repository Pattern** - Data access abstraction
- **Service Layer Pattern** - Business logic encapsulation
- **DTO Pattern** - Type-safe data transfer
- **Action Pattern** - Single-purpose operations
- **Observer Pattern** - Event-driven architecture
- **Strategy Pattern** - Flexible algorithm selection

## 🛠️ Tech Stack

### Backend
- **Framework:** Laravel 11.x
- **Language:** PHP 8.3+
- **Database:** MySQL 8.0+ / MariaDB 10.6+
- **Cache:** Redis 7+
- **Queue:** Redis with Laravel Horizon
- **Search:** Meilisearch 1.0+
- **Authentication:** Laravel Sanctum

### Frontend
- **Framework:** Vue.js 3 (Composition API)
- **State Management:** Pinia
- **Router:** Vue Router 4
- **UI Framework:** Tailwind CSS 3
- **Build Tool:** Vite 5
- **Internationalization:** Vue I18n

### Development Tools
- **Testing:** Pest PHP 2.x
- **Code Quality:** PHPStan Level 8, Laravel Pint
- **API Docs:** Scribe
- **Monitoring:** Laravel Telescope

## 🧪 Testing

Run the test suite:

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage --min=85

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run tests in parallel
php artisan test --parallel
```

**Coverage Target:** 85%+
**Quality Standard:** PHPStan Level 8

## 🔒 Security

SalonPro implements industry-standard security practices:

- ✅ **Authentication:** Laravel Sanctum with 2FA (TOTP)
- ✅ **Authorization:** RBAC + ABAC with 150+ granular permissions
- ✅ **Encryption:** At-rest and in-transit encryption
- ✅ **Data Protection:** KVKK & GDPR compliant
- ✅ **Security Headers:** CSP, HSTS, X-Frame-Options, etc.
- ✅ **Rate Limiting:** 60 requests/minute per user
- ✅ **SQL Injection Protection:** Parameterized queries
- ✅ **XSS Prevention:** Output escaping
- ✅ **CSRF Protection:** Token-based validation

For detailed security guidelines, see [Security Guidelines](./project-docs/security-guidelines.md).

## 🌍 Multi-Language Support

SalonPro supports **Turkish** (default) and **English**:

- All user-facing text is translatable
- Database fields use JSON: `{tr: "Türkçe", en: "English"}`
- Laravel translation system for backend
- Vue I18n for frontend
- Instant language switching without page reload
- User preference saved per account

## 💱 Multi-Currency Support

Supports **TRY** (Turkish Lira) and **USD** (US Dollar):

- Dual-column approach: `price_try` and `price_usd`
- Automatic currency conversion
- Daily exchange rate updates
- Historical rate tracking
- Transaction currency locked at time of purchase
- Reports available in both currencies

## 🏢 Multi-Tenant Architecture

- **Organization-level:** Multiple organizations on single installation
- **Branch-level:** Multiple branches per organization
- **Data Isolation:** All business data is branch-scoped
- **Global Scopes:** Automatic branch filtering
- **Cross-branch:** Optional with explicit permissions
- **Central vs Local:** Configurable inventory and reporting

## 📦 Modules

1. **User Management & Authorization** - Multi-factor auth, RBAC, session management
2. **Customer Management (CRM)** - 360° customer view, segmentation, portal
3. **Employee Management** - Schedule, performance, commission, payroll
4. **Service Management** - Catalog, packages, dynamic pricing
5. **Appointment & Calendar** - Smart scheduling, reminders, waiting list
6. **Product & Inventory** - Stock tracking, purchasing, transfers
7. **Financial Management** - Payments, invoicing, expenses, cash register
8. **Reporting & Analytics** - Real-time dashboards, predictive analytics
9. **Marketing & Campaigns** - Loyalty program, vouchers, email/SMS
10. **Settings & Configuration** - System-wide and branch-specific settings
11. **Activity Tracking & Audit** - Complete audit trail
12. **Mobile API** - RESTful API for mobile apps
13. **Advanced Features** - Franchise, quality management, vendor management

## 🎯 Business Rules

SalonPro enforces comprehensive business rules:

- **Appointment Rules:** Booking policies, cancellation fees, no-show tracking
- **Pricing Rules:** Dynamic pricing, discounts, package pricing
- **Payment Rules:** Multi-method, refund policies, debt management
- **Inventory Rules:** Stock management, auto-reorder, expiry tracking
- **Employee Rules:** Commission calculation, working hours, leave management
- **Security Rules:** Password policies, 2FA, session timeout
- **Compliance:** KVKK (Turkish) and GDPR (EU) compliant

See [Business Rules](./project-docs/business-rules.md) for complete details.

## 📊 Performance

- **Page Load Time:** < 2 seconds
- **API Response Time:** < 200ms (95th percentile)
- **Database Queries:** < 50ms average
- **Concurrent Users:** 1000+ supported
- **Optimizations:** OPcache, Redis caching, query optimization, CDN

## 🚀 Deployment

### Requirements
- Ubuntu 22.04 LTS (recommended)
- Nginx 1.20+
- PHP-FPM 8.3+
- MySQL 8.0+ / MariaDB 10.6+
- Redis 7+
- Meilisearch 1.0+
- SSL certificate

### Production Setup

```bash
# Install dependencies
composer install --no-dev --optimize-autoloader

# Build assets
npm ci
npm run build

# Run migrations
php artisan down
php artisan migrate --force
php artisan up

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Restart services
php artisan queue:restart
sudo systemctl restart php8.3-fpm
sudo systemctl reload nginx
```

For detailed deployment procedures, see [Deployment Plan](./project-docs/deployment-plan.md).

## 🤝 Contributing

We welcome contributions! Please read our [Contributing Guidelines](./CONTRIBUTING.md) before submitting pull requests.

### Development Workflow

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Follow coding standards (PHPStan Level 8, Laravel Pint)
4. Write tests (85%+ coverage)
5. Commit changes (`git commit -m 'feat(module): add amazing feature'`)
6. Push to branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

## 📝 Code Quality

```bash
# Code style check
./vendor/bin/pint --test

# Fix code style
./vendor/bin/pint

# Static analysis
./vendor/bin/phpstan analyse

# Run all quality checks
composer quality
```

## 🔄 Versioning

We use [Semantic Versioning](https://semver.org/):

- **MAJOR** version for incompatible API changes
- **MINOR** version for new functionality (backward compatible)
- **PATCH** version for bug fixes

See [CHANGELOG.md](./CHANGELOG.md) for release history.

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 Team

**Development Team Size:** 15 people
**Timeline:** 46 weeks (10 phases)
**Languages:** Turkish, English
**Currencies:** TRY, USD

### Roles
- Project Manager: 1
- Backend Developers: 4
- Frontend Developers: 3
- Mobile Developers: 2
- DevOps Engineer: 1
- QA Engineers: 2
- UI/UX Designer: 1
- Technical Writer: 1

## 📞 Support

- **Documentation:** [./project-docs](./project-docs)
- **Issues:** [GitHub Issues](https://github.com/yourusername/salonpro/issues)
- **Email:** support@salonpro.com
- **Website:** https://salonpro.com

## 🙏 Acknowledgments

- Laravel Community
- Vue.js Community
- Spatie Packages
- All open-source contributors

---

**Made with ❤️ by the SalonPro Team**

**Current Version:** 1.0.0
**Last Updated:** 2025-01-15

---

## 🗺️ Roadmap

**Phase 1** (Weeks 1-4): Foundation & Setup ✅
**Phase 2** (Weeks 5-12): Core Modules 🚧
**Phase 3** (Weeks 13-18): CRM & Employee Management
**Phase 4** (Weeks 19-24): Financial Management
**Phase 5** (Weeks 25-30): Inventory & Product Management
**Phase 6** (Weeks 31-35): Reporting & Analytics
**Phase 7** (Weeks 36-38): Marketing Features
**Phase 8** (Weeks 39-41): Mobile App & API
**Phase 9** (Weeks 42-44): Polish & Optimization
**Phase 10** (Weeks 45-46): Deployment & Documentation

See [Roadmap](./project-docs/roadmap.md) for detailed timeline.

## ⭐ Star History

If you find SalonPro useful, please consider giving it a star!

[![Star History Chart](https://api.star-history.com/svg?repos=yourusername/salonpro&type=Date)](https://star-history.com/#yourusername/salonpro&Date)
