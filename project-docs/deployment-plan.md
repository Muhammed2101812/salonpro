# Deployment Plan - SalonPro

## Deployment Overview

This document outlines the deployment strategy, environments, and procedures for SalonPro.

---

## Environments

### 1. Development
**Purpose:** Active development
**URL:** http://localhost:8000
**Access:** Development team
**Data:** Fake/seed data
**Updates:** Continuous

### 2. Staging
**Purpose:** Pre-production testing
**URL:** https://staging.salonpro.com
**Access:** QA team, stakeholders
**Data:** Sanitized production copy
**Updates:** Before each release

### 3. Production
**Purpose:** Live system
**URL:** https://app.salonpro.com
**Access:** End users
**Data:** Real data
**Updates:** Scheduled releases

---

## Server Requirements

### Minimum Production Server

**Operating System:**
- Ubuntu 22.04 LTS (recommended)
- CentOS 8+ / RHEL 8+

**Web Server:**
- Nginx 1.20+
- PHP-FPM 8.3+

**Database:**
- MySQL 8.0+ or MariaDB 10.6+
- 4GB RAM minimum
- SSD storage

**Cache & Queue:**
- Redis 7+

**Search:**
- Meilisearch 1.0+

**Resources:**
- CPU: 2+ cores
- RAM: 4GB minimum (8GB recommended)
- Storage: 50GB minimum (SSD)
- Bandwidth: 100Mbps+

### Recommended Production Server

**Resources:**
- CPU: 4+ cores
- RAM: 8GB+ (16GB for high traffic)
- Storage: 100GB+ SSD
- Bandwidth: 1Gbps

---

## Pre-Deployment Checklist

### Code Quality
- [ ] All tests passing (85%+ coverage)
- [ ] PHPStan Level 8 passing
- [ ] Laravel Pint passing (code style)
- [ ] No critical bugs
- [ ] Security audit completed
- [ ] Performance testing completed

### Configuration
- [ ] Environment variables configured
- [ ] Database credentials secured
- [ ] API keys secured
- [ ] SMTP credentials configured
- [ ] SMS provider configured
- [ ] Storage credentials configured (S3/MinIO)
- [ ] SSL certificate installed

### Database
- [ ] Migrations tested
- [ ] Seeders prepared (if needed)
- [ ] Backup verified
- [ ] Rollback plan ready

### Infrastructure
- [ ] Server provisioned
- [ ] Firewall configured
- [ ] Load balancer configured (if applicable)
- [ ] CDN configured
- [ ] DNS configured
- [ ] Monitoring setup
- [ ] Backup system operational

---

## Deployment Strategy

### Blue-Green Deployment

**Approach:** Maintain two identical production environments

**Process:**
1. Deploy to inactive environment (Green)
2. Run smoke tests on Green
3. Switch traffic from Blue to Green
4. Monitor Green environment
5. Keep Blue as rollback option
6. After stability, update Blue

**Benefits:**
- Zero downtime
- Easy rollback
- Safe deployment

### Rolling Deployment (Alternative)

**Approach:** Gradually update servers

**Process:**
1. Update 1 server
2. Health check
3. Update next server
4. Repeat until all updated

---

## Deployment Steps

### 1. Pre-Deployment

```bash
# Backup database
php artisan backup:run

# Export current configuration
cp .env .env.backup

# Tag release
git tag -a v1.0.0 -m "Release 1.0.0"
git push origin v1.0.0
```

### 2. Code Deployment

```bash
# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Install frontend dependencies
npm ci
npm run build
```

### 3. Database Migration

```bash
# Run migrations (with backup)
php artisan down
php artisan migrate --force
php artisan up
```

### 4. Cache & Optimization

```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 5. Queue & Services

```bash
# Restart queue workers
php artisan queue:restart

# Restart Horizon
php artisan horizon:terminate

# Restart PHP-FPM
sudo systemctl restart php8.3-fpm

# Restart Nginx
sudo systemctl reload nginx
```

### 6. Post-Deployment

```bash
# Health check
curl https://app.salonpro.com/health

# Smoke tests
php artisan test --filter=SmokeTest

# Monitor logs
tail -f storage/logs/laravel.log
```

---

## Automated Deployment (CI/CD)

### GitHub Actions Workflow

```yaml
name: Deploy to Production

on:
  push:
    tags:
      - 'v*'

jobs:
  deploy:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.3

      - name: Install Dependencies
        run: composer install --no-dev --optimize-autoloader

      - name: Build Assets
        run: |
          npm ci
          npm run build

      - name: Run Tests
        run: php artisan test

      - name: Deploy to Server
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.SERVER_HOST }}
          username: ${{ secrets.SERVER_USER }}
          key: ${{ secrets.SERVER_SSH_KEY }}
          script: |
            cd /var/www/salonpro
            git pull origin main
            composer install --no-dev
            php artisan migrate --force
            php artisan optimize
            php artisan queue:restart
            sudo systemctl reload php8.3-fpm

      - name: Health Check
        run: |
          sleep 10
          curl -f https://app.salonpro.com/health || exit 1
