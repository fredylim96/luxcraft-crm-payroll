<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$e = 'luxcraft_pdf_escape';
$money = function ($amount) use ($data) {
    return $data['currency'] . ' ' . number_format((float)$amount, 2);
};
?>
<style>
body{font-family:dejavusans,sans-serif;font-size:8.5pt;line-height:1.35;color:#263238}
table{width:100%;border-collapse:collapse}td,th{vertical-align:top}.hero{background-color:#edf5f4;border:1px solid #d8e4e2}.hero td{padding:12px}.logo{height:42px}.eyebrow{font-size:7pt;color:#668083;text-transform:uppercase;letter-spacing:1px}.company{font-family:dejavuserif,serif;font-size:18pt;color:#123f44;font-weight:bold}.title{font-size:22pt;color:#123f44;font-weight:bold}.muted{color:#68777a}.right{text-align:right}.center{text-align:center}.section{margin-top:12px}.section-title{padding:7px 8px;background-color:#123f44;color:#fff;font-size:9pt;font-weight:bold;text-transform:uppercase;letter-spacing:.6px}.info{border:1px solid #d7dfdf}.info td{width:25%;padding:7px 8px;border-bottom:1px solid #e7ecec}.label{font-size:7pt;color:#78878a;text-transform:uppercase}.value{font-weight:bold;color:#263238}.lines th{padding:7px 8px;background-color:#f0f4f3;color:#526568;border-bottom:1px solid #d7dfdf;font-size:7pt;text-transform:uppercase}.lines td{padding:7px 8px;border-bottom:1px solid #e3e8e8}.total td{font-weight:bold;background-color:#f7f9f8}.net{margin-top:12px;background-color:#0f5b63;color:#fff;border:1px solid #0a454b}.net td{padding:11px}.net-label{font-size:8pt;text-transform:uppercase}.net-value{font-family:dejavuserif,serif;font-size:20pt;font-weight:bold}.summary td{width:25%;padding:8px;border:1px solid #d7dfdf}.footer{margin-top:15px;padding-top:8px;border-top:1px solid #cfd8d7;color:#6c797b;font-size:7pt}
</style>
<table class="hero"><tr>
<td width="58%">
<?php if ($data['company_logo']) { ?><img class="logo" src="<?php echo $e($data['company_logo']); ?>"><br><?php } ?>
<span class="eyebrow">Official Payroll Record</span><br>
<span class="company"><?php echo $e($data['company_name']); ?></span><br>
<span class="muted"><?php echo $e($data['company_address']); ?></span><br>
<?php if ($data['company_uen']) { ?><strong>UEN:</strong> <?php echo $e($data['company_uen']); ?><?php } ?>
</td>
<td width="42%" class="right"><span class="title">PAYSLIP</span><br><br>
<span class="label">Payroll Month</span><br><strong><?php echo $e($data['payroll_month']); ?></strong><br><br>
<span class="label">Payslip No.</span><br><strong><?php echo $e($data['payslip_no']); ?></strong>
</td></tr></table>

<div class="section-title">Employee &amp; Payroll Information</div>
<table class="info">
<tr><td><span class="label">Employee Name</span><br><span class="value"><?php echo $e($data['employee_name']); ?></span></td><td><span class="label">Employee ID</span><br><span class="value"><?php echo $e($data['employee_id']); ?></span></td><td><span class="label">Job Title</span><br><span class="value"><?php echo $e($data['job_title']); ?></span></td><td><span class="label">Department</span><br><span class="value"><?php echo $e($data['department']); ?></span></td></tr>
<tr><td><span class="label">Date Joined</span><br><span class="value"><?php echo $e($data['date_joined']); ?></span></td><td><span class="label">NRIC / FIN</span><br><span class="value"><?php echo $e($data['nric_fin']); ?></span></td><td><span class="label">Pay Date</span><br><span class="value"><?php echo $e($data['pay_date']); ?></span></td><td><span class="label">Payment / Status</span><br><span class="value"><?php echo $e($data['payment_mode']); ?> · <?php echo $e($data['status']); ?></span></td></tr>
</table>

<table class="section"><tr><td width="49%">
<div class="section-title">Earnings</div><table class="lines"><tr><th width="68%">Description</th><th width="32%" class="right">Amount</th></tr>
<?php foreach ($data['earnings'] as $line) { ?><tr><td><?php echo $e($line['description']); ?></td><td class="right"><?php echo $e($money($line['amount'])); ?></td></tr><?php } ?>
<tr class="total"><td>Gross Earnings</td><td class="right"><?php echo $e($money($data['gross_earnings'])); ?></td></tr></table>
</td><td width="2%"></td><td width="49%">
<div class="section-title">Deductions</div><table class="lines"><tr><th width="68%">Description</th><th width="32%" class="right">Amount</th></tr>
<?php if (!$data['deductions']) { ?><tr><td>Nil</td><td class="right"><?php echo $e($money(0)); ?></td></tr><?php } ?>
<?php foreach ($data['deductions'] as $line) { ?><tr><td><?php echo $e($line['description']); ?></td><td class="right"><?php echo $e($money($line['amount'])); ?></td></tr><?php } ?>
<tr class="total"><td>Total Deductions</td><td class="right"><?php echo $e($money($data['total_deductions'])); ?></td></tr></table>
</td></tr></table>

<table class="net"><tr><td width="55%"><span class="net-label">Net Pay / Amount Payable</span><br><span class="muted" style="color:#c9dddd">After employee deductions and CPF</span></td><td width="45%" class="right"><span class="net-value"><?php echo $e($money($data['net_pay'])); ?></span></td></tr></table>

<table class="summary"><tr><td><span class="label">Employee CPF</span><br><strong><?php echo $e($money($data['employee_cpf'])); ?></strong></td><td><span class="label">Employer CPF</span><br><strong><?php echo $e($money($data['employer_cpf'])); ?></strong></td><td><span class="label">YTD Gross</span><br><strong><?php echo $e($money($data['ytd_gross'])); ?></strong></td><td><span class="label">YTD CPF</span><br><strong><?php echo $e($money($data['ytd_cpf'])); ?></strong></td></tr></table>

<div class="section-title">Company &amp; Record Details</div><table class="info"><tr><td><span class="label">CPF Submission Ref.</span><br><span class="value"><?php echo $e($data['company_cpf_reference']); ?></span></td><td><span class="label">Company Bank</span><br><span class="value"><?php echo $e($data['company_bank']); ?></span></td><td><span class="label">Prepared By</span><br><span class="value"><?php echo $e($data['prepared_by']); ?></span></td><td><span class="label">Currency</span><br><span class="value"><?php echo $e($data['currency']); ?></span></td></tr></table>
<?php if ($data['remarks']) { ?><div class="footer"><strong>Remarks:</strong> <?php echo nl2br($e($data['remarks'])); ?></div><?php } ?>
<div class="footer center">This is a computer-generated payslip and forms part of the company's payroll records. No signature is required.</div>
