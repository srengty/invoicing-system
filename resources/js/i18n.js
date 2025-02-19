import { createI18n } from 'vue-i18n';

const messages = {
  en: {
    quotation_no: 'Quotation No:',
    date: 'Date:',
    address: 'Address',
    contact: 'Contact',
    customer: 'Customer/Organization',
    item: 'Item',
    language: 'Khmer/English',
    no: 'No.',
    name: 'Name',
    unit: 'Unit',
    unit_price: 'Unit Price',
    qty: 'Qty',
    subtotal: 'Subtotal',
    actions: 'Actions',
    remove: 'Remove',
    total_khr: 'Total KHR',
    total_usd: 'Total USD',
    exchange_rate: 'Exchange Rate',
    submit: 'Submit',
    cancel: 'Cancel',
    addCustomer: 'AddCustomer',
      sed:'sed',
  },
  kh: {
    quotation_no: 'លេខរៀងអត្ថបទ:',
    date: 'កាលបរិច្ឆេទ:',
    address: 'អាសយដ្ឋាន',
    contact: 'ទំនាក់ទំនង',
    customer: 'អតិថិជន/ស្ថាប័ន',
    item: 'ទំនិញ',
    language: 'ភាសាខ្មែរ/អង់គ្លេស',
    no: 'លរ.',
    name: 'ឈ្មោះ',
    unit: 'ខ្នាត',
    unit_price: 'តម្លែរាយ',
    qty: 'ចំនួន',
    subtotal: 'សរុប',
    actions: 'សកម្មភាព',
    remove: 'លុប',
    total_khr: 'សរុប KHR',
    total_usd: 'សរុប USD',
    exchange_rate: 'អត្រាប្តូរប្រាក់',
    submit: 'ដាក់ស្នើ',
    cancel: 'បោះបង់',
    addCustomer: 'បន្ថែមអតិថិជន',
        sed:'លុយ',
  }
};

const i18n = createI18n({
  locale: 'en', // Default language
  fallbackLocale: 'en',
  messages
});

export default i18n;
