# SalonPro Frontend Dokümantasyonu

> **Güncelleme Tarihi:** 23 Aralık 2025  
> **Framework:** Vue 3 + TypeScript + Vite  
> **State Management:** Pinia  
> **UI Library:** Tailwind CSS + HeadlessUI + Heroicons

---

## 📁 Proje Yapısı

```
resources/js/
├── App.vue                 # Ana uygulama bileşeni (layout, sidebar, navbar)
├── app.ts                  # Uygulama giriş noktası
├── bootstrap.ts            # Bootstrap yapılandırması
├── components/             # Yeniden kullanılabilir bileşenler (41 dosya)
├── composables/           # Vue composable fonksiyonları (9 dosya)
├── locales/               # Çoklu dil desteği dosyaları
├── pages/                 # Sayfa bileşenleri
├── plugins/               # Vue eklentileri
├── router/                # Vue Router yapılandırması
├── services/              # API ve servis katmanı
├── stores/                # Pinia store dosyaları (117 dosya)
└── views/                 # Ana sayfa görünümleri (120+ klasör)
```

---

## 🗺️ Sayfalar (Routes)

### Ana Sayfalar

| Route | Sayfa | Açıklama |
|-------|-------|----------|
| `/login` | Login | Kullanıcı girişi |
| `/` | Dashboard | Ana gösterge paneli |
| `/branches` | Branches | Şube yönetimi |
| `/customers` | Customers | Müşteri yönetimi |
| `/customers/:id` | CustomerShow | Müşteri detay sayfası |
| `/employees` | Employees | Çalışan yönetimi |
| `/employees/schedule` | EmployeeSchedule | Çalışan programı |
| `/products` | Products | Ürün yönetimi |
| `/inventory` | Inventory | Stok hareketleri |
| `/expenses` | Expenses | Gider yönetimi |
| `/payments` | Payments | Ödeme yönetimi |
| `/sales` | Sales | Satış yönetimi |
| `/services` | Services | Hizmet yönetimi |
| `/appointments` | Appointments | Randevu yönetimi |
| `/settings` | Settings | Ayarlar |

### İkincil Sayfalar

| Route | Sayfa | Açıklama |
|-------|-------|----------|
| `/notifications/templates` | NotificationTemplates | Bildirim şablonları |
| `/appointments/group-participants` | AppointmentGroupParticipants | Grup randevu katılımcıları |
| `/custom-field-values` | CustomFieldValues | Özel alan değerleri |
| `/document-templates` | DocumentTemplates | Döküman şablonları |
| `/oauth-providers` | OauthProviders | OAuth sağlayıcıları |
| `/oauth-tokens` | OauthTokens | OAuth tokenları |
| `/mobile-devices` | MobileDevices | Mobil cihaz yönetimi |
| `/surveys` | Surveys | Anket yönetimi |
| `/survey-responses` | SurveyResponses | Anket yanıtları |

---

## 🧩 Bileşenler (Components)

### UI Bileşenleri (`components/ui/`)

| Bileşen | Dosya | Açıklama |
|---------|-------|----------|
| **Modal** | `Modal.vue` | Genel modal bileşeni (sm/md/lg/xl/full boyutları) |
| **ConfirmDialog** | `ConfirmDialog.vue` | Onay dialogu (danger/warning/info/success tipleri) |
| **Button** | `Button.vue` | Özelleştirilebilir buton |
| **Input** | `Input.vue` | Form input bileşeni |
| **FormField** | `FormField.vue` | Etiketli form alanı |
| **FormSelect** | `FormSelect.vue` | Dropdown seçici |
| **Dropdown** | `Dropdown.vue` | Dropdown menü |
| **BranchSwitcher** | `BranchSwitcher.vue` | Şube değiştirici (navbar) |
| **LanguageSwitcher** | `LanguageSwitcher.vue` | Dil değiştirici |
| **Breadcrumb** | `Breadcrumb.vue` | Sayfa yolu gösterici |
| **EmptyState** | `EmptyState.vue` | Boş durum gösterici |
| **ErrorPage** | `ErrorPage.vue` | Hata sayfası |
| **SkeletonLoader** | `SkeletonLoader.vue` | Yükleme animasyonu |
| **PageSkeleton** | `PageSkeleton.vue` | Sayfa yükleme iskeleti |
| **ToastContainer** | `ToastContainer.vue` | Bildirim toast'ları |
| **Currency** | `Currency.vue` | Para birimi gösterici |
| **MobileNav** | `MobileNav.vue` | Mobil navigasyon |
| **BottomSheet** | `BottomSheet.vue` | Alt sayfa modal (mobil) |
| **PullToRefresh** | `PullToRefresh.vue` | Çekip yenileme (mobil) |
| **SwipeAction** | `SwipeAction.vue` | Kaydırma aksiyonu (mobil) |
| **FormWrapper** | `FormWrapper.vue` | Form sarmalayıcı |

