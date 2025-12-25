/**
 * Export Utility - Excel ve PDF export işlemleri
 */
import * as XLSX from 'xlsx';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';

export interface ExportColumn {
    key: string;
    label: string;
    width?: number;
}

export interface ExportOptions {
    filename: string;
    title?: string;
    columns: ExportColumn[];
    data: Record<string, any>[];
}

/**
 * Veriyi Excel formatında dışa aktar
 */
export function exportToExcel(options: ExportOptions): void {
    const { filename, columns, data } = options;

    // Header satırı oluştur
    const headers = columns.map(col => col.label);

    // Veriyi düz dizi formatına çevir
    const rows = data.map(row =>
        columns.map(col => {
            const value = row[col.key];
            // Null/undefined kontrolü
            if (value === null || value === undefined) return '';
            // Object ise string'e çevir
            if (typeof value === 'object') return JSON.stringify(value);
            return value;
        })
    );

    // Worksheet oluştur
    const ws = XLSX.utils.aoa_to_sheet([headers, ...rows]);

    // Sütun genişliklerini ayarla
    const colWidths = columns.map(col => ({ wch: col.width || 15 }));
    ws['!cols'] = colWidths;

    // Workbook oluştur
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Veri');

    // Dosyayı indir
    XLSX.writeFile(wb, `${filename}.xlsx`);
}

/**
 * Türkçe karakterleri ASCII karşılıklarına çevir (PDF font desteği için)
 */
function turkishToAscii(text: string): string {
    if (!text) return '';
    const turkishMap: Record<string, string> = {
        'ş': 's', 'Ş': 'S',
        'ğ': 'g', 'Ğ': 'G',
        'ü': 'u', 'Ü': 'U',
        'ö': 'o', 'Ö': 'O',
        'ç': 'c', 'Ç': 'C',
        'ı': 'i', 'İ': 'I'
    };
    return text.replace(/[şŞğĞüÜöÖçÇıİ]/g, char => turkishMap[char] || char);
}

/**
 * Veriyi PDF formatında dışa aktar
 */
export function exportToPDF(options: ExportOptions): void {
    const { filename, title, columns, data } = options;

    // PDF oluştur (A4 landscape)
    const doc = new jsPDF({
        orientation: 'landscape',
        unit: 'mm',
        format: 'a4'
    });

    // Başlık ekle
    if (title) {
        doc.setFontSize(16);
        doc.text(turkishToAscii(title), 14, 15);
    }

    // Tablo başlıkları (Türkçe karakterler ASCII'ye çevrildi)
    const headers = columns.map(col => turkishToAscii(col.label));

    // Tablo verileri (Türkçe karakterler ASCII'ye çevrildi)
    const tableData = data.map(row =>
        columns.map(col => {
            const value = row[col.key];
            if (value === null || value === undefined) return '';
            if (typeof value === 'object') return turkishToAscii(JSON.stringify(value));
            return turkishToAscii(String(value));
        })
    );

    // AutoTable ile tablo oluştur
    autoTable(doc, {
        head: [headers],
        body: tableData,
        startY: title ? 25 : 10,
        styles: {
            fontSize: 8,
            cellPadding: 2,
        },
        headStyles: {
            fillColor: [59, 130, 246], // primary blue
            textColor: 255,
            fontStyle: 'bold',
        },
        alternateRowStyles: {
            fillColor: [249, 250, 251], // gray-50
        },
        margin: { top: 10, right: 10, bottom: 10, left: 10 },
    });

    // Tarih ekle (footer)
    const pageCount = doc.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(128);
        const now = new Date().toLocaleString('tr-TR');
        doc.text(turkishToAscii(`Olusturulma: ${now} - Sayfa ${i}/${pageCount}`), 14, doc.internal.pageSize.height - 10);
    }

    // Dosyayı indir
    doc.save(`${filename}.pdf`);
}

/**
 * Para birimi formatla
 */
export function formatCurrency(amount: number | string): string {
    const num = typeof amount === 'string' ? parseFloat(amount) : amount;
    return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(num || 0);
}

/**
 * Tarih formatla
 */
export function formatDate(dateString: string): string {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('tr-TR');
}

/**
 * Tarih ve saat formatla
 */
export function formatDateTime(dateString: string): string {
    if (!dateString) return '';
    return new Date(dateString).toLocaleString('tr-TR');
}
