# LuxCraft Payroll for Perfex CRM

A production-oriented Perfex CRM module that stores payroll records and renders branded A4 payslips with Perfex's bundled TCPDF library.

## Recommended module architecture

```text
luxcraft_payslips/
├── luxcraft_payslips.php                  # module metadata, hooks, permissions, menu
├── controllers/Luxcraft_payslips.php      # CRUD, access control, PDF/ZIP downloads
├── models/Luxcraft_payslips_model.php     # payroll profile and payslip persistence
├── libraries/Luxcraft_payslip_pdf.php     # TCPDF document adapter
├── helpers/luxcraft_payslips_helper.php   # CPF, normalization and PDF data contract
├── install/install.php                    # activation + idempotent schema upgrades
├── language/english/luxcraft_payslips_lang.php
├── views/
│   ├── admin/                             # Perfex admin screens
│   ├── staff/                             # employee self-service list
│   └── templates/
│       ├── pdf.php                        # TCPDF-optimized HTML/CSS
│       ├── payslip_template.php           # browser/print presentation
│       └── payslip_card.php
├── assets/css/luxcraft_payslips.css
└── examples/sample_payload.php            # complete PDF contract example
```

Perfex requires the module directory and bootstrap filename to match. The installable entry point must therefore be `modules/luxcraft_payslips/luxcraft_payslips.php`.

## Why the HTML has a dedicated PDF view

TCPDF's `writeHTML()` supports a useful but limited subset of browser HTML/CSS. The browser design cannot be passed through unchanged when it relies on flex/grid layouts, pseudo-elements, shadows, rounded cards, CSS variables, or complex positioning. `views/templates/pdf.php` follows the supplied HTML's composition directly—brand/status and payroll-summary header, paired employee/company cards, combined earnings-and-deductions table, dark net-pay summary, and confidential footer—but expresses it with nested tables, solid fills, borders, padding, and inline-compatible selectors that TCPDF renders reliably.

The screen/print template remains separate so future browser styling does not accidentally break archived payroll PDFs. Do not paste a full browser document into the PDF view; use fragments supported by TCPDF and keep all values escaped.

## Data contract

`luxcraft_payslip_pdf_data()` maps a database row to the template contract and supports:

- company: `company_name`, `company_uen`, `company_address`, `company_cpf_reference`, `company_bank`, `prepared_by`
- employee: `employee_name`, `employee_id`, `job_title`, `department`, `date_joined`, `nric_fin`
- payroll: `payslip_no`, `pay_date`, `payroll_month`, `payment_mode`, `status`, `currency`
- totals: `gross_earnings`, `total_deductions`, `employer_cpf`, `employee_cpf`, `ytd_gross`, `ytd_cpf`, `net_pay`
- variable lines: `earnings[]` and `deductions[]`, each containing `description`, `amount`, and an optional `note`

New records can store variable lines as JSON in `earnings_json` and `deductions_json`. Existing module records are backward compatible: base salary, commission, allowances, other deductions, and CPF columns are automatically converted into lines. The example at `luxcraft_payslips/examples/sample_payload.php` documents every field.

For payroll recordkeeping, populate the snapshot columns on creation rather than resolving mutable employee/company details only at download time. Empty legacy snapshot values fall back to current Perfex company options and staff data.

## Installation

1. Back up the Perfex database and files.
2. Copy the complete `luxcraft_payslips/` directory to `<perfex-root>/modules/luxcraft_payslips/`, or ZIP that directory (with `luxcraft_payslips.php` directly inside it) and upload it at **Setup → Modules**.
3. Activate **LuxCraft Payroll**. The activation hook creates or upgrades the tables using the configured Perfex database prefix.
4. In **Setup → Staff → Roles**, grant only the needed `LuxCraft Payroll` capabilities. Payroll data is sensitive; normally employees receive only **View Own Payslips**.
5. Configure the company name, address, UEN/tax number, and logo in Perfex company settings. Create a **Payroll Profile** for each employee.
6. Generate a payslip, review it, mark it paid, and use **Download PDF**. Employees can see only their own paid records. Users with **View All Payslips** can create a ZIP containing actual PDF files.

When upgrading, replace the module folder while retaining its exact name. The controller also runs the idempotent installer to repair installations where Perfex did not re-run the activation hook.

## PDF flow and extension points

The download action loads the record, enforces own/all access, normalizes it using `luxcraft_payslip_pdf_data()`, and calls:

```php
$this->load->library('luxcraft_payslips/Luxcraft_payslip_pdf');
$this->luxcraft_payslip_pdf->render($data, 'D', luxcraft_payslip_filename($data));
```

Use destination `S` to receive PDF bytes (the bulk ZIP implementation does this), `I` for inline browser display, or `F` with a controlled server path for an archival workflow. The adapter creates A4 portrait documents with UTF-8/DejaVu fonts, fixed margins, metadata, and automatic page breaks.

For custom integrations, first persist a payslip through `Luxcraft_payslips_model`; do not accept calculated totals or arbitrary employee IDs from an untrusted request. Recalculate totals server-side and authorize the staff member before rendering.

## Test payload

`examples/sample_payload.php` is intentionally guarded like other Perfex PHP files. From inside Perfex, after loading the module helper and library, it can be rendered for a visual smoke test:

```php
$data = require module_dir_path('luxcraft_payslips', 'examples/sample_payload.php');
$this->load->library('luxcraft_payslips/Luxcraft_payslip_pdf');
$this->luxcraft_payslip_pdf->render($data, 'I', 'LuxCraft_sample_payslip.pdf');
```

Do not expose a sample/debug route in production.

## TCPDF limitations and deliberate adjustments

- CSS Grid/Flexbox is replaced with width-controlled tables.
- Web fonts are replaced with TCPDF's bundled DejaVu Sans/Serif fonts for Unicode stability.
- Shadows, gradients, filters, animations, and complex rounded containers are omitted.
- Remote images may be blocked by server/TCPDF configuration. A local Perfex company logo is preferred; verify it in staging.
- Very long earning/deduction lists can continue onto another A4 page. TCPDF automatic page breaks are enabled rather than shrinking text below a readable payroll-record size.
- CPF rates in this repository are illustrative application logic, not a substitute for current CPF contribution tables, wage ceilings, age bands, or legal/payroll review. Validate them before production use.

## Packaging

The GitHub workflow packages only `luxcraft_payslips/`. GitHub's automatically generated source archive includes an extra repository wrapper and should not be uploaded directly as a Perfex module.
