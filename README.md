# LuxCraft Payroll for Perfex CRM

## Playwright PDF workflow

A production-oriented Node.js/Playwright PDF renderer is available in [`payslip-pdf/`](payslip-pdf/README.md). It provides the reusable A4 HTML template, runtime data mapper, JSON Schema, example payroll payload, CLI, and `generatePayslipPdf(data)` API while leaving the existing Perfex/TCPDF download path intact for backward compatibility.

## Installation

Perfex requires the module directory and its bootstrap PHP file to have the same name. This repository therefore keeps all installable files inside `luxcraft_payslips/`, with the entry point at `luxcraft_payslips/luxcraft_payslips.php`.

**Recommended:** download `luxcraft_payslips.zip` from the latest GitHub Release, then upload that ZIP in **Setup → Modules**. Do not upload GitHub's automatically generated “Source code” ZIP because it adds a repository-name wrapper directory that Perfex cannot detect as a module.

For a manual installation, copy the complete `luxcraft_payslips/` directory to `<perfex-root>/modules/luxcraft_payslips/`. The resulting entry point must be `<perfex-root>/modules/luxcraft_payslips/luxcraft_payslips.php`. Then open **Setup → Modules** and activate **LuxCraft Payroll**.

The release workflow packages the directory with the required layout whenever a `v*` tag is pushed, and also provides the ZIP as a workflow artifact when run manually.

---


## Key updates
- Repositioned module as Payroll, not only Payslips.
- Added Payroll tab hook for Staff profile where supported by Perfex.
- Payroll Profiles page remains as fallback and admin list view.
- Payment Details is now a single-line text field.
- Removed manual CPF Employee Rate / CPF Employer Rate from UI.
- Added Employment Type:
  - Singapore Citizen
  - SPR Year 1
  - SPR Year 2
  - SPR Year 3+
  - Work Permit
  - S Pass
  - Foreigner / No CPF
- CPF rates now derive from employment type.
- CPF rounding rules implemented:
  1. Total CPF Contribution rounds to nearest dollar: drop < $0.50, round up >= $0.50.
  2. Employee Share rounds down to nearest dollar.
  3. Employer Share = Total CPF Contribution - Employee Share.
- Commission Enabled reveals Commission %.
- Commission % appears in Payroll Profile table.
- Generate Payslip remains simple and pulls staff salary/payment/CPF details server-side from Payroll Profile.
- Paid payslips remain locked.

## Note
SPR Year 1 and Year 2 CPF rates are placeholders and should be adjusted to your company's exact CPF treatment. For full CPF compliance, add official CPF table rules for age bands, AW/OW ceilings, and future rate changes.


## Updates in v6
- Added Payroll Name field to Payroll Profile.
- Payslips display Payroll Name instead of CRM staff name, with CRM staff name as fallback.
- Staff Payroll tab also includes Payroll Name.
- Payroll Profile table now shows CRM Staff Name and Payroll Name separately.
- Sidebar icon spacing improved using menu icon CSS and label spacing.


## v7 Hotfix
- Fixes 500 errors after replacing/upgrading the module folder where Perfex activation hook does not re-run.
- The module now runs an idempotent schema check when the controller loads.
- Ensures these missing columns are created automatically:
  - `payroll_name`
  - `employment_type`
  - `commission_rate`
  - `cpf_total`
- This is the likely fix for 500 errors when submitting `/admin/luxcraft_payslips/create`.


## v8 Hotfix
- Converts Perfex date picker values with `to_sql_date()` before saving to MySQL DATE fields.
- This fixes common 500 errors caused by inserting `dd-mm-yyyy` / site-formatted dates directly into DATE columns.
- Adds safer defaults for commission, allowances, deductions and remarks.
- Adds database insert/update error logging to `application/logs/`.


## v9 Debug Fix
- Rewrites payslip model joins cleanly to avoid malformed duplicate joins.
- Adds explicit on-screen error output if payslip creation fails.
- Adds `/admin/luxcraft_payslips/debug` admin-only diagnostic endpoint.
- If generation still fails, open that debug URL and send the output.


