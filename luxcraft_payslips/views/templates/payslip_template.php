<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$logo_url = luxcraft_company_logo_url();
$employee_name = !empty($payslip['payroll_name']) ? $payslip['payroll_name'] : $payslip['firstname'].' '.$payslip['lastname'];
$money = function ($amount) {
    return app_format_money($amount, 'SGD');
};
?>
<main class="luxcraft-payslip-sheet" aria-label="Payslip for <?php echo $employee_name; ?>">
  <header class="luxcraft-payslip-hero">
    <div class="luxcraft-payslip-brand">
      <div class="luxcraft-payslip-brand-row">
        <?php if($logo_url){ ?>
          <div class="luxcraft-payslip-logo"><img src="<?php echo $logo_url; ?>" alt="LuxCraft"></div>
        <?php } else { ?>
          <div class="luxcraft-payslip-logo luxcraft-payslip-logo-mark" aria-hidden="true">L</div>
        <?php } ?>
        <div>
          <div class="luxcraft-payslip-eyebrow">Payslip Statement</div>
          <h1>LUXCRAFT PTE. LTD.</h1>
          <div class="luxcraft-payslip-uen">UEN: 202343751W</div>
        </div>
      </div>
      <div class="luxcraft-payslip-pill"><span></span>Payroll statement</div>
      <div>
        <h2><?php echo luxcraft_format_salary_month($payslip['salary_month']); ?> Payslip</h2>
        <p>A clear summary of your monthly salary, statutory contributions and payment details.</p>
      </div>
    </div>

    <aside class="luxcraft-payslip-period">
      <div class="luxcraft-payslip-eyebrow">Payroll Summary</div>
      <div class="luxcraft-payslip-period-grid">
        <div><span>Salary Month</span><strong><?php echo luxcraft_format_salary_month($payslip['salary_month']); ?></strong></div>
        <div><span>Payment Date</span><strong><?php echo _d($payslip['payment_date']); ?></strong></div>
        <div><span>Currency</span><strong>SGD</strong></div>
        <div><span>Payment Method</span><strong><?php echo $payslip['payment_method']; ?></strong></div>
      </div>
    </aside>
  </header>

  <div class="luxcraft-payslip-content">
    <section class="luxcraft-payslip-details-grid">
      <article class="luxcraft-payslip-panel">
        <div class="luxcraft-payslip-section-heading"><h3>Employee Information</h3><span>Employee record</span></div>
        <div class="luxcraft-payslip-detail-grid">
          <div><span>Employee Name</span><strong><?php echo $employee_name; ?></strong></div>
          <div><span>Job Title</span><strong><?php echo $payslip['job_title']; ?></strong></div>
        </div>
      </article>
      <article class="luxcraft-payslip-panel">
        <div class="luxcraft-payslip-section-heading"><h3>Payment Details</h3><span>Payment record</span></div>
        <div class="luxcraft-payslip-detail-grid">
          <div><span>Payment Method</span><strong><?php echo $payslip['payment_method']; ?></strong></div>
          <div><span>Payment Details</span><strong><?php echo $payslip['payment_details']; ?></strong></div>
        </div>
      </article>
    </section>

    <section class="luxcraft-payslip-salary-grid">
      <article class="luxcraft-payslip-panel luxcraft-payslip-breakdown">
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
      </article>

      <aside class="luxcraft-payslip-summary">
        <div class="luxcraft-payslip-summary-label">Net Salary / Take Home Pay</div>
        <div class="luxcraft-payslip-net"><span>Amount payable</span><strong><?php echo $money($payslip['take_home_pay']); ?></strong></div>
        <ul>
          <li><span>Employer CPF Contribution</span><strong><?php echo $money($payslip['cpf_employer']); ?></strong></li>
          <li><span>Total CPF Contribution</span><strong><?php echo $money($payslip['cpf_total']); ?></strong></li>
        </ul>
      </aside>
    </section>

    <?php if($payslip['remarks']){ ?>
      <section class="luxcraft-payslip-panel luxcraft-payslip-remarks">
        <div class="luxcraft-payslip-section-heading"><h3>Remarks</h3><span>Payroll note</span></div>
        <p><?php echo nl2br($payslip['remarks']); ?></p>
      </section>
    <?php } ?>

    <footer class="luxcraft-payslip-document-footer">
      <p>This is a computer-generated payslip. No signature is required.</p>
      <span>Confidential payroll record</span>
    </footer>
  </div>
</main>
