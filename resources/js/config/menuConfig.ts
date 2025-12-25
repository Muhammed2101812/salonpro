/**
 * SalonPro Menü Yapılandırması
 * Sidebar navigasyonu için tüm menü öğeleri ve alt menüler
 */

export interface MenuItem {
    label: string;
    path?: string;
    icon?: string;
    children?: MenuItem[];
    badge?: string | number;
}

export const menuConfig: MenuItem[] = [
    // ─────────────────────────────────────────────────────────────
    // Ana Menüler
    // ─────────────────────────────────────────────────────────────
    {
        label: 'Ana Sayfa',
        path: '/',
        icon: 'HomeIcon'
    },
    {
        label: 'Şubeler',
        path: '/branches',
        icon: 'BuildingOfficeIcon'
    },

    // ─────────────────────────────────────────────────────────────
    // Müşteri Yönetimi
    // ─────────────────────────────────────────────────────────────
    {
        label: 'Müşteriler',
        icon: 'UsersIcon',
        children: [
            { label: 'Müşteri Listesi', path: '/customers' },
            { label: 'Kategoriler', path: '/customer-categories' },
            { label: 'Etiketler', path: '/customer-tags' },
            { label: 'Segmentler', path: '/customer-segments' },
            { label: 'Segment Üyeleri', path: '/customer-segment-members' },
            { label: 'Geri Bildirimler', path: '/customer-feedbacks' },
            { label: 'Notlar', path: '/customer-notes' },
            { label: 'RFM Analizi', path: '/customer-rfm-analyses' }
        ]
    },

    // ─────────────────────────────────────────────────────────────
    // Çalışan Yönetimi
    // ─────────────────────────────────────────────────────────────
    {
        label: 'Çalışanlar',
        icon: 'BriefcaseIcon',
        children: [
            { label: 'Çalışan Listesi', path: '/employees' },
            { label: 'Çalışma Takvimi', path: '/employees/schedule' },
            { label: 'Vardiyalar', path: '/employee-shifts' },
            { label: 'İzinler', path: '/employee-leaves' },
            { label: 'Devam Takibi', path: '/employee-attendances' },
            { label: 'Sertifikalar', path: '/employee-certifications' },
            { label: 'Yetenekler', path: '/employee-skills' },
            { label: 'Komisyonlar', path: '/employee-commissions' },
            { label: 'Performans', path: '/employee-performances' }
        ]
    },

    // ─────────────────────────────────────────────────────────────
    // Hizmet Yönetimi
    // ─────────────────────────────────────────────────────────────
    {
        label: 'Hizmetler',
        icon: 'SparklesIcon',
        children: [
            { label: 'Hizmet Listesi', path: '/services' },
            { label: 'Kategoriler', path: '/service-categories' },
            { label: 'Paketler', path: '/service-packages' },
            { label: 'Ek Hizmetler', path: '/service-addons' },
            { label: 'Şablonlar', path: '/service-templates' },
            { label: 'Gereksinimler', path: '/service-requirements' },
            { label: 'Değerlendirmeler', path: '/service-reviews' },
            { label: 'Fiyatlandırma Kuralları', path: '/service-pricing-rules' },
            { label: 'Fiyat Geçmişi', path: '/service-price-histories' }
        ]
    },

    // ─────────────────────────────────────────────────────────────
    // Randevu Yönetimi
    // ─────────────────────────────────────────────────────────────
    {
        label: 'Randevular',
        icon: 'CalendarDaysIcon',
        children: [
            { label: 'Randevu Listesi', path: '/appointments' },
            { label: 'Gruplar', path: '/appointment-groups' },
            { label: 'Grup Katılımcıları', path: '/appointments/group-participants' },
            { label: 'Tekrarlanan', path: '/appointment-recurrences' },
            { label: 'Hatırlatıcılar', path: '/appointment-reminders' },
            { label: 'Bekleme Listesi', path: '/appointment-waitlists' },
            { label: 'İptaller', path: '/appointment-cancellations' },
            { label: 'İptal Nedenleri', path: '/appointment-cancellation-reasons' },
            { label: 'Çakışmalar', path: '/appointment-conflicts' },
            { label: 'Geçmiş', path: '/appointment-histories' }
        ]
    },

    // ─────────────────────────────────────────────────────────────
    // Satış & Kasa
    // ─────────────────────────────────────────────────────────────
    {
        label: 'Satış & Kasa',
        icon: 'ShoppingCartIcon',
        children: [
            { label: 'Satışlar', path: '/sales' },
            { label: 'Kasalar', path: '/cash-registers' },
            { label: 'Kasa Oturumları', path: '/cash-register-sessions' },
            { label: 'Kasa İşlemleri', path: '/cash-register-transactions' }
        ]
    },

    // ─────────────────────────────────────────────────────────────
    // Ürün & Stok
    // ─────────────────────────────────────────────────────────────
    {
        label: 'Ürünler & Stok',
        icon: 'CubeIcon',
        children: [
            { label: 'Ürünler', path: '/products' },
            { label: 'Stok Hareketleri', path: '/inventory' },
            { label: 'Stok Transferleri', path: '/stock-transfers' },
            { label: 'Stok Uyarıları', path: '/stock-alerts' },
            { label: 'Stok Sayımları', path: '/stock-audits' },
            { label: 'Ürün Varyantları', path: '/product-variants' },
            { label: 'Ürün Paketleri', path: '/product-bundles' },
            { label: 'Ürün İndirimleri', path: '/product-discounts' },
            { label: 'Barkodlar', path: '/product-barcodes' },
            { label: 'Fiyat Geçmişi', path: '/product-price-histories' },
            { label: 'Stok Geçmişi', path: '/product-stock-histories' },
            { label: 'Özellikler', path: '/product-attributes' },
            { label: 'Özellik Değerleri', path: '/product-attribute-values' },
            { label: 'Kategori Hiyerarşisi', path: '/product-category-hierarchies' },
            { label: 'Görseller', path: '/product-images' }
        ]
    },

    // ─────────────────────────────────────────────────────────────
    // Tedarik
    // ─────────────────────────────────────────────────────────────
    {
        label: 'Tedarik',
        icon: 'TruckIcon',
        children: [
            { label: 'Tedarikçiler', path: '/suppliers' },
            { label: 'Satın Alma Siparişleri', path: '/purchase-orders' },
            { label: 'Sipariş Kalemleri', path: '/purchase-order-items' },
            { label: 'Tedarikçi Fiyatları', path: '/product-supplier-prices' }
        ]
    },

    // ─────────────────────────────────────────────────────────────
    // Finans
    // ─────────────────────────────────────────────────────────────
    {
        label: 'Finans',
        icon: 'BanknotesIcon',
        children: [
            { label: 'Faturalar', path: '/invoices' },
            { label: 'Fatura Kalemleri', path: '/invoice-items' },
            { label: 'Ödemeler', path: '/payments' },
            { label: 'Giderler', path: '/expenses' },
            { label: 'Banka Hesapları', path: '/bank-accounts' },
            { label: 'Banka İşlemleri', path: '/bank-transactions' },
            { label: 'Muhasebe Kayıtları', path: '/journal-entries' },
            { label: 'Hesap Planı', path: '/chart-of-accounts' },
            { label: 'Vergi Oranları', path: '/tax-rates' },
            { label: 'Para Birimleri', path: '/currencies' },
            { label: 'Döviz Kurları', path: '/exchange-rates' },
            { label: 'Bütçe Planları', path: '/budget-plans' },
            { label: 'Bütçe Kalemleri', path: '/budget-items' }
        ]
    },

    // ─────────────────────────────────────────────────────────────
    // Pazarlama & CRM
    // ─────────────────────────────────────────────────────────────
    {
        label: 'Pazarlama',
        icon: 'MegaphoneIcon',
        children: [
            { label: 'Kampanyalar', path: '/marketing-campaigns' },
            { label: 'Kampanya İstatistikleri', path: '/campaign-statistics' },
            { label: 'Kuponlar', path: '/coupons' },
            { label: 'Kupon Kullanımları', path: '/coupon-usages' },
            { label: 'Sadakat Programları', path: '/loyalty-programs' },
            { label: 'Sadakat Puanları', path: '/loyalty-points' },
            { label: 'Puan İşlemleri', path: '/loyalty-point-transactions' },
            { label: 'Potansiyel Müşteriler', path: '/leads' },
            { label: 'Müşteri Adayı Aktiviteleri', path: '/lead-activities' },
            { label: 'Referans Programları', path: '/referral-programs' },
            { label: 'Referanslar', path: '/referrals' }
        ]
    },

    // ─────────────────────────────────────────────────────────────
    // Raporlar & Analiz
    // ─────────────────────────────────────────────────────────────
    {
        label: 'Raporlar',
        icon: 'ChartBarIcon',
        children: [
            { label: 'Rapor Şablonları', path: '/report-templates' },
            { label: 'Rapor Çalıştırmaları', path: '/report-executions' },
            { label: 'Rapor Zamanlamaları', path: '/report-schedules' },
            { label: 'KPI Tanımları', path: '/kpi-definitions' },
            { label: 'KPI Değerleri', path: '/kpi-values' },
            { label: 'Performans Metrikleri', path: '/performance-metrics' },
            { label: 'Analitik Oturumları', path: '/analytics-sessions' },
            { label: 'Analitik Olayları', path: '/analytics-events' },
            { label: 'RFM Analizleri', path: '/rfm-analyses' }
        ]
    },

    // ─────────────────────────────────────────────────────────────
    // Bildirimler & İletişim
    // ─────────────────────────────────────────────────────────────
    {
        label: 'Bildirimler',
        icon: 'BellIcon',
        children: [
            { label: 'Bildirim Şablonları', path: '/notifications/templates' },
            { label: 'Bildirim Kayıtları', path: '/notification-logs' },
            { label: 'Bildirim Kuyruğu', path: '/notification-queues' },
            { label: 'Bildirim Tercihleri', path: '/notification-preferences' },
            { label: 'E-posta Sağlayıcıları', path: '/email-providers' },
            { label: 'SMS Sağlayıcıları', path: '/sms-providers' }
        ]
    },

    // ─────────────────────────────────────────────────────────────
    // Anketler
    // ─────────────────────────────────────────────────────────────
    {
        label: 'Anketler',
        icon: 'ClipboardDocumentListIcon',
        children: [
            { label: 'Anket Listesi', path: '/surveys' },
            { label: 'Anket Yanıtları', path: '/survey-responses' }
        ]
    },

    // ─────────────────────────────────────────────────────────────
    // Sistem Ayarları
    // ─────────────────────────────────────────────────────────────
    {
        label: 'Ayarlar',
        icon: 'Cog6ToothIcon',
        children: [
            { label: 'Genel Ayarlar', path: '/settings' },
            { label: 'Özel Alanlar', path: '/custom-fields' },
            { label: 'Özel Alan Değerleri', path: '/custom-field-values' },
            { label: 'Belge Şablonları', path: '/document-templates' },
            { label: 'Çeviriler', path: '/translations' },
            { label: 'Özellik Bayrakları', path: '/feature-flags' },
            { label: 'Webhooks', path: '/webhooks' },
            { label: 'Entegrasyonlar', path: '/integrations' },
            { label: 'Sistem Yedekleri', path: '/system-backups' },
            { label: 'Denetim Kayıtları', path: '/audit-logs' },
            { label: 'Aktivite Kayıtları', path: '/activity-logs' },
            { label: 'OAuth Sağlayıcıları', path: '/oauth-providers' },
            { label: 'OAuth Tokenları', path: '/oauth-tokens' },
            { label: 'Mobil Cihazlar', path: '/mobile-devices' },
            { label: 'Kullanıcı Tercihleri', path: '/user-preferences' }
        ]
    }
];

// Menü ikonlarını dinamik olarak import etmek için yardımcı
export const iconMap: Record<string, string> = {
    HomeIcon: 'HomeIcon',
    BuildingOfficeIcon: 'BuildingOfficeIcon',
    UsersIcon: 'UsersIcon',
    BriefcaseIcon: 'BriefcaseIcon',
    SparklesIcon: 'SparklesIcon',
    CalendarDaysIcon: 'CalendarDaysIcon',
    ShoppingCartIcon: 'ShoppingCartIcon',
    CubeIcon: 'CubeIcon',
    TruckIcon: 'TruckIcon',
    BanknotesIcon: 'BanknotesIcon',
    MegaphoneIcon: 'MegaphoneIcon',
    ChartBarIcon: 'ChartBarIcon',
    BellIcon: 'BellIcon',
    ClipboardDocumentListIcon: 'ClipboardDocumentListIcon',
    Cog6ToothIcon: 'Cog6ToothIcon'
};
