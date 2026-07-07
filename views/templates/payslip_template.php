<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$logo_url = luxcraft_company_logo_url();
$employee_name = !empty($payslip['payroll_name']) ? $payslip['payroll_name'] : $payslip['firstname'].' '.$payslip['lastname'];
$is_pdf = isset($is_pdf) && $is_pdf;
?>

<?php if($is_pdf){ ?>
<style>
body{font-family:DejaVu Sans,sans-serif;font-size:10.8px;color:#111827;line-height:1.35}
.wrapper{width:100%}
.header{text-align:center;border-bottom:3px solid #111827;padding-bottom:18px;margin-bottom:22px}
.header-table{width:100%;border-collapse:collapse}
.header-table td{border:none;padding:0;vertical-align:top}
.logo{width:200px;max-width:200px;margin-bottom:14px}
.company{font-size:18px;font-weight:bold;letter-spacing:.3px}
.muted{color:#6b7280;font-size:9.5px}
.doc-title{text-align:center;font-size:24px;font-weight:bold;letter-spacing:1px;margin-top:10px}
.status{display:inline-block;padding:4px 8px;font-size:9px;font-weight:bold;border-radius:8px;background:#f3f4f6;color:#374151}
.status-paid{background:#dcfce7;color:#166534}
.info-two{width:100%;border-collapse:collapse;margin-bottom:16px}
.info-two td{border:none;padding:0;vertical-align:top}
.info-left{width:49%;padding-right:1%}
.info-right{width:49%;padding-left:1%}
.box-title{background:#f3f4f6;border:1px solid #d1d5db;border-bottom:none;padding:8px 9px;font-weight:bold;text-transform:uppercase;font-size:9.5px;letter-spacing:.4px}
table.clean{width:100%;border-collapse:collapse;margin-bottom:12px}
table.clean th,table.clean td{border:1px solid #d1d5db;padding:7px 8px}
table.clean th{background:#fafafa;text-align:left}
table.pay th{background:#f3f4f6}
.right{text-align:right}
.net-row td,.net-row th{background:#111827;color:#fff;font-weight:bold;font-size:11.5px}
.notes{border:1px solid #d1d5db;padding:9px;min-height:34px;margin-bottom:12px}
.footer{margin-top:24px;padding-top:10px;border-top:1px solid #e5e7eb;text-align:center;color:#6b7280;font-style:italic;font-size:9.5px}
</style>
<div class="wrapper">

<div class="header">
  <?php if($logo_url){ ?><img src="<?php echo $logo_url; ?>" class="logo"><?php } ?>
  <div class="company">LUXCRAFT PTE. LTD.</div>
  <div class="muted"><strong>UEN: 202343751W</strong></div>
  <div class="doc-title">PAYSLIP</div>
  <div class="muted"><?php echo luxcraft_format_salary_month($payslip['salary_month']); ?></div>
</div>

  <table class="info-two"><tr>
    <td class="info-left">
      <div class="box-title">Employee Information</div>
      <table class="clean"><tr><th>Employee Name</th><td><?php echo $employee_name; ?></td></tr><tr><th>Job Title</th><td><?php echo $payslip['job_title']; ?></td></tr></table>
    </td>
    <td class="info-right">
      <div class="box-title">Payroll Information</div>
      <table class="clean"><tr><th>Salary Month</th><td><?php echo luxcraft_format_salary_month($payslip['salary_month']); ?></td></tr><tr><th>Payment Date</th><td><?php echo _d($payslip['payment_date']); ?></td></tr><tr><th>Payment Method</th><td><?php echo $payslip['payment_method']; ?></td></tr></table>
    </td>
  </tr></table>
  <div class="box-title">Salary Breakdown</div>
  <table class="clean pay">
    <tr><th>Description</th><th class="right">Amount (SGD)</th></tr>
    <tr><td>Base Salary</td><td class="right"><?php echo luxcraft_pdf_money($payslip['base_salary']); ?></td></tr>
    <?php if((float)$payslip['commission'] != 0){ ?><tr><td>Commission</td><td class="right"><?php echo luxcraft_pdf_money($payslip['commission']); ?></td></tr><?php } ?>
    <?php if((float)$payslip['allowances'] != 0){ ?><tr><td>Allowances</td><td class="right"><?php echo luxcraft_pdf_money($payslip['allowances']); ?></td></tr><?php } ?>
    <?php if((float)$payslip['deductions'] != 0){ ?><tr><td>Other Deductions</td><td class="right">-<?php echo luxcraft_pdf_money($payslip['deductions']); ?></td></tr><?php } ?>
    <tr><td>Employee CPF Contribution</td><td class="right">-<?php echo luxcraft_pdf_money($payslip['cpf_employee']); ?></td></tr>
    <tr class="net-row"><td>Net Salary / Take Home Pay</td><td class="right"><?php echo luxcraft_pdf_money($payslip['take_home_pay']); ?></td></tr>
  </table>
  <div class="box-title">Employer Contributions</div>
  <table class="clean"><tr><th>Employer CPF Contribution</th><td class="right"><?php echo luxcraft_pdf_money($payslip['cpf_employer']); ?></td></tr><tr><th>Total CPF Contribution</th><td class="right"><?php echo luxcraft_pdf_money($payslip['cpf_total']); ?></td></tr></table>
  <div class="box-title">Payment Details</div>
  <table class="clean"><tr><th>Payment Method</th><td><?php echo $payslip['payment_method']; ?></td></tr><tr><th>Payment Details</th><td><?php echo $payslip['payment_details']; ?></td></tr></table>
  <?php if($payslip['remarks']){ ?><div class="box-title">Remarks</div><div class="notes"><?php echo nl2br($payslip['remarks']); ?></div><?php } ?>
  <div class="footer">This is a computer-generated payslip. No signature is required.</div>
</div>
<?php } else { ?>
<div class="luxcraft-invoice-shell">

<div class="luxcraft-invoice-header luxcraft-invoice-header-centered">
  <?php if($logo_url){ ?><img src="<?php echo $logo_url; ?>" class="luxcraft-invoice-logo luxcraft-invoice-logo-large"><?php } ?>
  <div class="luxcraft-invoice-company">LUXCRAFT PTE. LTD.</div>
  <div class="luxcraft-invoice-muted"><strong>UEN: 202343751W</strong></div>
  <div class="luxcraft-invoice-title luxcraft-invoice-title-centered">
    <h2>PAYSLIP</h2>
    
  </div>
</div>
  <div class="row">
    <div class="col-md-6">
      <h4>Employee Information</h4>
      <table class="table table-bordered">
        <tr><th>Employee Name</th><td><?php echo $employee_name; ?></td></tr>
        <tr><th>Job Title</th><td><?php echo $payslip['job_title']; ?></td></tr>
      </table>
    </div>
    <div class="col-md-6">
      <h4>Payroll Information</h4>
      <table class="table table-bordered">
        <tr><th>Salary Month</th><td><?php echo luxcraft_format_salary_month($payslip['salary_month']); ?></td></tr>
        <tr><th>Payment Date</th><td><?php echo _d($payslip['payment_date']); ?></td></tr>
      </table>
    </div>
  </div>

  <h4>Salary Breakdown</h4>
  <table class="table table-bordered luxcraft-pay-table"><thead><tr><th>Description</th><th class="text-right">Amount (SGD)</th></tr></thead><tbody>
    <tr><td>Base Salary</td><td class="text-right"><?php echo app_format_money($payslip['base_salary'], 'SGD'); ?></td></tr>
    <?php if((float)$payslip['commission'] != 0){ ?><tr><td>Commission</td><td class="text-right"><?php echo app_format_money($payslip['commission'], 'SGD'); ?></td></tr><?php } ?>
    <?php if((float)$payslip['allowances'] != 0){ ?><tr><td>Allowances</td><td class="text-right"><?php echo app_format_money($payslip['allowances'], 'SGD'); ?></td></tr><?php } ?>
    <?php if((float)$payslip['deductions'] != 0){ ?><tr><td>Other Deductions</td><td class="text-right">-<?php echo app_format_money($payslip['deductions'], 'SGD'); ?></td></tr><?php } ?>
    <tr><td>Employee CPF Contribution</td><td class="text-right">-<?php echo app_format_money($payslip['cpf_employee'], 'SGD'); ?></td></tr>
    <tr class="luxcraft-net-row"><th>Net Salary / Take Home Pay</th><th class="text-right"><?php echo app_format_money($payslip['take_home_pay'], 'SGD'); ?></th></tr>
  </tbody></table>
  <h4>Employer Contributions</h4>
  <table class="table table-bordered"><tr><th>Employer CPF Contribution</th><td class="text-right"><?php echo app_format_money($payslip['cpf_employer'], 'SGD'); ?></td></tr><tr><th>Total CPF Contribution</th><td class="text-right"><?php echo app_format_money($payslip['cpf_total'], 'SGD'); ?></td></tr></table>
  <h4>Payment Details</h4>
  <table class="table table-bordered"><tr><th>Payment Method</th><td><?php echo $payslip['payment_method']; ?></td></tr><tr><th>Payment Details</th><td><?php echo $payslip['payment_details']; ?></td></tr></table>
  <?php if($payslip['remarks']){ ?><h4>Remarks</h4><div class="luxcraft-notes-box"><?php echo nl2br($payslip['remarks']); ?></div><?php } ?>
  <div class="luxcraft-payslip-footer">This is a computer-generated payslip. No signature is required.</div>
</div>
<?php } ?>
