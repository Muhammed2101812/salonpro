import { configure } from 'vee-validate'
import { localize, setLocale } from '@vee-validate/i18n'
import tr from '@vee-validate/i18n/dist/locale/tr.json'
import en from '@vee-validate/i18n/dist/locale/en.json'
import * as yup from 'yup'

// Configure Yup locale for Turkish
yup.setLocale({
  mixed: {
    default: 'Geçersiz değer',
    required: '${path} alanı zorunludur',
    notType: '${path} geçerli bir ${type} olmalıdır',
  },
  string: {
    email: 'Geçerli bir e-posta adresi giriniz',
    min: '${path} en az ${min} karakter olmalıdır',
    max: '${path} en fazla ${max} karakter olmalıdır',
    url: 'Geçerli bir URL giriniz',
    uuid: 'Geçerli bir UUID giriniz',
  },
  number: {
    min: '${path} en az ${min} olmalıdır',
    max: '${path} en fazla ${max} olmalıdır',
    positive: '${path} pozitif bir sayı olmalıdır',
    negative: '${path} negatif bir sayı olmalıdır',
    integer: '${path} tam sayı olmalıdır',
  },
  date: {
    min: '${path} ${min} tarihinden sonra olmalıdır',
    max: '${path} ${max} tarihinden önce olmalıdır',
  },
  array: {
    min: 'En az ${min} öğe seçmelisiniz',
    max: 'En fazla ${max} öğe seçebilirsiniz',
  },
})

// Configure VeeValidate
configure({
  generateMessage: localize({
    tr: {
      ...tr,
      messages: {
        ...tr.messages,
        required: '{field} alanı zorunludur',
        email: 'Geçerli bir e-posta adresi giriniz',
        min: '{field} en az 0:{min} karakter olmalıdır',
        max: '{field} en fazla 0:{max} karakter olmalıdır',
        confirmed: '{field} alanı eşleşmiyor',
        numeric: '{field} sayısal bir değer olmalıdır',
        between: '{field} 0:{min} ile 0:{max} arasında olmalıdır',
        url: 'Geçerli bir URL giriniz',
        regex: '{field} formatı geçersiz',
        alpha: '{field} sadece harf içerebilir',
        alpha_num: '{field} sadece harf ve rakam içerebilir',
        alpha_spaces: '{field} sadece harf ve boşluk içerebilir',
        length: '{field} 0:{length} karakter uzunluğunda olmalıdır',
        min_value: '{field} en az 0:{min} olmalıdır',
        max_value: '{field} en fazla 0:{max} olmalıdır',
      },
      names: {
        email: 'E-posta',
        password: 'Şifre',
        password_confirmation: 'Şifre Tekrarı',
        name: 'Ad',
        first_name: 'Ad',
        last_name: 'Soyad',
        phone: 'Telefon',
        address: 'Adres',
        city: 'Şehir',
        country: 'Ülke',
        notes: 'Notlar',
        title: 'Başlık',
        description: 'Açıklama',
        price: 'Fiyat',
        quantity: 'Miktar',
        date: 'Tarih',
        time: 'Saat',
      },
    },
    en,
  }),
})

// Set default locale to Turkish
setLocale('tr')

export { setLocale, yup }
