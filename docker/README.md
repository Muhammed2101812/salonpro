# Docker Setup - SalonPro

Bu dizin, SalonPro uygulamasının Docker yapılandırma dosyalarını içerir.

## Servisler

- **nginx**: Nginx web sunucusu (Port: 8080)
- **php**: PHP 8.3-FPM
- **mysql**: MySQL 8.0 veritabanı (Port: 3306)
- **redis**: Redis cache ve queue (Port: 6379)
- **queue**: Laravel queue worker
- **scheduler**: Laravel task scheduler
- **mailpit**: E-posta test aracı (SMTP: 1025, Web UI: 8025)
- **node**: Node.js ve Vite (Port: 5173)

## Kurulum

### 1. Docker'ı Başlatma

```bash
# Container'ları oluştur ve başlat
docker-compose up -d

# Build edip başlat (ilk kurulum)
docker-compose up -d --build
```

### 2. Uygulama Kurulumu

```bash
# PHP container'ına gir
docker exec -it salonpro_php bash

# Composer bağımlılıklarını yükle
composer install

# .env dosyasını oluştur
cp .env.example .env

# Uygulama anahtarı oluştur
php artisan key:generate

# Veritabanı migration'ları çalıştır
php artisan migrate

# Seeder'ları çalıştır (opsiyonel)
php artisan db:seed
```

### 3. Önbellek Temizleme

```bash
# Container içinde
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## Kullanım

### Container Yönetimi

```bash
# Container'ları başlat
docker-compose up -d

# Container'ları durdur
docker-compose down

# Container'ları yeniden başlat
docker-compose restart

# Container loglarını görüntüle
docker-compose logs -f

# Belirli bir servisin loglarını görüntüle
docker-compose logs -f php
```

### PHP Container'ına Erişim

```bash
# Bash açma
docker exec -it salonpro_php bash

# Artisan komutları çalıştırma
docker exec -it salonpro_php php artisan migrate
docker exec -it salonpro_php php artisan cache:clear
```

### MySQL Veritabanına Erişim

```bash
# MySQL container'ına bağlan
docker exec -it salonpro_mysql mysql -u root -p
# Şifre: root

# veya
docker exec -it salonpro_mysql mysql -u salonpro -psecret salonpro
```

### Redis'e Erişim

```bash
# Redis CLI
docker exec -it salonpro_redis redis-cli

# Komutlar:
# PING
# KEYS *
# FLUSHALL (tüm cache'i temizle)
```

## URL'ler

- **Ana Uygulama**: http://localhost:8080
- **Vite Dev Server**: http://localhost:5173
- **Mailpit Web UI**: http://localhost:8025
- **MySQL**: localhost:3306
- **Redis**: localhost:6379

## Ortam Değişkenleri

`.env` dosyasında Docker için şu ayarları yapın:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=salonpro
DB_USERNAME=root
DB_PASSWORD=root

REDIS_HOST=redis
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```

## Sorun Giderme

### Port Çakışması

Portlar kullanımda ise `docker-compose.yml` dosyasındaki port numaralarını değiştirin:

```yaml
ports:
  - "8081:80"  # 8080 yerine 8081
```

### Permission Hataları

```bash
# Linux/Mac için storage ve bootstrap/cache dizinlerine yetki ver
docker exec -it salonpro_php chmod -R 775 storage bootstrap/cache
docker exec -it salonpro_php chown -R www:www storage bootstrap/cache
```

### Container'lar Başlatılamıyor

```bash
# Eski container'ları ve volume'ları temizle
docker-compose down -v
docker system prune -a

# Yeniden build et
docker-compose up -d --build
```

### Database Connection Error

```bash
# MySQL container'ın hazır olduğundan emin ol
docker-compose logs mysql

# Migration'ları tekrar çalıştır
docker exec -it salonpro_php php artisan migrate:fresh --seed
```

## Performans İpuçları

1. **OPcache**: Production'da OPcache aktif (php.ini'de yapılandırılmış)
2. **Redis**: Cache ve queue için Redis kullanılıyor
3. **MySQL**: InnoDB optimizasyonları yapılandırılmış
4. **Nginx**: Gzip sıkıştırma ve static asset caching aktif

## Güvenlik Notları

- Production ortamında `MYSQL_ROOT_PASSWORD` değiştirin
- `.env` dosyasını asla commit etmeyin
- SSL sertifikası ekleyin (production)
- Firewall kuralları yapılandırın
