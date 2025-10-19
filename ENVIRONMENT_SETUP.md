# Environment Setup Guide - SalonPro

Bu rehber, SalonPro uygulamasının farklı ortamlarda (development, staging, production) nasıl kurulacağını açıklar.

## Ortam Dosyaları

Proje, üç farklı ortam yapılandırması içerir:

- `.env.development` - Yerel geliştirme ortamı
- `.env.staging` - Test/staging ortamı
- `.env.production` - Üretim ortamı
- `.env.example` - Şablon dosya

## Kurulum Adımları

### 1. Development (Local)

```bash
# .env dosyasını oluştur
cp .env.development .env

# Uygulama anahtarı oluştur
php artisan key:generate

# Veritabanını oluştur ve migrate et
php artisan migrate

# Seeder'ları çalıştır
php artisan db:seed

# Docker ile çalıştırma (önerilen)
docker-compose up -d

# Veya yerel olarak
php artisan serve
npm run dev
```

**Gereksinimler:**
- PHP 8.3+
- MySQL 8.0+
- Redis 7+
- Node.js 20+
- Composer 2.6+

### 2. Staging

```bash
# .env dosyasını oluştur
cp .env.staging .env

# Gerekli değerleri düzenle
nano .env

# Uygulama anahtarı oluştur
php artisan key:generate

# Bağımlılıkları yükle (production mode)
composer install --optimize-autoloader --no-dev
npm ci
npm run build

# Veritabanını hazırla
php artisan migrate --force

# Önbelleği optimize et
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Queue worker'ı başlat
php artisan queue:work redis --daemon
```

**Dikkat Edilecekler:**
- `APP_DEBUG=true` staging'de aktif kalabilir
- Log level: `info`
- Email: Test servisi (Mailtrap) kullanılabilir
- Telescope aktif kalabilir

### 3. Production

```bash
# .env dosyasını oluştur
cp .env.production .env

# MUTLAKA GÜVENLİ ŞİFRELER KULLAN!
nano .env

# Uygulama anahtarı oluştur (sadece ilk kurulumda)
php artisan key:generate

# Bağımlılıkları yükle
composer install --optimize-autoloader --no-dev --no-interaction
npm ci
npm run build

# Dosya izinlerini ayarla
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Veritabanını hazırla
php artisan migrate --force

# Önbelleği optimize et
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan optimize

# Queue worker'ı Supervisor ile başlat
# (supervisor config örneği aşağıda)

# Cron job ekle
# * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

**Güvenlik Kontrol Listesi:**
- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] Güçlü `APP_KEY` oluşturuldu
- [ ] Güvenli veritabanı şifreleri
- [ ] Redis şifresi ayarlandı
- [ ] SSL sertifikası kuruldu
- [ ] Firewall kuralları yapılandırıldı
- [ ] Session encryption aktif
- [ ] Secure cookies aktif
- [ ] CORS ayarları yapılandırıldı
- [ ] Rate limiting aktif
- [ ] Backup sistemi kuruldu
- [ ] Monitoring (Sentry) entegre edildi
- [ ] Log rotation yapılandırıldı

## Ortam Değişkenleri Açıklaması

### Zorunlu Değişkenler

```env
APP_NAME=SalonPro              # Uygulama adı
APP_ENV=production             # Ortam (local, staging, production)
APP_KEY=                       # Laravel encryption key (php artisan key:generate)
APP_DEBUG=false                # Debug modu (production'da MUTLAKA false)
APP_URL=https://...            # Uygulama URL'i

DB_DATABASE=                   # Veritabanı adı
DB_USERNAME=                   # Veritabanı kullanıcı adı
DB_PASSWORD=                   # Veritabanı şifresi

REDIS_PASSWORD=                # Redis şifresi (production'da MUTLAKA ayarlayın)

MAIL_HOST=                     # SMTP host
MAIL_USERNAME=                 # SMTP kullanıcı adı
MAIL_PASSWORD=                 # SMTP şifresi
```

### İsteğe Bağlı Değişkenler

```env
# AWS S3 (dosya depolama için)
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=
AWS_BUCKET=

# Sentry (hata izleme)
SENTRY_LARAVEL_DSN=

# Slack (bildirimler)
LOG_SLACK_WEBHOOK_URL=

# SMS Gateway
SMS_PROVIDER=
SMS_API_KEY=
```

## Supervisor Yapılandırması (Production)

`/etc/supervisor/conf.d/salonpro-worker.conf`:

```ini
[program:salonpro-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/salonpro/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/salonpro/storage/logs/worker.log
stopwaitsecs=3600
```

Supervisor'ı başlatma:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start salonpro-worker:*
```

## Nginx Yapılandırması (Production)

`/etc/nginx/sites-available/salonpro`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name app.salonpro.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name app.salonpro.com;
    root /var/www/salonpro/public;

    # SSL
    ssl_certificate /etc/letsencrypt/live/app.salonpro.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/app.salonpro.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    index index.php;
    charset utf-8;
    client_max_body_size 100M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        fastcgi_read_timeout 300;
        fastcgi_send_timeout 300;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Gzip
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml text/javascript application/json application/javascript application/xml+rss application/rss+xml font/truetype font/opentype application/vnd.ms-fontobject image/svg+xml;

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

## Cron Jobs

Crontab'a ekle (`crontab -e`):

```bash
# Laravel Scheduler
* * * * * cd /var/www/salonpro && php artisan schedule:run >> /dev/null 2>&1

# Backup (Günlük saat 02:00)
0 2 * * * cd /var/www/salonpro && php artisan backup:run >> /dev/null 2>&1

# Log temizleme (Haftalık)
0 3 * * 0 cd /var/www/salonpro && php artisan log:clean >> /dev/null 2>&1
```

## Deployment Checklist

### İlk Deployment
- [ ] Sunucu gereksinimlerini kontrol et
- [ ] Veritabanı oluştur
- [ ] Redis kur ve yapılandır
- [ ] SSL sertifikası kur
- [ ] .env dosyasını yapılandır
- [ ] Bağımlılıkları yükle
- [ ] Veritabanı migration'larını çalıştır
- [ ] Storage dizin izinlerini ayarla
- [ ] Queue worker'ı yapılandır
- [ ] Cron job'ları ekle
- [ ] Backup sistemi kur
- [ ] Monitoring kur
- [ ] Test et

### Her Deployment
- [ ] Maintenance mode aktif et
- [ ] Git pull
- [ ] Composer install
- [ ] NPM install ve build
- [ ] Migration çalıştır
- [ ] Cache temizle ve yeniden oluştur
- [ ] Queue worker'ları yeniden başlat
- [ ] Maintenance mode kapat
- [ ] Smoke test yap

## Sorun Giderme

### Permission Errors

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Queue Not Working

```bash
# Worker'ları yeniden başlat
php artisan queue:restart

# Supervisor kontrol et
sudo supervisorctl status
sudo supervisorctl restart salonpro-worker:*
```

### Cache Issues

```bash
# Tüm cache'i temizle
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

# OPcache temizle
sudo service php8.3-fpm reload
```

## Support

Sorunlar için:
- GitHub Issues: https://github.com/yourorg/salonpro/issues
- Documentation: https://docs.salonpro.com
