# LuxCraft Playwright payslip PDF service

This directory is an isolated Node.js renderer that can be called from the Perfex CRM module, a queue worker, or an internal payroll API. The HTML remains the visual source of truth; CRM values are validated, HTML-escaped, formatted, and injected into explicit placeholders before Chromium prints the page.

## Structure

```text
payslip-pdf/
├── templates/luxcraft-payslip-template.html  # A4 design and explicit fields
├── src/render-payslip.js                     # validation, mapping, formatting
├── src/generate-payslip-pdf.js               # reusable Playwright API
├── src/cli.js                                # JSON-to-PDF command
├── schema/payslip.schema.json                # CRM data contract
├── examples/payslip.example.json             # representative payroll input
└── test/render-payslip.test.js                # deterministic renderer tests
```

## Install and run

Node.js 20 LTS or later is recommended.

```bash
cd payslip-pdf
npm install
npx playwright install --with-deps chromium
npm run generate -- examples/payslip.example.json
```

The default filename is `output/payslip-July-2026-LC-0042-LC-PS-2026-07-0042.pdf`. Pass a second argument to override it:

```bash
npm run generate -- examples/payslip.example.json /secure/payroll/2026-07/LC-0042.pdf
```

For application code:

```js
import { generatePayslipPdf } from './payslip-pdf/src/index.js';

const file = await generatePayslipPdf(payrollData, {
  outputPath: `/secure/payroll/${payrollData.payslip_no}.pdf`
});
```

`generatePayslipPdf(data, options)` resolves to the absolute output path. Optional settings include `templatePath`, `locale`, `executablePath`, `browserArgs`, `timeout`, and `waitUntil`. In a busy CRM, put jobs on a queue and reuse a long-running browser pool rather than launching Chromium inside the PHP request. The standalone function deliberately owns and closes its browser to remain safe for scripts and low-volume jobs.

## Data contract

The canonical machine-readable contract is [`schema/payslip.schema.json`](schema/payslip.schema.json). Amounts are JSON numbers and line items have `{ "description": string, "amount": number }`. Deductions are supplied as positive values and rendered with a minus sign. All money is formatted with the payload's ISO 4217 `currency` and exactly two decimal places. Totals are accepted from the payroll system of record rather than recalculated, so statutory rounding remains under the CRM payroll engine's control.

Dates and `payroll_month` are display strings. Format them in the CRM before sending the payload. Mask `nric_fin` before transmission unless full identifiers are explicitly required and protected by policy. The renderer rejects absent text, non-array line items, and non-finite amounts before Chromium starts.

## Production deployment

- Commit the lockfile produced by your approved package registry, and pin the Playwright image/browser version together. A container based on the official Playwright image provides consistent Chromium and system libraries.
- Store PDFs outside the public web root, encrypt at rest, authorize every download, log access, and apply payroll retention/deletion rules. Never log the JSON payload.
- Treat payloads as untrusted. The mapper HTML-escapes every CRM text and line item; only trusted application configuration should choose `templatePath`, `executablePath`, or browser arguments.
- The Google Fonts link preserves the current Inter/Playfair identity and generation waits for fonts. For closed networks and deterministic records, download licensed WOFF2 files into a private asset directory, replace the `<link>` tags with `@font-face`, and use absolute `file://` URLs or inline data URLs. Keep Arial/Georgia fallbacks.
- Remote logos can add network variability. Prefer an HTTPS URL on a controlled host or a small trusted `data:image/...;base64,...` value. The built-in “L” mark is used when `logo_url` is omitted.
- The template uses `@page { size: A4 portrait; margin: 0 }`, print color adjustment, fixed millimetre dimensions, tabular numerals, break avoidance, and `printBackground: true`. These are intentional PDF-stability rules.
- Long employee/company fields and unusually many line items can exceed one page. Enforce CRM field limits and cap/aggregate line items for a guaranteed one-page document; add a regression payload for the maximum supported content.
- Validate incoming requests against the JSON Schema at the service boundary. The renderer also performs runtime validation as defense in depth.

## CRM integration boundary

The existing PHP module can POST the schema-shaped payload to an internal Node service or enqueue it for a Node worker. Return only a document ID/status to PHP, then stream the authorized PDF from protected storage. This avoids installing Chromium in the web-facing PHP container and makes retries, timeouts, audit trails, and resource limits straightforward.
