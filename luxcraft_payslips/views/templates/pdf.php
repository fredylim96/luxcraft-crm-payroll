<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$e = 'luxcraft_pdf_escape';
$money = function ($amount, $withCurrency = false) use ($data) {
    $formatted = number_format(abs((float) $amount), 2);
    return $withCurrency ? $data['currency'] . ' ' . $formatted : $formatted;
};
$date = function ($value) {
    if (!$value || $value === '0000-00-00') {
        return '-';
    }
    $timestamp = strtotime($value);
    return $timestamp ? date('d M Y', $timestamp) : $value;
};
$isPaid = strtolower($data['status']) === 'paid' || strtolower($data['status']) === 'released';
$statusLabel = $isPaid ? 'Salary credited' : 'Pending salary payment';
?>
<style>
body{font-family:dejavusans,sans-serif;font-size:8.2pt;line-height:1.35;color:#1f2328;background-color:#fcfbf8}
table{width:100%;border-collapse:collapse}td,th{vertical-align:top}.sheet{border:1px solid #dedbd5;background-color:#fcfbf8}.header{background-color:#edf4f3;border-bottom:1px solid #d7ddda}.header-cell{padding:18px}.brand-table td{vertical-align:middle}.logo-cell{width:17%;padding-right:10px}.logo-box{width:44px;height:44px;background-color:#0f5b63;color:#fff;text-align:center;font-family:dejavuserif,serif;font-size:22pt;font-weight:bold;line-height:44px}.logo-image{max-width:52px;max-height:52px}.eyebrow,.meta-label{font-size:6.7pt;color:#8c919a;text-transform:uppercase;letter-spacing:1px}.brand-name{font-family:dejavuserif,serif;font-size:17pt;font-weight:bold;color:#1f2328}.pill{display:inline-block;margin-top:10px;padding:5px 9px;background-color:#d9e9ea;color:#0f5b63;font-size:7pt;font-weight:bold}.pill-pending{background-color:#f4e8d4;color:#795929}.headline{margin-top:11px;font-size:17pt;font-weight:bold;line-height:1.12;color:#1f2328}.intro{margin-top:5px;color:#5d6470;font-size:7.5pt}.period-box{border:1px solid #d8e3e1;background-color:#fff;padding:12px}.period-title{font-size:9.5pt;font-weight:bold;color:#0f5b63}.period-grid td{width:50%;padding:6px 4px}.meta-value{font-size:7.7pt;font-weight:bold}.content-cell{padding:15px 18px 17px}.gap-bottom{margin-bottom:12px}.card{border:1px solid #dedbd5;background-color:#fff;padding:11px}.card-title{font-size:10.5pt;font-weight:bold}.card-kicker{text-align:right;font-size:6.5pt;color:#8c919a;text-transform:uppercase;letter-spacing:.7px}.details td{width:50%;padding:5px 3px}.detail-value{font-size:7.6pt;font-weight:bold}.salary-card{border:1px solid #dedbd5;background-color:#fff;padding:12px}.lines th{padding:6px 3px;border-bottom:1px solid #d8d6d1;color:#8c919a;font-size:6.7pt;text-transform:uppercase}.lines td{padding:6px 3px;border-bottom:1px solid #ebe9e5}.line-name{font-weight:bold}.line-note{font-size:6.6pt;color:#5d6470}.amount{text-align:right}.deduction{color:#8b2f4a}.summary-panel{background-color:#0f5b63;color:#f4f6f7;padding:13px}.summary-panel .meta-label{color:#b9ccce}.net-pay{margin-top:9px;padding:10px;border:1px solid #4b8389;background-color:#1f6970}.net-amount{font-family:dejavuserif,serif;font-size:17pt;font-weight:bold;color:#fff}.summary-list td{padding:6px 0;border-bottom:1px solid #477a7f;color:#fff}.footer-table{margin-top:13px}.note{color:#5d6470;font-size:7pt}.seal{text-align:right;color:#0f5b63;font-size:6.7pt;font-weight:bold;text-transform:uppercase;letter-spacing:.8px}.right{text-align:right}
</style>
<table class="sheet"><tr><td>
<table class="header"><tr>
<td width="58%" class="header-cell">
<table class="brand-table"><tr>
<td class="logo-cell"><?php if ($data['company_logo']) { ?><img class="logo-image" src="<?php echo $e($data['company_logo']); ?>"><?php } else { ?><div class="logo-box">L</div><?php } ?></td>
<td width="83%"><span class="eyebrow">Payslip Statement</span><br><span class="brand-name"><?php echo $e($data['company_name']); ?></span></td>
</tr></table>
<div class="pill <?php echo $isPaid ? '' : 'pill-pending'; ?>">&#9679;&nbsp; <?php echo $e($statusLabel); ?></div>
<div class="headline"><?php echo $e($data['payroll_month']); ?> payslip</div>
<div class="intro">A formal record of monthly earnings, statutory deductions and the net salary payable.</div>
</td>
<td width="42%" class="header-cell">
<div class="period-box"><div class="eyebrow">Payroll Summary</div><table class="period-grid">
<tr><td><span class="meta-label">Payslip no.</span><br><span class="meta-value"><?php echo $e($data['payslip_no']); ?></span></td><td><span class="meta-label">Pay date</span><br><span class="meta-value"><?php echo $e($date($data['pay_date'])); ?></span></td></tr>
<tr><td><span class="meta-label">Payroll month</span><br><span class="meta-value"><?php echo $e($data['payroll_month']); ?></span></td><td><span class="meta-label">Currency</span><br><span class="meta-value"><?php echo $e($data['currency']); ?></span></td></tr>
<tr><td><span class="meta-label">Payment mode</span><br><span class="meta-value"><?php echo $e($data['payment_mode']); ?></span></td><td><span class="meta-label">Status</span><br><span class="meta-value"><?php echo $e($data['status']); ?></span></td></tr>
</table></div>
</td></tr></table>

<table><tr><td class="content-cell">
<table class="gap-bottom"><tr>
<td width="55%"><div class="card"><table><tr><td class="card-title">Employee details</td><td class="card-kicker">Primary record</td></tr></table><table class="details">
<tr><td><span class="meta-label">Employee name</span><br><span class="detail-value"><?php echo $e($data['employee_name']); ?></span></td><td><span class="meta-label">Employee ID</span><br><span class="detail-value"><?php echo $e($data['employee_id']); ?></span></td></tr>
<tr><td><span class="meta-label">Job title</span><br><span class="detail-value"><?php echo $e($data['job_title']); ?></span></td><td><span class="meta-label">Department</span><br><span class="detail-value"><?php echo $e($data['department']); ?></span></td></tr>
<tr><td><span class="meta-label">Date joined</span><br><span class="detail-value"><?php echo $e($date($data['date_joined'])); ?></span></td><td><span class="meta-label">NRIC / FIN</span><br><span class="detail-value"><?php echo $e($data['nric_fin']); ?></span></td></tr>
</table></div></td><td width="2%"></td>
<td width="43%"><div class="card"><table><tr><td class="card-title">Company details</td><td class="card-kicker">Issuer</td></tr></table><table class="details">
<tr><td><span class="meta-label">Entity</span><br><span class="detail-value"><?php echo $e($data['company_name']); ?></span></td><td><span class="meta-label">UEN</span><br><span class="detail-value"><?php echo $e($data['company_uen']); ?></span></td></tr>
<tr><td colspan="2"><span class="meta-label">Address</span><br><span class="detail-value"><?php echo $e($data['company_address']); ?></span></td></tr>
<tr><td><span class="meta-label">CPF reference</span><br><span class="detail-value"><?php echo $e($data['company_cpf_reference']); ?></span></td><td><span class="meta-label">Bank</span><br><span class="detail-value"><?php echo $e($data['company_bank']); ?></span></td></tr>
<tr><td colspan="2"><span class="meta-label">Prepared by</span><br><span class="detail-value"><?php echo $e($data['prepared_by']); ?></span></td></tr>
</table></div></td></tr></table>

<table><tr><td width="61%"><div class="salary-card"><table><tr><td class="card-title">Earnings and deductions</td><td class="card-kicker">Month breakdown</td></tr></table><table class="lines">
<tr><th width="70%">Description</th><th width="30%" class="amount">Amount (<?php echo $e($data['currency']); ?>)</th></tr>
<?php foreach ($data['earnings'] as $line) { ?><tr><td><span class="line-name"><?php echo $e($line['description']); ?></span><?php if (!empty($line['note'])) { ?><br><span class="line-note"><?php echo $e($line['note']); ?></span><?php } ?></td><td class="amount"><?php echo $e($money($line['amount'])); ?></td></tr><?php } ?>
<?php foreach ($data['deductions'] as $line) { ?><tr><td><span class="line-name"><?php echo $e($line['description']); ?></span><?php if (!empty($line['note'])) { ?><br><span class="line-note"><?php echo $e($line['note']); ?></span><?php } ?></td><td class="amount deduction"><?php echo $e('(' . $money($line['amount']) . ')'); ?></td></tr><?php } ?>
<?php if (!$data['deductions']) { ?><tr><td><span class="line-name">No deductions</span></td><td class="amount">0.00</td></tr><?php } ?>
</table></div></td><td width="2%"></td>
<td width="37%"><div class="summary-panel"><span class="meta-label">Net salary payable</span><div class="net-pay"><span class="meta-label">Amount credited</span><br><span class="net-amount"><?php echo $e($money($data['net_pay'], true)); ?></span></div><table class="summary-list">
<tr><td>Gross earnings</td><td class="right"><strong><?php echo $e($money($data['gross_earnings'])); ?></strong></td></tr>
<tr><td>Total deductions</td><td class="right"><strong><?php echo $e($money($data['total_deductions'])); ?></strong></td></tr>
<tr><td>Employer CPF</td><td class="right"><strong><?php echo $e($money($data['employer_cpf'])); ?></strong></td></tr>
<tr><td>Year-to-date gross</td><td class="right"><strong><?php echo $e($money($data['ytd_gross'])); ?></strong></td></tr>
<tr><td>Year-to-date CPF</td><td class="right"><strong><?php echo $e($money($data['ytd_cpf'])); ?></strong></td></tr>
</table></div></td></tr></table>

<?php if ($data['remarks']) { ?><div class="note" style="margin-top:10px"><strong>Remarks:</strong> <?php echo nl2br($e($data['remarks'])); ?></div><?php } ?>
<table class="footer-table"><tr><td width="72%" class="note">This document is computer-generated and does not require a physical signature. It is an official and confidential payroll record.</td><td width="28%" class="seal">Confidential payroll record</td></tr></table>
</td></tr></table>
</td></tr></table>
