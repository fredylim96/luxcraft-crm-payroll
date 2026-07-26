import fs from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const moduleDirectory = path.dirname(fileURLToPath(import.meta.url));
export const defaultTemplatePath = path.resolve(moduleDirectory, '../templates/luxcraft-payslip-template.html');

const requiredTextFields = [
  'company_name', 'company_uen', 'company_address', 'employee_name', 'employee_id',
  'job_title', 'department', 'date_joined', 'nric_fin', 'payslip_no', 'pay_date',
  'payroll_month', 'payment_mode', 'status', 'currency', 'prepared_by'
];
const amountFields = [
  'gross_earnings', 'total_deductions', 'employer_cpf', 'employee_cpf', 'ytd_gross',
  'ytd_cpf', 'net_pay'
];

function assertText(value, field) {
  if (typeof value !== 'string' || value.trim() === '') {
    throw new TypeError(`${field} must be a non-empty string`);
  }
}

function toAmount(value, field) {
  if (value === '' || value === null || value === undefined || typeof value === 'boolean') {
    throw new TypeError(`${field} must be a finite number`);
  }
  const amount = Number(value);
  if (!Number.isFinite(amount)) throw new TypeError(`${field} must be a finite number`);
  return amount;
}

function normalizeItems(items, field) {
  if (!Array.isArray(items)) throw new TypeError(`${field} must be an array`);
  return items.map((item, index) => {
    if (!item || typeof item !== 'object') throw new TypeError(`${field}[${index}] must be an object`);
    assertText(item.description, `${field}[${index}].description`);
    return { description: item.description.trim(), amount: toAmount(item.amount, `${field}[${index}].amount`) };
  });
}

export function normalizePayslipData(input) {
  if (!input || typeof input !== 'object' || Array.isArray(input)) {
    throw new TypeError('Payslip data must be an object');
  }
  requiredTextFields.forEach((field) => assertText(input[field], field));
  const data = { ...input };
  data.earnings = normalizeItems(input.earnings, 'earnings');
  data.deductions = normalizeItems(input.deductions, 'deductions');
  amountFields.forEach((field) => { data[field] = toAmount(input[field], field); });
  data.company_name = input.company_name.trim();
  data.status_class = input.status.trim().toLowerCase().replace(/[^a-z0-9]+/g, '-');
  data.generated_at = input.generated_at || new Date().toISOString();
  data.logo_url = typeof input.logo_url === 'string' ? input.logo_url : '';
  return data;
}

function formatMoney(amount, options) {
  return new Intl.NumberFormat(options.locale, {
    style: 'currency', currency: options.currency, currencyDisplay: 'code',
    minimumFractionDigits: 2, maximumFractionDigits: 2
  }).format(amount).replace(/\u00a0/g, ' ');
}

function escapeHtml(value) {
  return String(value).replace(/[&<>'"]/g, (character) => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;'
  })[character]);
}

function fillTokens(source, values) {
  return source.replace(/{{([a-z_]+)}}/g, (token, key) => {
    if (!(key in values)) throw new Error(`Unknown template field: ${key}`);
    return escapeHtml(values[key]);
  });
}

export async function renderPayslipHtml(input, options = {}) {
  const data = normalizePayslipData(input);
  const templatePath = options.templatePath || defaultTemplatePath;
  const source = await fs.readFile(templatePath, 'utf8');
  const moneyOptions = { currency: data.currency.toUpperCase(), locale: options.locale || 'en-SG' };
  const values = { ...data, currency_upper: data.currency.toUpperCase() };
  amountFields.forEach((field) => { values[`${field}_formatted`] = formatMoney(data[field], moneyOptions); });
  values.earnings_rows = data.earnings.map((item) => `<tr><td>${escapeHtml(item.description)}</td><td class="amount">${escapeHtml(formatMoney(item.amount, moneyOptions))}</td></tr>`).join('');
  values.deductions_rows = data.deductions.map((item) => `<tr><td>${escapeHtml(item.description)}</td><td class="amount">−${escapeHtml(formatMoney(Math.abs(item.amount), moneyOptions))}</td></tr>`).join('');
  values.logo_markup = data.logo_url
    ? `<img class="logo" src="${escapeHtml(data.logo_url)}" alt="${escapeHtml(data.company_name)} logo">`
    : '<div class="logo logo-mark" aria-hidden="true">L</div>';
  let html = source.replace('{{{logo_markup}}}', values.logo_markup)
    .replace('{{{earnings_rows}}}', values.earnings_rows)
    .replace('{{{deductions_rows}}}', values.deductions_rows);
  html = fillTokens(html, values);
  if (/{{{?[^}]+}}}?/.test(html)) throw new Error('Unresolved placeholder in payslip template');
  return html;
}