```

---

## Database Migration Strategy

### Zero-Downtime Migrations

**Safe Operations:**
- Adding columns (with defaults)
- Adding indexes (online)
- Creating new tables
- Adding foreign keys (online)

**Unsafe Operations (require maintenance):**
- Dropping columns
- Renaming columns
- Changing column types
- Dropping tables

### Migration Best Practices

1. **Always test migrations on staging**
2. **Backup before migrating**
3. **Run migrations during low-traffic**
4. **Have rollback plan ready**
5. **Monitor after migration**

### Example Safe Migration

```php
// Add column with default (safe)
Schema::table('appointments', function (Blueprint $table) {
    $table->string('cancellation_reason')->nullable()->after('status');
});

// Add index (online, safe)
Schema::table('appointments', function (Blueprint $table) {
    $table->index(['branch_id', 'scheduled_at']);
});
```

---

## Rollback Procedure

### When to Rollback
- Critical bugs in production
- Performance degradation
- Data corruption
- Security issues

### Rollback Steps

```bash
# 1. Enable maintenance mode
php artisan down

# 2. Revert code
git revert HEAD  # or git checkout previous-tag

# 3. Install dependencies
composer install --no-dev

# 4. Rollback migrations (if needed)
php artisan migrate:rollback

# 5. Restore database (if needed)
# Restore from backup

# 6. Clear caches and optimize
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan optimize

# 7. Restart services
php artisan queue:restart
sudo systemctl restart php8.3-fpm

# 8. Disable maintenance mode
php artisan up

# 9. Verify
curl https://app.salonpro.com/health
```

---

## Monitoring

### Application Monitoring

**Laravel Telescope:**
- Requests
- Commands
- Queries
- Jobs
- Exceptions

**Sentry:**
- Error tracking
- Performance monitoring
- Release tracking

### Server Monitoring

**Metrics to Monitor:**
- CPU usage
- Memory usage
- Disk space
- Network traffic
- Response times
- Error rates
- Queue length

**Tools:**
- New Relic / Datadog (application)
- Prometheus + Grafana (infrastructure)
- UptimeRobot (uptime)

### Alerting

**Alert Conditions:**
- Error rate > 5%
- Response time > 2 seconds
- CPU usage > 80%
- Memory usage > 90%
- Disk space < 10%
- Queue jobs failing
- Application down

**Notification Channels:**
- Email
- Slack
- SMS (critical only)

---

## Backup Strategy

### What to Backup
- Database
- User uploaded files
- Application code (Git)
- Environment configuration

### Backup Schedule

**Database:**
- Hourly: Last 24 hours
- Daily: Last 30 days
- Weekly: Last 12 weeks
- Monthly: Last 12 months

**Files:**
- Daily: Last 7 days
- Weekly: Last 4 weeks
- Monthly: Last 12 months

### Backup Storage
- Primary: On-server
- Secondary: Off-site (S3/MinIO)
- Tertiary: Different region

### Backup Testing
- Monthly restore test
- Verify integrity
- Document process

---

## Security Hardening

### Server Security

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Configure firewall
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable

# Install Fail2Ban
sudo apt install fail2ban
sudo systemctl enable fail2ban

# Secure MySQL
sudo mysql_secure_installation

# Disable root login
sudo sed -i 's/PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config
sudo systemctl restart ssh
```

### Application Security
- HTTPS only (enforce)
- Security headers configured
- CSRF protection enabled
- Rate limiting active
- 2FA enabled for admins
- Regular security updates
- Secrets in environment variables

---

## Performance Optimization

### Application Level
```bash
# Optimize composer autoloader
composer install --optimize-autoloader --no-dev

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize framework
php artisan optimize
```

### Database Level
- Query optimization
- Proper indexing
- Connection pooling
- Query caching

### Server Level
- OPcache enabled
- Redis caching
- CDN for static assets
- Gzip compression
- HTTP/2 enabled

---

## Maintenance Windows

### Scheduled Maintenance
**Frequency:** Monthly (or as needed)
**Duration:** 2-4 hours
**Time:** Sunday 02:00-06:00 (low traffic)

**Activities:**
- Apply security updates
- Database optimization
- Log rotation
- Backup verification
- Performance tuning

### Communication
- Notify users 72 hours in advance
- Display maintenance banner
- Send email notifications
- Update status page

---

## Disaster Recovery

### Recovery Time Objective (RTO)
**Target:** 4 hours

### Recovery Point Objective (RPO)
**Target:** 1 hour (data loss tolerance)

### Disaster Recovery Plan

1. **Assess the situation**
2. **Activate backup systems**
3. **Restore from latest backup**
4. **Verify data integrity**
5. **Test functionality**
6. **Redirect traffic**
7. **Monitor closely**
8. **Post-mortem analysis**

---

## Go-Live Checklist

- [ ] All tests passing
- [ ] Security audit completed
- [ ] Performance testing completed
- [ ] Backup system operational
- [ ] Monitoring configured
- [ ] SSL certificate installed
- [ ] DNS configured
- [ ] Email service configured
- [ ] SMS service configured
- [ ] Payment gateway tested (if active)
- [ ] Customer portal tested
- [ ] Admin panel tested
- [ ] Multi-language tested
- [ ] Multi-currency tested
- [ ] Documentation completed
- [ ] Training completed
- [ ] Support plan ready
- [ ] Communication plan ready

---

## Post-Launch

### First 24 Hours
- Monitor closely
- Check error logs
- Verify backups
- User feedback

### First Week
- Daily monitoring
- Performance analysis
- Bug tracking
- User support

### First Month
- Weekly reviews
- Performance optimization
- Feature feedback
- Training sessions

---

**Document Version:** 1.0
**Last Updated:** 2025-01-15
