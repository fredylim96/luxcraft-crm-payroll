<?php defined('BASEPATH') or exit('No direct script access allowed'); init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="luxcraft-card luxcraft-print-area">
      <?php $this->load->view('luxcraft_payslips/templates/payslip_card', ['payslip'=>$payslip]); ?>
      <hr>
      <?php /* v44 admin-only action confirmation: edit / paid / unpaid buttons below remain wrapped with is_admin(). */ ?>
        <div class="luxcraft-actions luxcraft-view-actions" style="display:flex;justify-content:center;align-items:center;gap:8px;flex-wrap:wrap;text-align:center;width:100%;">
        <button type="button" class="btn btn-default" onclick="window.print();">
          <i class="fa fa-print"></i> Print Payslip
        </button>
        <a href="<?php echo admin_url('luxcraft_payslips/download/'.$payslip['id']); ?>" class="btn btn-default">
          <i class="fa fa-file-pdf-o"></i> Download PDF
        </a>

        <?php if((is_admin() || has_permission('luxcraft_payslips', '', 'edit') || has_permission('luxcraft_payslips', '', 'mark_paid')) && empty($is_employee_view)){ ?>
          <?php if($payslip['status']!='paid'){ ?>
            <?php if(is_admin() || has_permission('luxcraft_payslips', '', 'edit')){ ?>
              <a href="<?php echo admin_url('luxcraft_payslips/create/'.$payslip['id']); ?>" class="btn btn-default">
                <i class="fa fa-edit"></i> Edit
              </a>
            <?php } ?>
            <?php if(is_admin() || has_permission('luxcraft_payslips', '', 'mark_paid')){ ?>
              <a href="<?php echo admin_url('luxcraft_payslips/mark_paid/'.$payslip['id']); ?>" class="btn btn-success">
                <i class="fa fa-lock"></i> Mark as Paid
              </a>
            <?php } ?>
          <?php } else { ?>
            <?php if(is_admin() || has_permission('luxcraft_payslips', '', 'mark_paid')){ ?>
              <a href="<?php echo admin_url('luxcraft_payslips/mark_unpaid/'.$payslip['id']); ?>" class="btn btn-warning">
                <i class="fa fa-unlock"></i> Mark as Unpaid
              </a>
            <?php } ?>
          <?php } ?>
        <?php } ?>
      </div>
    </div>
  </div>
</div>



<style>
@media print {
  html,
  body {
    margin: 0 !important;
    padding: 0 !important;
    background: #fff !important;
  }

  body * {
    visibility: hidden !important;
  }

  .luxcraft-print-area,
  .luxcraft-print-area * {
    visibility: visible !important;
  }

  .luxcraft-print-area {
    position: static !important;
    display: block !important;
    width: 100% !important;
    max-width: 100% !important;
    min-height: 0 !important;
    height: auto !important;
    margin: 0 auto !important;
    padding: 0 !important;
    border: none !important;
    box-shadow: none !important;
    background: #fff !important;
    transform: none !important;
    page-break-before: auto !important;
    page-break-after: auto !important;
  }

  .luxcraft-print-area .luxcraft-invoice-shell {
    width: 100% !important;
    max-width: 100% !important;
    margin: 0 auto !important;
    padding: 0 !important;
    border: none !important;
    box-shadow: none !important;
    background: #fff !important;
    transform: none !important;
    page-break-before: auto !important;
    page-break-after: auto !important;
    page-break-inside: avoid !important;
  }

  .luxcraft-actions,
  .luxcraft-actions *,
  .luxcraft-print-area > hr,
  #side-menu,
  #header,
  .navbar,
  .screen-options-area,
  .btn,
  body > footer,
  .modal,
  .alert {
    display: none !important;
    visibility: hidden !important;
  }

  #wrapper,
  .content,
  .row,
  .col-md-12,
  .panel_s,
  .panel-body {
    margin: 0 !important;
    padding: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    min-height: 0 !important;
    height: auto !important;
  }

  .luxcraft-print-area .row {
    margin-left: 0 !important;
    margin-right: 0 !important;
  }

  .luxcraft-print-area .col-md-6 {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }

  .luxcraft-print-area table {
    width: 100% !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
  }

  .luxcraft-print-area .luxcraft-payslip-sheet {
    width: 147.06% !important;
    max-width: 147.06% !important;
    margin: 0 !important;
    zoom: 0.68;
    page-break-inside: avoid !important;
    break-inside: avoid-page !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  @page {
    size: A4 portrait;
    margin: 6mm;
  }
}
</style>

<?php init_tail(); ?>
