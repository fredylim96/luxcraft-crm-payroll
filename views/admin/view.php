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
  #side-menu,
  #header,
  .navbar,
  .screen-options-area,
  .btn,
  footer,
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
  }@page {
    size: A4 portrait;
    margin: 10mm;
  }
}
</style>




<script>
(function(){
  function mmToPx(mm) {
    var el = document.createElement('div');
    el.style.width = mm + 'mm';
    el.style.position = 'absolute';
    el.style.visibility = 'hidden';
    document.body.appendChild(el);
    var px = el.offsetWidth;
    document.body.removeChild(el);
    return px;
  }

  function clearPrintPageNumbers() {
    document.querySelectorAll('.luxcraft-print-page-number').forEach(function(el){
      el.parentNode.removeChild(el);
    });
  }

  function addPrintPageNumbers() {
    clearPrintPageNumbers();

    var area = document.querySelector('.luxcraft-print-area');
    if (!area) return;

    // A4 height minus @page top/bottom margins: 297mm - 20mm.
    var printableHeight = mmToPx(277);
    var contentHeight = Math.max(area.scrollHeight, area.offsetHeight);
    var pages = Math.max(1, Math.ceil(contentHeight / printableHeight));

    for (var i = 1; i <= pages; i++) {
      var pageNo = document.createElement('div');
      pageNo.className = 'luxcraft-print-page-number';
      pageNo.textContent = i + ' / ' + pages;
      pageNo.style.top = ((i * printableHeight) - 22) + 'px';
      area.appendChild(pageNo);
    }
  }

  if (window.matchMedia) {
    var mediaQueryList = window.matchMedia('print');
    mediaQueryList.addListener(function(mql) {
      if (mql.matches) {
        addPrintPageNumbers();
      } else {
        setTimeout(clearPrintPageNumbers, 300);
      }
    });
  }

  window.addEventListener('beforeprint', addPrintPageNumbers);
  window.addEventListener('afterprint', function(){
    setTimeout(clearPrintPageNumbers, 300);
  });
})();
</script>

<?php init_tail(); ?>
