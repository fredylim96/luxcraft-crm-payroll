import fs from 'node:fs/promises';
import path from 'node:path';
import { chromium } from 'playwright';
import { renderPayslipHtml } from './render-payslip.js';

export function payslipFilename(data) {
  const safe = (value) => String(value).trim().replace(/[^a-z0-9_-]+/gi, '-').replace(/^-|-$/g, '');
  return `payslip-${safe(data.payroll_month)}-${safe(data.employee_id)}-${safe(data.payslip_no)}.pdf`;
}

export async function generatePayslipPdf(data, options = {}) {
  const html = await renderPayslipHtml(data, options);
  const outputPath = path.resolve(options.outputPath || payslipFilename(data));
  await fs.mkdir(path.dirname(outputPath), { recursive: true });

  const browser = await chromium.launch({
    headless: true,
    executablePath: options.executablePath,
    args: options.browserArgs || ['--disable-dev-shm-usage', '--no-sandbox']
  });
  try {
    const page = await browser.newPage({ viewport: { width: 794, height: 1123 }, deviceScaleFactor: 1 });
    await page.setContent(html, { waitUntil: options.waitUntil || 'networkidle', timeout: options.timeout || 30_000 });
    await page.emulateMedia({ media: 'print' });
    await page.evaluate(async () => { if (document.fonts?.ready) await document.fonts.ready; });
    await page.pdf({
      path: outputPath,
      format: 'A4',
      printBackground: true,
      preferCSSPageSize: true,
      margin: { top: '0', right: '0', bottom: '0', left: '0' },
      tagged: true
    });
  } finally {
    await browser.close();
  }
  return outputPath;
}
