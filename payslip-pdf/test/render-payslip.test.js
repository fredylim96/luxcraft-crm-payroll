import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
import test from 'node:test';
import { renderPayslipHtml, normalizePayslipData } from '../src/render-payslip.js';

const example = JSON.parse(await fs.readFile(new URL('../examples/payslip.example.json', import.meta.url)));

test('renders escaped CRM data and fixed-decimal payroll amounts', async () => {
  const html = await renderPayslipHtml({ ...example, employee_name: 'Amelia <Tan>' });
  assert.match(html, /Amelia &lt;Tan&gt;/);
  assert.match(html, /SGD 7,255\.50/);
  assert.match(html, /SGD 5,684\.50/);
  assert.doesNotMatch(html, /{{[^}]+}}/);
});

test('rejects invalid monetary data before browser rendering', () => {
  assert.throws(() => normalizePayslipData({ ...example, net_pay: 'not-a-number' }), /net_pay/);
});
