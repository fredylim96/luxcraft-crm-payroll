<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$logo_url = luxcraft_company_logo_url();
$employee_name = !empty($payslip['payroll_name']) ? $payslip['payroll_name'] : $payslip['firstname'].' '.$payslip['lastname'];
?>
<html>
<head>
<style>
body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 9.4px;
    color: #111827;
    line-height: 1.12;
    background: #ffffff;
}

.page {
    width: 100%;
}

.header {
    text-align: center;
    margin-top: -16px;
    margin-bottom: 6px;
}

.logo {
    width: 130px;
    max-width: 130px;
    margin: 0 auto 0 auto;
}

.company {
    font-size: 12px;
    font-weight: bold;
    color: #111827;
    margin-top: 0;
}

.uen {
    font-size: 8px;
    font-weight: bold;
    color: #374151;
    margin-top: -4px;
}

.title {
    font-size: 15px;
    font-weight: bold;
    color: #111827;
    letter-spacing: 0.4px;
    margin-top: 2px;
}

.month {
    font-size: 8px;
    color: #6b7280;
    margin-top: 0;
}

.top-rule {
    border-top: 3px solid #111827;
    height: 1px;
    margin: 8px 0 5px 0;
}

.section {
    margin-bottom: 6px;
}

.section-title {
    font-size: 9.4px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.2px;
    margin-bottom: 1px;
}

.line {
    margin-bottom: 1px;
}

.label {
    font-weight: normal;
}

.amount {
    white-space: nowrap;
}

.footer {
    margin-top: 18px;
    text-align: center;
    color: #6b7280;
    font-size: 8px;
    font-style: italic;
}

/* v24 PDF-only compact spacing */
.header {
    margin-top: -18px !important;
    margin-bottom: 5px !important;
}
.logo {
    width: 130px !important;
    max-width: 130px !important;
    margin-top: 0 !important;
    margin-bottom: 0 !important;
}
.company {
    margin-top: -2px !important;
    margin-bottom: 0 !important;
}
.uen {
    margin-top: -5px !important;
    margin-bottom: 0 !important;
}
.title {
    margin-top: 2px !important;
}
.top-rule {
    margin: 7px 0 5px 0 !important;
}
.section {
    margin-bottom: 6px !important;
}
.section-title {
    margin-top: 0 !important;
    margin-bottom: 1px !important;
    font-size: 9.2px !important;
}
.line {
    margin-bottom: 0 !important;
    line-height: 1.05 !important;
}
.footer {
    margin-top: 16px !important;
}

</style>
</head>
<body>
<div class="page">

    <div class="header">
        <?php if($logo_url){ ?>
            <img src="<?php echo $logo_url; ?>" class="logo">
        <?php } ?>
        <div class="company">LUXCRAFT PTE. LTD.</div>
        <div class="uen">UEN: 202343751W</div>
        <div class="title"><?php echo strtoupper(luxcraft_format_salary_month($payslip['salary_month'])); ?> PAYSLIP</div>
    </div>

    <div class="top-rule"></div>

    <div class="section">
        <div class="section-title">EMPLOYEE INFORMATION</div>
        <div class="line">Employee Name: <?php echo $employee_name; ?></div>
        <div class="line">Job Title: <?php echo $payslip['job_title']; ?></div>
    </div>

    <div class="section">
        <div class="section-title">PAYROLL INFORMATION</div>
        <div class="line">Salary Month: <?php echo luxcraft_format_salary_month($payslip['salary_month']); ?></div>
        <div class="line">Payment Date: <?php echo _d($payslip['payment_date']); ?></div>
        
    </div>

    <div class="section">
        <div class="section-title">SALARY BREAKDOWN</div>
        <div class="line">Base Salary: <?php echo luxcraft_pdf_money($payslip['base_salary']); ?></div>
        <?php if((float)$payslip['commission'] != 0){ ?>
            <div class="line">Commission: <?php echo luxcraft_pdf_money($payslip['commission']); ?></div>
        <?php } ?>
        <?php if((float)$payslip['allowances'] != 0){ ?>
            <div class="line">Allowances: <?php echo luxcraft_pdf_money($payslip['allowances']); ?></div>
        <?php } ?>
        <?php if((float)$payslip['deductions'] != 0){ ?>
            <div class="line">Other Deductions: -<?php echo luxcraft_pdf_money($payslip['deductions']); ?></div>
        <?php } ?>
        <div class="line">Employee CPF Contribution: -<?php echo luxcraft_pdf_money($payslip['cpf_employee']); ?></div>
        <div class="line"><strong>Net Salary / Take Home Pay: <?php echo luxcraft_pdf_money($payslip['take_home_pay']); ?></strong></div>
    </div>

    <div class="section">
        <div class="section-title">EMPLOYER CPF CONTRIBUTION</div>
        <div class="line">Employer CPF Contribution: <?php echo luxcraft_pdf_money($payslip['cpf_employer']); ?></div>
        <div class="line">Total CPF Contribution: <?php echo luxcraft_pdf_money($payslip['cpf_total']); ?></div>
    </div>

    <div class="section">
        <div class="section-title">PAYMENT DETAILS:</div>
        <div class="line">Payment Method: <?php echo $payslip['payment_method']; ?></div>
        <div class="line">Payment Details: <?php echo $payslip['payment_details']; ?></div>
    </div>

    <?php if($payslip['remarks']){ ?>
    <div class="section">
        <div class="section-title">REMARKS</div>
        <div class="line"><?php echo nl2br($payslip['remarks']); ?></div>
    </div>
    <?php } ?>

    <div class="footer">This is a computer-generated payslip. No signature is required.</div>

</div>
</body>
</html>
