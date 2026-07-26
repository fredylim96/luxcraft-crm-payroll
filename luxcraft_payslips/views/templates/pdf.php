<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$e = 'luxcraft_pdf_escape';
$money = function ($amount, $negative = false) use ($data) {
    $prefix = strtoupper($data['currency']) === 'SGD' ? 'S$' : strtoupper($data['currency']) . ' ';
    return ($negative ? '-' : '') . $prefix . number_format(abs((float)$amount), 2);
};
$date = function ($value) {
    if (!$value || $value === '0000-00-00') {
        return '-';
    }
    $timestamp = strtotime($value);
    return $timestamp ? date('d-m-Y', $timestamp) : $value;
};
?>
<style>
body{font-family:dejavusans,sans-serif;font-size:8.7pt;line-height:1.35;color:#111;background-color:#fff}
table{width:100%;border-collapse:collapse}td,th{vertical-align:top}.right{text-align:right}.top td{padding:8px}.brand td{vertical-align:middle}.logo-cell{width:16%;padding-right:8px}.logo{max-width:44px;max-height:44px}.logo-fallback{width:40px;height:40px;border:1px solid #111;text-align:center;font-family:dejavuserif,serif;font-size:22pt;font-weight:bold}.eyebrow,.label,.kicker{font-size:6.7pt;font-weight:bold;text-transform:uppercase;letter-spacing:1px}.company{font-family:dejavuserif,serif;font-size:17pt;font-weight:bold}.uen{font-size:7.6pt}.title{margin-top:14px;font-size:21pt;font-weight:bold}.summary{padding:14px;border:1px solid #dce3e3;background-color:#fff}.summary-grid td{width:50%;padding:8px 4px}.value{margin-top:3px;font-size:8.5pt}.divider{border-top:1px solid #dedede;height:12px}.section-row{margin-bottom:12px}.section-left{width:50%;padding-right:7px}.section-right{width:50%;padding-left:7px}.panel{padding:14px;border:1px solid #dedede;background-color:#fff}.panel-heading{font-size:12pt;font-weight:bold}.kicker{text-align:right;font-size:6.2pt}.detail-grid{margin-top:9px}.detail-grid td{width:50%;padding:5px 2px}.salary-left{width:57%;padding-right:7px}.salary-right{width:43%;padding-left:7px}.pay-table{margin-top:10px}.pay-table th{padding:6px 1px;border-bottom:1px solid #dedede;font-size:6.8pt;font-weight:normal;text-transform:uppercase;letter-spacing:.7px}.pay-table td{padding:8px 1px;border-bottom:1px solid #e5e5e5}.pay-table tr:last-child td{border-bottom:0}.net-panel{padding:14px;border:1.4px solid #0f6770;background-color:#fff}.net-box{margin-top:10px;padding:12px;border:1px solid #6f9ca0;background-color:#fff}.net-amount{font-family:dejavuserif,serif;font-size:18pt;font-weight:bold}.cpf-table{margin-top:9px}.cpf-table td{padding:6px 0}.remarks{margin-top:12px}.remarks-copy{margin-top:10px}.footer{margin-top:13px;font-size:7.5pt}
</style>

<table class="top"><tr>
<td width="58%">
  <table class="brand"><tr><td class="logo-cell"><?php if ($data['company_logo']) { ?><img class="logo" src="<?php echo $e($data['company_logo']); ?>"><?php } else { ?><div class="logo-fallback">L</div><?php } ?></td><td width="84%"><span class="eyebrow">Payslip Statement</span><br><span class="company"><?php echo $e(strtoupper($data['company_name'])); ?></span><br><?php if ($data['company_uen']) { ?><span class="uen">UEN: <?php echo $e($data['company_uen']); ?></span><?php } ?></td></tr></table>
  <div class="title"><?php echo $e($data['payroll_month']); ?> Payslip</div>
</td>
<td width="42%"><div class="summary"><span class="eyebrow">Payroll Summary</span><table class="summary-grid">
  <tr><td><span class="label">Salary Month</span><br><span class="value"><?php echo $e($data['payroll_month']); ?></span></td><td><span class="label">Currency</span><br><span class="value"><?php echo $e($data['currency']); ?></span></td></tr>
  <tr><td><span class="label">Payment Date</span><br><span class="value"><?php echo $e($date($data['pay_date'])); ?></span></td><td><span class="label">Payment Method</span><br><span class="value"><?php echo $e($data['payment_mode']); ?></span></td></tr>
</table></div></td>
</tr></table>
<div class="divider"></div>

<table class="section-row"><tr>
<td class="section-left"><div class="panel"><table><tr><td class="panel-heading">Employee Information</td><td class="kicker">Employee record</td></tr></table><table class="detail-grid"><tr><td><span class="label">Employee Name</span><br><span class="value"><?php echo $e($data['employee_name']); ?></span></td><td><span class="label">Job Title</span><br><span class="value"><?php echo $e($data['job_title']); ?></span></td></tr></table></div></td>
<td class="section-right"><div class="panel"><table><tr><td class="panel-heading">Payment Details</td><td class="kicker">Payment record</td></tr></table><table class="detail-grid"><tr><td><span class="label">Payment Method</span><br><span class="value"><?php echo $e($data['payment_mode']); ?></span></td><td><span class="label">Payment Details</span><br><span class="value"><?php echo $e($data['payment_details']); ?></span></td></tr></table></div></td>
</tr></table>

<table><tr>
<td class="salary-left"><div class="panel"><table><tr><td class="panel-heading">Salary Breakdown</td><td class="kicker">Month breakdown</td></tr></table><table class="pay-table">
<tr><th width="68%">Description</th><th width="32%" class="right">Amount (<?php echo $e($data['currency']); ?>)</th></tr>
<?php foreach ($data['earnings'] as $line) { ?><tr><td><strong><?php echo $e($line['description']); ?></strong></td><td class="right"><?php echo $e($money($line['amount'])); ?></td></tr><?php } ?>
<?php foreach ($data['deductions'] as $line) { ?><tr><td><strong><?php echo $e($line['description']); ?></strong></td><td class="right"><?php echo $e($money($line['amount'], true)); ?></td></tr><?php } ?>
</table></div></td>
<td class="salary-right"><div class="net-panel"><span class="eyebrow">Net Salary / Take Home Pay</span><div class="net-box"><span class="label">Amount Payable</span><br><span class="net-amount"><?php echo $e($money($data['net_pay'])); ?></span></div><table class="cpf-table">
<tr><td>Employer CPF Contribution</td><td class="right"><strong><?php echo $e($money($data['employer_cpf'])); ?></strong></td></tr>
<tr><td>Total CPF Contribution</td><td class="right"><strong><?php echo $e($money($data['total_cpf'])); ?></strong></td></tr>
</table></div></td>
</tr></table>

<?php if ($data['remarks']) { ?><div class="panel remarks"><table><tr><td class="panel-heading">Remarks</td><td class="kicker">Payroll note</td></tr></table><div class="remarks-copy"><?php echo nl2br($e($data['remarks'])); ?></div></div><?php } ?>
<div class="footer">This is a computer-generated payslip. No signature is required.</div>
