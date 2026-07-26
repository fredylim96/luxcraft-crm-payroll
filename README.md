# LuxCraft Payroll for Perfex CRM

A custom Perfex CRM payroll module for maintaining employee payroll profiles, generating payslip records, viewing them in the CRM, and printing the approved A4 payslip layout from the browser.

## Module architecture

```text
luxcraft_payslips/
├── luxcraft_payslips.php                  # module metadata, hooks, permissions, menu
├── controllers/Luxcraft_payslips.php      # payroll CRUD and access control
├── models/Luxcraft_payslips_model.php     # profile and payslip persistence
├── helpers/luxcraft_payslips_helper.php   # CPF and display helpers
├── install/install.php                    # activation and schema upgrades
├── language/english/luxcraft_payslips_lang.php
├── views/
│   ├── admin/                             # administration screens and print action
│   ├── staff/                             # employee self-service payslips
│   └── templates/payslip_template.php     # shared browser/print payslip
└── assets/css/luxcraft_payslips.css       # CRM and A4 print styling
```

## Installation

1. Back up the Perfex database and application files.
2. Copy `luxcraft_payslips/` to `<perfex-root>/modules/luxcraft_payslips/`. The directory and bootstrap file names must remain identical.
3. Open **Setup → Modules** and activate **LuxCraft Payroll**.
4. Grant the required capabilities under **Setup → Staff → Roles**. Normally employees should receive only **View Own Payslips**.
5. Configure the company favicon and create a Payroll Profile for each employee.
6. Generate and review a payslip. Mark it paid when finalized.

## Viewing and printing

Open a payslip from **Payroll → Payslips** and select **Print Payslip**. Browser print mode hides the Perfex navigation, buttons, and other application chrome, then formats only the payslip content as an A4 portrait document.

The module intentionally does not expose a PDF download endpoint, PDF button, TCPDF renderer, or bulk PDF/ZIP export. Printing is the only document-output workflow. If a PDF file is required operationally, use the browser print dialog's **Save as PDF** option so the saved document is identical to the approved print outcome.

Employees can view only their own paid payslips unless their role has broader payroll permissions. Paid records remain locked until an authorized payroll user marks them unpaid.

## Print-template mapping

The printable structure is maintained in `views/templates/payslip_template.php`, with A4 and print-isolation rules in `views/admin/view.php` and the shared visual styling in `assets/css/luxcraft_payslips.css`. Update those files together when changing the approved layout; there is no separate PDF template that can drift from the print result.

## Packaging

The GitHub workflow packages only the `luxcraft_payslips/` directory. Do not upload GitHub's automatically generated source archive directly to Perfex because it contains an additional repository wrapper directory.
