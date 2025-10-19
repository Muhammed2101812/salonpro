import * as yup from 'yup'

/**
 * Common validation schemas for SalonPro
 */

// Customer validation schema
export const customerSchema = yup.object({
  first_name: yup
    .string()
    .required()
    .max(255)
    .label('Ad'),
  last_name: yup
    .string()
    .required()
    .max(255)
    .label('Soyad'),
  email: yup
    .string()
    .email()
    .nullable()
    .max(255)
    .label('E-posta'),
  phone: yup
    .string()
    .required()
    .matches(/^[0-9+\-\s()]+$/, 'Geçerli bir telefon numarası giriniz')
    .label('Telefon'),
  address: yup
    .string()
    .nullable()
    .max(500)
    .label('Adres'),
  city: yup
    .string()
    .nullable()
    .max(100)
    .label('Şehir'),
  notes: yup
    .string()
    .nullable()
    .label('Notlar'),
})

// Service validation schema
export const serviceSchema = yup.object({
  name: yup
    .string()
    .required()
    .max(255)
    .label('Hizmet Adı'),
  description: yup
    .string()
    .nullable()
    .label('Açıklama'),
  price: yup
    .number()
    .required()
    .positive('Fiyat pozitif bir değer olmalıdır')
    .label('Fiyat'),
  duration: yup
    .number()
    .required()
    .positive('Süre pozitif bir değer olmalıdır')
    .integer('Süre tam sayı olmalıdır')
    .label('Süre (dakika)'),
  is_active: yup
    .boolean()
    .default(true)
    .label('Aktif'),
})

// Appointment validation schema
export const appointmentSchema = yup.object({
  customer_id: yup
    .number()
    .required()
    .positive()
    .label('Müşteri'),
  service_id: yup
    .number()
    .required()
    .positive()
    .label('Hizmet'),
  employee_id: yup
    .number()
    .required()
    .positive()
    .label('Çalışan'),
  date: yup
    .date()
    .required()
    .min(new Date(), 'Tarih gelecekte olmalıdır')
    .label('Tarih'),
  time: yup
    .string()
    .required()
    .matches(/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/, 'Geçerli bir saat formatı giriniz (ÖR: 14:30)')
    .label('Saat'),
  notes: yup
    .string()
    .nullable()
    .label('Notlar'),
})

// Product validation schema
export const productSchema = yup.object({
  name: yup
    .string()
    .required()
    .max(255)
    .label('Ürün Adı'),
  sku: yup
    .string()
    .required()
    .max(100)
    .label('Stok Kodu'),
  description: yup
    .string()
    .nullable()
    .label('Açıklama'),
  purchase_price: yup
    .number()
    .required()
    .positive('Alış fiyatı pozitif bir değer olmalıdır')
    .label('Alış Fiyatı'),
  sale_price: yup
    .number()
    .required()
    .positive('Satış fiyatı pozitif bir değer olmalıdır')
    .test('greater-than-purchase', 'Satış fiyatı alış fiyatından büyük olmalıdır', function (value) {
      const { purchase_price } = this.parent
      return !value || !purchase_price || value >= purchase_price
    })
    .label('Satış Fiyatı'),
  stock_quantity: yup
    .number()
    .required()
    .min(0, 'Stok miktarı negatif olamaz')
    .integer('Stok miktarı tam sayı olmalıdır')
    .label('Stok Miktarı'),
  min_stock_level: yup
    .number()
    .nullable()
    .min(0, 'Minimum stok seviyesi negatif olamaz')
    .integer('Minimum stok seviyesi tam sayı olmalıdır')
    .label('Minimum Stok Seviyesi'),
  is_active: yup
    .boolean()
    .default(true)
    .label('Aktif'),
})

// Expense validation schema
export const expenseSchema = yup.object({
  category_id: yup
    .number()
    .required()
    .positive()
    .label('Kategori'),
  amount: yup
    .number()
    .required()
    .positive('Tutar pozitif bir değer olmalıdır')
    .label('Tutar'),
  date: yup
    .date()
    .required()
    .label('Tarih'),
  description: yup
    .string()
    .required()
    .max(500)
    .label('Açıklama'),
  vendor: yup
    .string()
    .nullable()
    .max(255)
    .label('Tedarikçi'),
  receipt_number: yup
    .string()
    .nullable()
    .max(100)
    .label('Fiş/Fatura No'),
})

// Payment validation schema
export const paymentSchema = yup.object({
  appointment_id: yup
    .number()
    .nullable()
    .positive()
    .label('Randevu'),
  sale_id: yup
    .number()
    .nullable()
    .positive()
    .label('Satış'),
  amount: yup
    .number()
    .required()
    .positive('Tutar pozitif bir değer olmalıdır')
    .label('Tutar'),
  payment_method: yup
    .string()
    .required()
    .oneOf(['cash', 'credit_card', 'debit_card', 'bank_transfer'], 'Geçersiz ödeme yöntemi')
    .label('Ödeme Yöntemi'),
  notes: yup
    .string()
    .nullable()
    .label('Notlar'),
})

// Login validation schema
export const loginSchema = yup.object({
  email: yup
    .string()
    .required()
    .email()
    .label('E-posta'),
  password: yup
    .string()
    .required()
    .min(8)
    .label('Şifre'),
  remember: yup
    .boolean()
    .default(false)
    .label('Beni Hatırla'),
})

// User validation schema
export const userSchema = yup.object({
  name: yup
    .string()
    .required()
    .max(255)
    .label('Ad Soyad'),
  email: yup
    .string()
    .required()
    .email()
    .max(255)
    .label('E-posta'),
  password: yup
    .string()
    .when('$isUpdate', {
      is: false,
      then: (schema) => schema.required().min(8),
      otherwise: (schema) => schema.nullable().min(8),
    })
    .label('Şifre'),
  password_confirmation: yup
    .string()
    .when('password', {
      is: (val: string) => val && val.length > 0,
      then: (schema) => schema.required().oneOf([yup.ref('password')], 'Şifreler eşleşmiyor'),
      otherwise: (schema) => schema.nullable(),
    })
    .label('Şifre Tekrarı'),
  branch_id: yup
    .number()
    .required()
    .positive()
    .label('Şube'),
  role: yup
    .string()
    .required()
    .label('Rol'),
})

/**
 * Helper function to convert Yup errors to VeeValidate format
 */
export function toFieldErrors(error: yup.ValidationError) {
  const errors: Record<string, string> = {}

  error.inner.forEach((err) => {
    if (err.path) {
      errors[err.path] = err.message
    }
  })

  return errors
}

/**
 * Helper to create async validator for VeeValidate
 */
export function createYupValidator(schema: yup.AnyObjectSchema) {
  return async (values: Record<string, any>) => {
    try {
      await schema.validate(values, { abortEarly: false })
      return { valid: true }
    } catch (error) {
      if (error instanceof yup.ValidationError) {
        return {
          valid: false,
          errors: toFieldErrors(error),
        }
      }
      throw error
    }
  }
}
