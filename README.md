# SalonPro - Profesyonel Salon Yönetim Sistemi

Enterprise düzeyde bir güzellik salonu ve spa yönetim sistemi.

## 🚀 Teknoloji Stack

- **Backend:** Laravel 12, PHP 8.3+
- **Frontend:** Vue.js 3 (Composition API), Pinia, Tailwind CSS 3
- **Database:** MySQL 8.0+
- **Cache:** Redis 7
- **Build:** Vite

## 📦 Kurulum

```bash
# Bağımlılıkları yükle
composer install
npm install

# Environment dosyasını oluştur
cp .env.example .env
php artisan key:generate

# Veritabanını hazırla
php artisan migrate --seed

# Frontend'i build et
npm run build

# Development sunucusunu başlat
php artisan serve
```

## 🏗️ Mimari

Proje **Repository Pattern** ve **Service Layer** kullanmaktadır:

```
Controller → Service → Repository → Model
```

### Klasör Yapısı

```
app/
├── Http/Controllers/    # API Controllers
├── Services/            # İş mantığı
├── Repositories/        # Veri erişim katmanı
├── Models/              # Eloquent modelleri
├── Policies/            # Authorization
├── Events/              # Event sınıfları
├── Listeners/           # Event listeners
└── Jobs/                # Queue jobs
```

## 📋 Modüller

1. **Kullanıcı Yönetimi** - Roller, izinler, 2FA
2. **Müşteri Yönetimi (CRM)** - Profiller, segmentasyon
3. **Personel Yönetimi** - Takvim, komisyon, izin
4. **Hizmet Yönetimi** - Katalog, paketler, fiyatlandırma
5. **Randevu Yönetimi** - Takvim, hatırlatmalar
6. **Ürün & Stok** - Envanter, satın alma
7. **Finansal Yönetim** - Kasa, ödemeler, faturalar
8. **Raporlama** - Dashboard, KPI'lar
9. **Pazarlama** - Kampanyalar, sadakat programları

## 🔐 Güvenlik

- Laravel Sanctum ile API authentication
- Spatie Permission ile RBAC
- Policy-based authorization
- Input validation with Form Requests

## 📝 API Dokümantasyonu

API endpoint'leri `/api/v1/` prefix'i altındadır.

```bash
# Route listesini görüntüle
php artisan route:list
```

## 🧪 Test

```bash
# Tüm testleri çalıştır
php artisan test

# Belirli bir test
php artisan test --filter=CustomerTest
```

## 📄 Lisans

Bu proje özel lisans altındadır.
