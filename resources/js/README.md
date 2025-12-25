# SalonPro - Salon Yönetim Sistemi

Modern ve kapsamlı salon yönetim sistemi. Vue.js 3, Pinia ve Tailwind CSS ile geliştirildi.

## 🚀 Özellikler

### Ana Modüller
- **Dashboard** - Gerçek zamanlı istatistikler ve grafikler
- **Müşteri Yönetimi** - Müşteri bilgileri, geçmiş ve segmentasyon
- **Randevu Sistemi** - Takvim görünümü ve hatırlatmalar
- **Satış & POS** - Satış işlemleri ve ödeme yönetimi
- **Ürün Yönetimi** - Stok takibi ve barkod sistemi
- **Hizmet Kataloğu** - Hizmetler ve fiyatlandırma
- **Çalışan Yönetimi** - Personel ve mesai planlaması
- **Finansal Modül** - Gelir, gider ve raporlama

### Teknik Özellikler
- ✅ 78+ modernize edilmiş sayfa
- ✅ Chart.js ile interaktif grafikler
- ✅ Lazy loading ve code splitting
- ✅ Toast notifications
- ✅ Confirm dialogs
- ✅ Keyboard shortcuts
- ✅ Skeleton loaders
- ✅ TypeScript desteği

## 🛠 Teknoloji Stack

| Teknoloji | Versiyon |
|-----------|----------|
| Vue.js | 3.5 |
| Pinia | 3.0 |
| Tailwind CSS | 4.0 |
| Vite | 7.x |
| TypeScript | 5.9 |
| Chart.js | 4.5 |
| Heroicons | 2.2 |

## 📦 Kurulum

```bash
# Bağımlılıkları yükle
npm install

# Development server
npm run dev

# Production build
npm run build

# Testleri çalıştır
npm run test

# Test coverage
npm run test:coverage
```

## 📁 Proje Yapısı

```
resources/js/
├── components/
│   ├── charts/          # Chart.js bileşenleri
│   │   ├── BarChart.vue
│   │   ├── LineChart.vue
│   │   └── DoughnutChart.vue
│   └── ui/              # UI bileşenleri
│       ├── Button.vue
│       ├── ConfirmDialog.vue
│       ├── EmptyState.vue
│       ├── ErrorPage.vue
│       ├── Breadcrumb.vue
│       ├── ToastContainer.vue
│       ├── SkeletonLoader.vue
│       └── PageSkeleton.vue
├── composables/         # Vue composables
│   ├── useApi.ts
│   ├── useToast.ts
│   └── useKeyboardShortcuts.ts
├── services/
│   └── api.ts           # API servisi (retry, timeout)
├── stores/              # Pinia stores (117+)
├── views/               # Sayfa bileşenleri (78+)
└── router/
    └── index.ts         # Vue Router
```

## ⌨️ Klavye Kısayolları

| Kısayol | Açıklama |
|---------|----------|
| `Alt + D` | Dashboard |
| `Alt + C` | Müşteriler |
| `Alt + A` | Randevular |
| `Alt + S` | Satışlar |
| `Alt + P` | Ürünler |

## 🧪 Test

```bash
# Watch modunda test
npm run test

# Tek seferlik test
npm run test:run

# Coverage raporu
npm run test:coverage
```

## 📊 Bundle Analizi

| Metrik | Değer |
|--------|-------|
| Bundle Size | 288 kB |
| Gzip Size | 103 kB |
| Build Time | ~6 sn |

## 🔧 Yapılandırma

### API Ayarları
```typescript
// services/api.ts
const API_CONFIG = {
  baseURL: '/api/v1',
  timeout: 30000,      // 30 sn
  retryAttempts: 3,    // 3 deneme
  retryDelay: 1000,    // 1 sn
};
```

## 📝 Lisans

Bu proje özel lisans altındadır.