### Form Bileşenleri (`components/form/`)

| Bileşen | Dosya | Açıklama |
|---------|-------|----------|
| **TextInput** | `TextInput.vue` | Metin girişi |
| **TextareaInput** | `TextareaInput.vue` | Çok satırlı metin |
| **SelectInput** | `SelectInput.vue` | Seçici input |
| **RelationshipSelect** | `RelationshipSelect.vue` | İlişki seçici (API'den veri çeker) |
| **BranchSelect** | `BranchSelect.vue` | Şube seçici |
| **CustomerSelect** | `CustomerSelect.vue` | Müşteri seçici |
| **EmployeeSelect** | `EmployeeSelect.vue` | Çalışan seçici |
| **ProductSelect** | `ProductSelect.vue` | Ürün seçici |
| **ServiceSelect** | `ServiceSelect.vue` | Hizmet seçici |

### Grafik Bileşenleri (`components/charts/`)

| Bileşen | Dosya | Açıklama |
|---------|-------|----------|
| **BarChart** | `BarChart.vue` | Çubuk grafik (Chart.js) |
| **LineChart** | `LineChart.vue` | Çizgi grafik (Chart.js) |
| **DoughnutChart** | `DoughnutChart.vue` | Halka grafik (Chart.js) |

### Genel Bileşenler

| Bileşen | Dosya | Açıklama |
|---------|-------|----------|
| **FormModal** | `FormModal.vue` | Form içeren modal (Kaydet/İptal butonlu) |
| **ValidatedForm** | `ValidatedForm.vue` | Doğrulama destekli form |

---

## 🔄 Modaller

### Temel Modal Tipleri

#### 1. FormModal (`components/FormModal.vue`)
- **Props:** `modelValue` (boolean), `title` (string)
- **Events:** `update:modelValue`, `save`
- **Kullanım:** CRUD operasyonları için standart form modal

```vue
<FormModal v-model="showModal" title="Yeni Müşteri" @save="handleSave">
  <!-- Form içeriği -->
</FormModal>
```

#### 2. Modal (`components/ui/Modal.vue`)
- **Props:** `modelValue`, `title`, `size` (sm/md/lg/xl/full), `closable`
- **Slots:** `header`, `default`, `footer`
- **Kullanım:** Genel amaçlı modal

#### 3. ConfirmDialog (`components/ui/ConfirmDialog.vue`)
- **Props:** `isOpen`, `title`, `message`, `type` (danger/warning/info/success), `confirmText`, `cancelText`
- **Events:** `confirm`, `cancel`, `close`
- **Kullanım:** Silme onayı, uyarılar

```vue
<ConfirmDialog
  :is-open="showConfirm"
  title="Silme Onayı"
  message="Bu kaydı silmek istediğinizden emin misiniz?"
  type="danger"
  @confirm="handleDelete"
  @close="showConfirm = false"
/>
```

### Sayfa İçi Modaller

Her sayfada (Index.vue) inline modal yapısı kullanılmaktadır:

| Sayfa | Modal İçeriği |
|-------|---------------|
| **Customers/Index.vue** | Müşteri ekleme/düzenleme formu |
| **Appointments/Index.vue** | Randevu ekleme/düzenleme formu |
| **Employees/Index.vue** | Çalışan ekleme/düzenleme formu |
| **Products/Index.vue** | Ürün ekleme/düzenleme formu |
| **Services/Index.vue** | Hizmet ekleme/düzenleme formu |
| **Expenses/Index.vue** | Gider ekleme/düzenleme formu |
| **Payments/Index.vue** | Ödeme ekleme/düzenleme formu |
| **Sales/Index.vue** | Satış ekleme/düzenleme formu |
| **Branches/Index.vue** | Şube ekleme/düzenleme formu |

---

## 📊 Dashboard

Dashboard sayfası (`views/Dashboard.vue`) aşağıdaki bileşenleri içerir:

### İstatistik Kartları
- **Toplam Gelir** (yeşil)
- **Toplam Gider** (kırmızı)
- **Net Kar/Zarar** (mavi)
- **Toplam Satış** (mor)
- **Toplam Müşteri** (teal)
- **Bugünkü Randevu** (sarı)
- **Toplam Ürün** (mor)
- **Aktif Hizmet** (cam göbeği)

### Grafikler
1. **Gelir vs Gider** - BarChart
2. **Hizmet Dağılımı** - DoughnutChart
3. **Aylık Satış Trendi** - LineChart

### Listeler
- **Bugünkü Randevular** - Hızlı görünüm listesi
- **Son Ödemeler** - Yapılan ödemelerin listesi
- **Düşük Stoklu Ürünler** - Stok uyarısı

### Dönem Filtresi
- Bugün
- Bu Hafta
- Bu Ay
- Bu Yıl

---

## 👥 Müşteriler Sayfası

### Özellikler

#### İstatistik Kartları
- Toplam Müşteri
- Yeni (Bu Ay)
- Sadık Müşteri
- Riskli (90+ gün ziyaret etmemiş)
- Bu Ay Doğum Günü

#### Filtreler
- **Arama:** Ad, telefon, e-posta
- **Şube Filtresi:** Dropdown
- **Cinsiyet Filtresi:** Erkek/Kadın
- **Segment Filtresi:** Yeni/Sadık/VIP/Riskli

#### Görünüm Modları
- **Grid:** Kart görünümü
- **Tablo:** Liste görünümü

#### Müşteri Segmentasyonu
| Segment | Kriter |
|---------|--------|
| Yeni | 30 gün içinde kaydolmuş |
| VIP | 10+ ziyaret VE 5000₺+ harcama |
| Sadık | 5+ ziyaret |
| Riskli | Son ziyaretten 90+ gün geçmiş |

#### Form Alanları (Modal)
- Ad* / Soyad*
- Telefon* / E-posta
- Şube*
- Cinsiyet (Erkek/Kadın toggle)
- Doğum Tarihi
- Notlar

---

## 📅 Randevular Sayfası

### Özellikler

#### İstatistik Kartları
- Bugün
- Bekleyen
- Onaylı
- Tamamlanan
- Bugün Gelir

#### Görünüm Modları
1. **Takvim Görünümü**
   - Aylık takvim
   - Ay değiştirme
   - Bugüne git
   - Günlük randevu sayısı
   - **Drag & Drop** desteği (randevu taşıma)

2. **Liste Görünümü**
   - Tablo formatı
   - Durum filtresi
   - Müşteri/Çalışan araması

#### Durum Tipleri
| Durum | Renk |
|-------|------|
| Bekliyor | Sarı |
| Onaylı | Mavi |
| Tamamlandı | Yeşil |
| İptal Edildi | Kırmızı |

#### Form Alanları (Modal)
- Şube* / Müşteri*
- Çalışan* / Hizmet*
- Randevu Tarihi*
- Süre (otomatik) / Fiyat*
- Durum seçici (4 buton)
- Notlar

---

## 🏢 Şubeler Sayfası

### Şube Kartları
- Şube adı
- Adres
- Telefon
- Çalışan sayısı
- Durum (Aktif/Pasif)

### Form Alanları
- Ad*
- Adres
- Telefon
- E-posta
- Çalışma Saatleri
- Durum

---

## 🛍️ Ürünler Sayfası

### Özellikler
- Ürün listesi (grid/tablo)
- Stok takibi
- Fiyat yönetimi
- Kategori filtresi
- Barkod desteği

### Form Alanları
- Ad* / SKU*
- Açıklama
- Fiyat* / Maliyet
- Stok Miktarı*
- Kategori
- Barkod
- Resim

---

## 📦 Stok Hareketleri Sayfası

### Hareket Tipleri
- Giriş (yeşil)
- Çıkış (kırmızı)
- Transfer (mavi)
- Sayım (sarı)

### Form Alanları
- Ürün*
- Hareket Tipi*
- Miktar*
- Şube*
- Tarih
- Açıklama

---

## 💰 Giderler Sayfası

### İstatistikler
- Toplam Gider
- Bu Ay
- Bu Hafta
- Bugün

### Form Alanları
- Başlık*
- Kategori*
- Tutar*
- Ödeme Yöntemi
- Tarih*
- Açıklama
- Fatura/Makbuz

---

## 💳 Ödemeler Sayfası

### Ödeme Yöntemleri
- Nakit
- Kredi Kartı
- Banka Havalesi
- Çek

### Form Alanları
- Müşteri*
- Tutar*
- Ödeme Yöntemi*
- Tarih*
- Referans No
- Açıklama

---

## 🧾 Satışlar Sayfası

### Özellikler
- Satış listesi
- Müşteri bilgisi
- Ürün/Hizmet detayı
- Toplam tutar
- Ödeme durumu

### Form Alanları
- Müşteri*
- Ürünler/Hizmetler*
- İndirim
- Vergi
- Toplam Tutar
- Ödeme Yöntemi

---

## ✂️ Hizmetler Sayfası

### Hizmet Kartları
- Hizmet adı
- Süre (dakika)
- Fiyat
- Kategori
- Durum (Aktif/Pasif)

### Form Alanları
- Ad*
- Açıklama
- Süre (dakika)*
- Fiyat*
- Kategori
- Durum

---

## ⚙️ Ayarlar Sayfası

### Ayar Kategorileri
- Genel Ayarlar
- Şube Ayarları
- Bildirim Ayarları
- Entegrasyon Ayarları
- Kullanıcı Ayarları

---

## 🔧 Composables

| Composable | Dosya | Açıklama |
|------------|-------|----------|
| **useApi** | `useApi.ts` | API istekleri yönetimi |
| **useCurrency** | `useCurrency.ts` | Para birimi formatlaması |
| **useI18n** | `useI18n.ts` | Çoklu dil desteği |
| **useKeyboardShortcuts** | `useKeyboardShortcuts.ts` | Klavye kısayolları |
| **useRelationships** | `useRelationships.ts` | İlişki yönetimi |
| **useToast** | `useToast.ts` | Toast bildirimleri |
| **useValidation** | `useValidation.ts` | Form doğrulama |

---

## 🗄️ Stores (Pinia)

### Ana Store'lar

| Store | Dosya | Açıklama |
|-------|-------|----------|
| **auth** | `auth.ts` | Kimlik doğrulama |
| **branch** | `branch.ts` | Şube yönetimi |
| **customer** | `customer.ts` | Müşteri yönetimi |
| **employee** | `employee.ts` | Çalışan yönetimi |
| **appointment** | `appointment.ts` | Randevu yönetimi |
| **product** | `product.ts` | Ürün yönetimi |
| **service** | `service.ts` | Hizmet yönetimi |
| **inventory** | `inventory.ts` | Stok yönetimi |
| **expense** | `expense.ts` | Gider yönetimi |
| **payment** | `payment.ts` | Ödeme yönetimi |
| **sale** | `sale.ts` | Satış yönetimi |
| **setting** | `setting.ts` | Ayarlar |

### Diğer Store'lar (117 toplam)
- Activity logs, Analytics, Audit logs
- Bank accounts, Budget, Campaign
- Cash register, Coupons, Currency
- Custom fields, Document templates
- Employee (attendance, certifications, commissions, leaves, performance, schedules, shifts, skills)
- Exchange rates, Feature flags
- Integrations, Invoices, Journal entries
- KPI, Leads, Loyalty programs
- Marketing campaigns, Mobile devices
- Notifications (logs, preferences, queues, templates)
- OAuth, Performance metrics
- Product (attributes, barcodes, bundles, discounts, images, price histories, stock histories, variants)
- Purchase orders, Referrals
- Reports (executions, schedules, templates)
- RFM analysis, Segments
- Service (addons, categories, packages, price histories, pricing rules, requirements, reviews, templates)
- SMS providers, Stock (alerts, audits, transfers)
- Suppliers, Surveys, System backups
- Tax rates, Translations, User preferences, Webhooks

---

## 🎨 Tasarım Sistemi

### Renkler
- **Primary:** Blue (#3B82F6)
- **Success:** Green (#22C55E)
- **Warning:** Yellow (#F59E0B)
- **Danger:** Red (#EF4444)
- **Info:** Indigo (#6366F1)

### Tipografi
- **Font:** Inter (Google Fonts)
- **Başlıklar:** font-bold, text-gray-900
- **Metinler:** text-gray-600
- **Küçük:** text-sm, text-gray-500

### Kart Stili
```css
bg-white rounded-xl shadow-sm border border-gray-100
```

### Buton Stilleri
```css
/* Primary */
bg-blue-600 hover:bg-blue-700 text-white

/* Success */
bg-green-600 hover:bg-green-700 text-white

/* Danger */
bg-red-600 hover:bg-red-700 text-white

/* Secondary */
bg-gray-100 hover:bg-gray-200 text-gray-700
```

---

## 📱 Mobil Özellikler

### Responsive Tasarım
- Grid sistemi: `grid-cols-1 md:grid-cols-2 lg:grid-cols-4`
- Sidebar: Mobilde gizli, toggle ile açılır
- Tablolar: Yatay scroll

### Mobil Bileşenler
- `MobileNav.vue` - Alt navigasyon
- `BottomSheet.vue` - Alt sayfa modal
- `PullToRefresh.vue` - Çekip yenile
- `SwipeAction.vue` - Kaydırma aksiyonu

---

## 🔐 Kimlik Doğrulama

### Route Guard
```typescript
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('auth_token')
  if (to.meta.requiresAuth && !token) {
    next('/login')
  } else {
    next()
  }
})
```

### Auth Store
- `login(email, password)`
- `logout()`
- `fetchProfile()`
- `isAuthenticated` (computed)
- `user` (state)

---

## 🌍 Çoklu Dil Desteği

### Dil Dosyaları
- `locales/tr.json` - Türkçe
- `locales/en.json` - İngilizce

### Kullanım
```typescript
import { useI18n } from '@/composables/useI18n'
const { t } = useI18n()
// {{ t('common.save') }}
```

---

## 📈 Chart.js Entegrasyonu

### Kurulum
```bash
npm install chart.js vue-chartjs
```

### Bileşenler
```vue
<BarChart :labels="labels" :datasets="datasets" />
<LineChart :labels="labels" :datasets="datasets" />
<DoughnutChart :labels="labels" :data="data" />
```

---

## 🛠️ Geliştirme Komutları

```bash
# Geliştirme sunucusu
npm run dev

# Production build
npm run build

# Type check
npm run type-check
```

---

## 📝 Notlar

1. **Tüm sayfalar** `meta: { requiresAuth: true }` ile korunmaktadır
2. **Route prefetch** kritik rotalar için otomatik yapılır (Dashboard, Customers, Appointments, Sales)
3. **Form validasyonu** `useValidation` composable ile yapılır
4. **API istekleri** `useApi` composable üzerinden yapılır
5. **Para formatlaması** `useCurrency` composable kullanır
6. **Toast bildirimleri** `useToast` composable ile gösterilir

---

> Bu doküman, SalonPro frontend yapısının kapsamlı bir özetidir. Güncellemeler için commit geçmişine bakınız.