## v10 Schema Fix
Your debug output showed the payslips table was from an older module schema:
- `gross_salary` instead of `base_salary`
- `net_pay` instead of `take_home_pay`
- `other_deductions` instead of `deductions`
- missing `job_title`
- missing current module columns

v10 adds missing columns safely and backfills old values where possible.
This should fix the generate payslip 500 error caused by DB schema mismatch.


## v11 Fixes
- Fixes Salary Month showing as `0000-00-00` for newly generated payslips by normalizing and storing salary month as `YYYY-MM`.
- Migrates `salary_month` to `CHAR(7)`.
- Adds display formatter so salary month shows as `June 2026` instead of raw database value.
- Rewrites PDF download method to support Perfex/TCPDF style PDF generation.
- Adds company logo above `LUXCRAFT PTE. LTD.` in digital payslip view and PDF template when company logo is configured in Perfex.


## v12 Privacy Fix
- Removes Take Home Pay from list/table views.
- Removes Estimated Take Home from Generate Payslip preview.
- Take Home Pay remains visible only inside the actual payslip view/PDF.
- Keeps v11 fixes:
  - salary_month normalized as YYYY-MM
  - salary month displayed as readable text
  - PDF download method patched for Perfex/TCPDF
  - company logo shown above LUXCRAFT PTE. LTD. when configured


## v13 Fixes
- Removes dependency on `$this->load->library('pdf')`, fixing `Unable to load the requested class: Pdf`.
- Uses Perfex native `app_pdf()` / TCPDF-style PDF generation.
- Employee can access `/admin/luxcraft_payslips/view/{id}` for own payslip.
- Employee view shows only view/download actions, no admin edit/paid controls.
- Admin can bulk mark selected payslips as paid from the payslip list.
- Bulk download now generates PDF files inside ZIP instead of HTML files.


## v14 Fix
- Fixes `app_pdf() exactly 2 expected` error by removing `app_pdf()` usage.
- Uses TCPDF directly from Perfex/CodeIgniter third-party library.
- Applies to single PDF download and bulk ZIP PDF generation.


## v15 Updates
- Generate Payslip form grouped into clean sections:
  - Payroll Details
  - Additional Payments / Deductions
  - Payroll Profile
  - Remarks
- Employee label now sits above the dropdown like other form fields.
- Added visible spacing between `Payroll Profile` heading and preview content.
- Payroll Profile preview is now a neat table.
- PDF payslip design rebuilt to closely match the `/luxcraft_payslips/view/` page:
  - logo above company name
  - clear sections
  - bordered tables
  - cleaner spacing
  - professional payment summary


## v16 Updates
- Rebuilt PDF payslip with a more premium, clean layout similar to the manually generated LuxCraft payslips.
- Reduced logo size significantly.
- Added header with compact logo, company details, PAYSLIP title and month.
- Added stronger visual hierarchy with section headers, neat bordered tables and dark take-home row.
- Screen payslip view also updated to better match the premium PDF design.

## v17 Invoice Style Refactor
- Refactored payslip preview and PDF to use one shared template: `views/templates/payslip_template.php`.
- PDF and `/luxcraft_payslips/view/` now visually match each other.
- Redesigned payslip in Perfex invoice style with company/logo header, PAYSLIP title/status, two-column info blocks, invoice-style salary breakdown, separate employer contributions, payment details and notes.
- Employer CPF is separated from employee payout for clearer payroll presentation.


## v18 Updates
- Payslip view/PDF header is now center-aligned.
- Logo width set to 200px.
- `LUXCRAFT PTE. LTD.` remains bold.
- `UEN: 202343751W` is now bold.
- Removed Draft/Paid status badge from payslip header.
- Employees only see payslips after they are marked as paid.
- Employee `My Payslips` list only shows paid payslips.


