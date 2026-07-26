<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$logo_url = luxcraft_company_logo_url();
$employee_name = !empty($payslip['payroll_name']) ? $payslip['payroll_name'] : $payslip['firstname'].' '.$payslip['lastname'];
$is_paid = isset($payslip['status']) && $payslip['status'] === 'paid';
$status_label = $is_paid ? 'Salary Credited' : 'Pending Salary Payment';
?>
<html><head><style>
body{font-family:dejavusans,sans-serif;font-size:9px;line-height:1.35;color:#1f2328;background:#fff}
.page{border:1px solid #d9d7d2}.hero{background-color:#edf4f3;border-bottom:1px solid #d5dedc;padding:20px}.hero-table,.columns,.salary-layout,.details,.summary-table{width:100%;border-collapse:collapse}.hero-table td,.columns td,.salary-layout td,.details td,.summary-table td{border:0;vertical-align:top}.brand{width:57%;padding-right:18px}.period{width:43%;padding:14px;background-color:#fff;border:1px solid #d5e1df}.logo{width:115px;max-height:45px;margin-bottom:5px}.eyebrow,.label{color:#7a8189;font-size:7px;font-weight:bold;text-transform:uppercase;letter-spacing:.7px}.company{font-family:dejavuserif,serif;font-size:17px;font-weight:bold}.uen{color:#5d6470;font-size:8px}.status{display:inline-block;margin-top:9px;padding:5px 9px;font-size:8px;font-weight:bold}.status-paid{background-color:#dcefe2;color:#24643a}.status-pending{background-color:#fff0bd;color:#775900}.title{font-size:23px;font-weight:bold;line-height:1.1;margin-top:12px}.intro{color:#5d6470;font-size:8px;margin-top:4px}.period-title{color:#0f5b63;font-size:8px;font-weight:bold;text-transform:uppercase}.meta{padding:8px 5px}.value{font-size:9px;font-weight:bold;color:#1f2328}.content{padding:18px 20px 20px}.columns{margin-bottom:14px}.column-left{width:50%;padding-right:7px}.column-right{width:50%;padding-left:7px}.card{border:1px solid #deddd9;padding:13px;background-color:#fff}.card-title{font-size:12px;font-weight:bold;margin-bottom:10px}.details td{width:50%;padding:6px 5px}.detail-value{font-size:9px;font-weight:bold}.salary-left{width:63%;padding-right:7px}.salary-right{width:37%;padding-left:7px}.pay-table{width:100%;border-collapse:collapse}.pay-table th{padding:7px 4px;border-bottom:1px solid #d8d8d5;color:#7a8189;font-size:7px;text-transform:uppercase;text-align:left}.pay-table td{padding:9px 4px;border-bottom:1px solid #ebeae7}.right{text-align:right}.summary{padding:14px;background-color:#0f5b63;color:#fff}.summary .label{color:#b8d0d2}.net{margin:11px 0;padding:12px;background-color:#256b72;border:1px solid #4d858a}.net-amount{font-family:dejavuserif,serif;font-size:18px;font-weight:bold;color:#fff}.summary-table td{padding:7px 0;color:#fff;border-bottom:1px solid #4d7a7e}.remarks{margin-top:14px}.footer{margin-top:15px;padding-top:11px;border-top:1px solid #deddd9;color:#5d6470;font-size:8px}.seal{color:#0f5b63;font-size:7px;font-weight:bold;text-align:right;text-transform:uppercase;letter-spacing:.5px}
</style></head><body>
<div class="page">
  <div class="hero">
    <table class="hero-table"><tr>
      <td class="brand">
        <?php if($logo_url){ ?><img src="<?php echo $logo_url; ?>" class="logo"><br><?php } ?>
        <span class="eyebrow">Payslip Statement</span><br>
        <span class="company">LUXCRAFT PTE. LTD.</span><br>
        <span class="uen">UEN: 202343751W</span>
        <div class="status <?php echo $is_paid ? 'status-paid' : 'status-pending'; ?>"><?php echo $status_label; ?></div>
        <div class="title"><?php echo luxcraft_format_salary_month($payslip['salary_month']); ?> Payslip</div>
        <div class="intro">A clear summary of your monthly salary, statutory contributions and payment details.</div>
      </td>
      <td class="period">
        <div class="period-title">Payroll Summary</div>
        <table class="details">
          <tr><td class="meta"><span class="label">Salary Month</span><br><span class="value"><?php echo luxcraft_format_salary_month($payslip['salary_month']); ?></span></td><td class="meta"><span class="label">Payment Date</span><br><span class="value"><?php echo _d($payslip['payment_date']); ?></span></td></tr>
          <tr><td class="meta"><span class="label">Currency</span><br><span class="value">SGD</span></td><td class="meta"><span class="label">Payment Method</span><br><span class="value"><?php echo $payslip['payment_method']; ?></span></td></tr>
        </table>
      </td>
    </tr></table>
  </div>
  <div class="content">
    <table class="columns"><tr>
      <td class="column-left"><div class="card"><div class="card-title">Employee Information</div><table class="details"><tr><td><span class="label">Employee Name</span><br><span class="detail-value"><?php echo $employee_name; ?></span></td><td><span class="label">Job Title</span><br><span class="detail-value"><?php echo $payslip['job_title']; ?></span></td></tr></table></div></td>
      <td class="column-right"><div class="card"><div class="card-title">Payment Details</div><table class="details"><tr><td><span class="label">Payment Method</span><br><span class="detail-value"><?php echo $payslip['payment_method']; ?></span></td><td><span class="label">Payment Details</span><br><span class="detail-value"><?php echo $payslip['payment_details']; ?></span></td></tr></table></div></td>
    </tr></table>
    <table class="salary-layout"><tr>
      <td class="salary-left"><div class="card"><div class="card-title">Salary Breakdown</div><table class="pay-table">
        <tr><th>Description</th><th class="right">Amount (SGD)</th></tr>
        <tr><td><strong>Base Salary</strong></td><td class="right"><?php echo luxcraft_pdf_money($payslip['base_salary']); ?></td></tr>
        <?php if((float)$payslip['commission'] != 0){ ?><tr><td><strong>Commission</strong></td><td class="right"><?php echo luxcraft_pdf_money($payslip['commission']); ?></td></tr><?php } ?>
        <?php if((float)$payslip['allowances'] != 0){ ?><tr><td><strong>Allowances</strong></td><td class="right"><?php echo luxcraft_pdf_money($payslip['allowances']); ?></td></tr><?php } ?>
        <?php if((float)$payslip['deductions'] != 0){ ?><tr><td><strong>Other Deductions</strong></td><td class="right">-<?php echo luxcraft_pdf_money($payslip['deductions']); ?></td></tr><?php } ?>
        <tr><td><strong>Employee CPF Contribution</strong></td><td class="right">-<?php echo luxcraft_pdf_money($payslip['cpf_employee']); ?></td></tr>
      </table></div></td>
      <td class="salary-right"><div class="summary"><span class="label">Net Salary / Take Home Pay</span><div class="net"><span class="label">Amount Payable</span><br><span class="net-amount"><?php echo luxcraft_pdf_money($payslip['take_home_pay']); ?></span></div><table class="summary-table"><tr><td>Employer CPF Contribution</td><td class="right"><strong><?php echo luxcraft_pdf_money($payslip['cpf_employer']); ?></strong></td></tr><tr><td>Total CPF Contribution</td><td class="right"><strong><?php echo luxcraft_pdf_money($payslip['cpf_total']); ?></strong></td></tr></table></div></td>
    </tr></table>
    <?php if($payslip['remarks']){ ?><div class="card remarks"><div class="card-title">Remarks</div><?php echo nl2br($payslip['remarks']); ?></div><?php } ?>
    <table class="footer"><tr><td>This is a computer-generated payslip. No signature is required.</td><td class="seal">Confidential payroll record</td></tr></table>
  </div>
</div>
</body></html>
