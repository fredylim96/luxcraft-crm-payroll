<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$logo_url = luxcraft_company_logo_url();
$employee_name = !empty($payslip['payroll_name']) ? $payslip['payroll_name'] : $payslip['firstname'].' '.$payslip['lastname'];
$is_pdf = !empty($is_pdf);
$money = function ($amount) use ($is_pdf) {
    return $is_pdf ? luxcraft_pdf_money($amount) : app_format_money($amount, 'SGD');
};
$is_paid = isset($payslip['status']) && $payslip['status'] === 'paid';
$status_label = $is_paid ? 'Salary Credited' : 'Pending Salary Payment';
?>
<main class="luxcraft-payslip-sheet" aria-label="Payslip for <?php echo $employee_name; ?>">
  <header class="luxcraft-payslip-hero"><table class="luxcraft-payslip-layout-table"><tr>
    <td class="luxcraft-payslip-brand-cell"><div class="luxcraft-payslip-brand">
      <table class="luxcraft-payslip-brand-row"><tr>
        <?php if($logo_url){ ?>
          <td class="luxcraft-payslip-brand-logo-cell"><div class="luxcraft-payslip-logo"><img src="<?php echo $logo_url; ?>" alt="LuxCraft"></div></td>
        <?php } else { ?>
          <td class="luxcraft-payslip-brand-logo-cell"><div class="luxcraft-payslip-logo luxcraft-payslip-logo-mark" aria-hidden="true">L</div></td>
        <?php } ?>
        <td class="luxcraft-payslip-brand-text-cell"><div>
          <div class="luxcraft-payslip-eyebrow">Payslip Statement</div>
          <h1>LUXCRAFT PTE. LTD.</h1>
          <div class="luxcraft-payslip-uen">UEN: 202343751W</div>
        </div></td>
      </tr></table>
      <div class="luxcraft-payslip-pill <?php echo $is_paid ? 'luxcraft-payslip-pill-paid' : 'luxcraft-payslip-pill-pending'; ?>"><span></span><?php echo $status_label; ?></div>
      <div>
        <h2><?php echo luxcraft_format_salary_month($payslip['salary_month']); ?> Payslip</h2>
        <p>A clear summary of your monthly salary, statutory contributions and payment details.</p>
      </div>
    </div></td>

    <td class="luxcraft-payslip-period-cell"><aside class="luxcraft-payslip-period">
      <div class="luxcraft-payslip-eyebrow">Payroll Summary</div>
      <table class="luxcraft-payslip-period-grid"><tr>
        <td><span>Salary Month</span><strong><?php echo luxcraft_format_salary_month($payslip['salary_month']); ?></strong></td>
        <td><span>Payment Date</span><strong><?php echo _d($payslip['payment_date']); ?></strong></td>
      </tr><tr>
        <td><span>Currency</span><strong>SGD</strong></td>
        <td><span>Payment Method</span><strong><?php echo $payslip['payment_method']; ?></strong></td>
      </tr></table>
    </aside></td>
  </tr></table></header>

  <div class="luxcraft-payslip-content">
    <section class="luxcraft-payslip-details-grid"><table class="luxcraft-payslip-layout-table"><tr>
      <td class="luxcraft-payslip-half luxcraft-payslip-left-cell"><article class="luxcraft-payslip-panel">
        <div class="luxcraft-payslip-section-heading"><h3>Employee Information</h3><span>Employee record</span></div>
        <table class="luxcraft-payslip-detail-grid"><tr><td><span>Employee Name</span><strong><?php echo $employee_name; ?></strong></td><td><span>Job Title</span><strong><?php echo $payslip['job_title']; ?></strong></td></tr></table>
      </article></td>
      <td class="luxcraft-payslip-half luxcraft-payslip-right-cell"><article class="luxcraft-payslip-panel">
        <div class="luxcraft-payslip-section-heading"><h3>Payment Details</h3><span>Payment record</span></div>
        <table class="luxcraft-payslip-detail-grid"><tr><td><span>Payment Method</span><strong><?php echo $payslip['payment_method']; ?></strong></td><td><span>Payment Details</span><strong><?php echo $payslip['payment_details']; ?></strong></td></tr></table>
      </article></td>
    </tr></table></section>

    <section class="luxcraft-payslip-salary-grid"><table class="luxcraft-payslip-layout-table"><tr>
      <td class="luxcraft-payslip-salary-cell"><article class="luxcraft-payslip-panel luxcraft-payslip-breakdown">
        <div class="luxcraft-payslip-section-heading"><h3>Salary Breakdown</h3><span>Month breakdown</span></div>
        <table aria-label="Salary breakdown">
          <thead><tr><th>Description</th><th class="text-right">Amount (SGD)</th></tr></thead>
          <tbody>
            <tr><td><strong>Base Salary</strong></td><td class="text-right"><?php echo $money($payslip['base_salary']); ?></td></tr>
            <?php if((float)$payslip['commission'] != 0){ ?><tr><td><strong>Commission</strong></td><td class="text-right"><?php echo $money($payslip['commission']); ?></td></tr><?php } ?>
            <?php if((float)$payslip['allowances'] != 0){ ?><tr><td><strong>Allowances</strong></td><td class="text-right"><?php echo $money($payslip['allowances']); ?></td></tr><?php } ?>
            <?php if((float)$payslip['deductions'] != 0){ ?><tr><td><strong>Other Deductions</strong></td><td class="text-right">-<?php echo $money($payslip['deductions']); ?></td></tr><?php } ?>
            <tr><td><strong>Employee CPF Contribution</strong></td><td class="text-right">-<?php echo $money($payslip['cpf_employee']); ?></td></tr>
          </tbody>
        </table>
      </article></td>

      <td class="luxcraft-payslip-summary-cell"><aside class="luxcraft-payslip-summary">
        <div class="luxcraft-payslip-summary-label">Net Salary / Take Home Pay</div>
        <div class="luxcraft-payslip-net"><span>Amount payable</span><strong><?php echo $money($payslip['take_home_pay']); ?></strong></div>
        <table class="luxcraft-payslip-summary-list"><tr><td>Employer CPF Contribution</td><td><strong><?php echo $money($payslip['cpf_employer']); ?></strong></td></tr><tr><td>Total CPF Contribution</td><td><strong><?php echo $money($payslip['cpf_total']); ?></strong></td></tr></table>
      </aside></td>
    </tr></table></section>

    <?php if($payslip['remarks']){ ?>
      <section class="luxcraft-payslip-panel luxcraft-payslip-remarks">
        <div class="luxcraft-payslip-section-heading"><h3>Remarks</h3><span>Payroll note</span></div>
        <p><?php echo nl2br($payslip['remarks']); ?></p>
      </section>
    <?php } ?>

    <footer class="luxcraft-payslip-document-footer"><table><tr>
      <td><p>This is a computer-generated payslip. No signature is required.</p></td>
      <td class="luxcraft-payslip-seal-cell"><span>Confidential payroll record</span></td>
    </tr></table></footer>
  </div>
</main>