## v19 Updates
- Updated PDF template only.
- Does not affect `/admin/luxcraft_payslips/view/{id}` layout or code.
- PDF styled to match the supplied screenshot:
  - centered logo/header
  - 200px logo width
  - centered company/UEN/PAYSLIP/month
  - top divider
  - two-column employee/payroll information
  - clean salary breakdown table
  - dark net salary row
  - employer contributions and payment details sections


## v20 Updates
- PDF template only.
- Does not affect `/admin/luxcraft_payslips/view/{id}` page.
- Removed all PDF table lines/borders.
- Added more spacing between Salary Breakdown, Employer Contributions and Payment Details.
- Added more spacing above the computer-generated payslip disclaimer.


## v21 Updates
- PDF template only.
- Does not affect `/admin/luxcraft_payslips/view/{id}` page.
- Logo reduced to 150px width.
- PDF content after the divider now follows the requested plain text section format:
  - Employee Information
  - Payroll Information
  - Salary Breakdown
  - Employer CPF Contribution
  - Payment Details
- Removed table layout entirely from PDF.


## v22
- PDF only.
- Reduced spacing between company/UEN.
- Reduced spacing above Employee Information.
- Tightened all body text spacing so the payslip fits comfortably on one page.


## v24 Updates
- PDF only: reduced top space above logo as much as possible.
- PDF only: reduced logo size and tightened all text spacing below divider to better fit one A4 page.
- View page only: removed Payment Method row under Payroll Information.


## v25 Updates
- Added `Print Payslip` button on the payslip view page.
- Print mode prints only the payslip content, hiding Perfex sidebar/header/buttons/actions.
- Download PDF remains unchanged.


## v26 Updates
- Improved Print Payslip alignment.
- Print output is centered on A4.
- Payslip uses near end-to-end printable width with consistent A4 margins.
- Hides Perfex wrapper spacing during print.


## v27 Updates
- Print-only adjustments to help payslip fit onto one A4 page.
- Keeps the existing design.
- Removes the month text below PAYSLIP in the print/view header.
- Removes the header divider line during print.
- Tightens print spacing slightly without changing the visual structure.


## v28 Updates
- Removed external table borders around Employee Information and Payroll Information.
- Employee/Payroll section headers now use the same visual style/size as Salary Breakdown.
- Added clearer section spacing so areas are better separated while preserving print layout.


## v29 Updates
- Employee Information / Payroll Information data rows have table borders again.
- Employee Information and Payroll Information headers now match Salary Breakdown header font/size/weight more closely.
- Removed PDF download buttons from payslip view, payslip list, and employee My Payslips.
- Disabled single PDF download and bulk PDF download endpoints.
- Bulk mark as paid remains available.


## v30
- Employee Information and Payroll Information now use the exact same `<h4 class="bold">` markup as Salary Breakdown for identical appearance in print and screen.


## v31 Updates
- Fixed Employee Information / Payroll Information headers so they are no longer normal text size.
- Headers now use explicit font size/weight to visually match Salary Breakdown.
- Employee My Payslips table heading changed from `View / Download` to `View`.


## v32 Updates
- Removed Employee Information and Payroll Information from the `luxcraft-info-box` wrapper.
- These sections now use direct `<h4 class="bold">` headings and normal bordered tables.
- This removes the CSS conflict causing those headings to render differently from Salary Breakdown.


## v34
- Removed `class="bold"` from all five section headers.
- Removed custom CSS overrides for h4 headings so default styling is used.


## v35 Updates
- Center-aligned Print, Edit and Mark as Paid buttons on the payslip view page.
- Moved Mark Selected as Paid beside the DataTable export buttons where available.


## v36 Updates
- Fixed payslip view buttons with inline flex centering so Print/Edit/Mark as Paid reliably center-align.
- Restored Mark Selected as Paid button so it is always visible.
- Button is placed in the list action area and moved beside DataTable export buttons when available.


## v37 Updates
- Moved `Mark Selected as Paid` beside the `Generate Payslip` button.
- Removed JavaScript that moved it beside DataTable export buttons.
- Button remains linked to selected payslips via `form="bulk-payslip-form"`.


## v38 Updates
- Generate Payslip and Mark Selected as Paid buttons are placed in the same tight button group.
- Generate Payslip page section headers are made uniform.
- Removed outer borders around Generate Payslip form sections.


## v39 Updates
- Fixed Payroll Profile Commission Enabled toggle.
- Commission % field now appears immediately when Commission Enabled is checked.
- Commission % field hides and disables when Commission Enabled is unchecked.
- Fixed Generate Payslip Employee field layout so the Employee label sits above the Select Employee dropdown.


## v40 Updates
- Rebuilt Commission Enabled toggle with vanilla JavaScript so it does not depend on jQuery/load order.
- Commission % now reliably appears immediately when Commission Enabled is checked.
- Commission % hides and disables when unchecked.
- Strengthened Generate Payslip Employee field markup and CSS so the Employee label stays above the dropdown.


## v41 Updates
- Payroll Profiles Employee dropdown now uses the same label-above-dropdown layout as Generate Payslip.
- Commission Enabled checkbox and Commission % field are now aligned in a responsive row.
- Commission field stacks neatly on mobile/tablet.


## v42 Updates
- All Payslips header aligns left while action buttons align right.
- Added Mark as Unpaid for paid payslips in view page.
- Added bulk Mark Selected as Unpaid.
- Added individual Mark Unpaid shortcut in All Payslips table for paid payslips.


## v43 Updates
- Fixed 500 error on All Payslips by rewriting the list view safely.
- Edit Payslip button now says Save Payslip.
- Generate Payslip employee dropdown now only shows employees with Payroll Profiles.
- Added Bulk Generate Payslips function.
- Bulk Generate only lists employees with Payroll Profiles.
- Added clean bulk paid/unpaid buttons without malformed JavaScript.


## v44 Updates
- Generate Payslip employee dropdown only lists employees with Payroll Profiles.
- Bulk Generate only lists employees with Payroll Profiles.
- Payroll Profiles employee dropdown lists all active CRM staff.
- Confirmed/hardened payslip view actions: Edit / Mark as Paid / Mark as Unpaid are admin-only.


## v45 Updates
- Print-only fix for duplicated second page.
- Removed print position fixed, transform scale, and A4 min-height rules that can create duplicate pages.
- Keeps the existing visual payslip design unchanged.
- Print still hides Perfex sidebar/header/buttons and prints only the payslip.


## v46 Updates
- Added print pagination at bottom-right corner.
- Pagination displays as `1 / 2`, `2 / 2`, etc. in browsers/PDF printers that support CSS paged counters.
- Hidden on screen; only visible during print.


## v47 Updates
- Replaced CSS counter pagination with JavaScript-generated print page numbers.
- Page numbers are added just before printing, e.g. `1 / 2`, `2 / 2`.
- This is more reliable in Chrome/Edge because standard CSS `counter(page)` is not consistently supported inside normal page content.


## v48 Updates
- Added Perfex Roles permissions under `LuxCraft Payroll`.
- Admin can now configure what employees/directors/staff roles can see/do under Setup > Staff > Roles.
- Permissions added:
  - View Own Payslips
  - View All Payslips
  - Generate Payslips
  - Edit Payslips
  - Delete Payslips
  - Mark Paid / Unpaid
  - Bulk Generate Payslips
  - Manage Payroll Profiles
- View/Edit/Mark Paid/Profile pages now enforce permissions.
- Menu items and action buttons now show/hide based on permissions.


## v49 Updates
- Sidebar/menubar tabs are now strictly permission-based.
- Users will only see Payroll child tabs they have permission to access:
  - Payslips requires View All Payslips.
  - Generate Payslip requires Generate Payslips.
  - Bulk Generate requires Bulk Generate Payslips.
  - Payroll Profiles requires Manage Payroll Profiles.
  - My Payslips requires View Own Payslips.
- Payroll parent menu is hidden completely if the user has no Payroll permissions.


## v50 Updates
- Fixed All Payslips page error after v49.
- Rewrote `views/admin/list.php` cleanly to remove malformed/duplicated PHP permission wrappers.
- Permission-based buttons remain intact.
