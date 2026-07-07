<?php defined('BASEPATH') or exit('No direct script access allowed'); init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="luxcraft-card">
      <h4><?php echo $title; ?></h4>
      <p class="luxcraft-muted">Payslip generation pulls job title, salary, employment type, CPF, payment method and payment details from the employee Payroll Profile.</p>
      <hr>

      <?php echo form_open(current_url(), ['id'=>'payslip-form']); ?>

      <div class="luxcraft-form-section mBottom20">
        <h4>Payroll Details</h4>
        <div class="luxcraft-form-grid">
          <div class="luxcraft-col-4">
            <div class="form-group luxcraft-field" style="display:block;width:100%;clear:both;">
              <label for="staff_id" style="display:block;width:100%;margin-bottom:6px;float:none;clear:both;">Employee</label>
              <select name="staff_id" id="staff_id" class="selectpicker form-control" data-live-search="true" required style="width:100%;display:block;clear:both;">
                <option value="">Select Employee</option>
                <?php foreach($staff as $s){ ?>
                  <option value="<?php echo $s['staffid']; ?>" <?php echo isset($payslip)&&$payslip['staff_id']==$s['staffid']?'selected':''; ?>>
                    <?php echo $s['firstname'].' '.$s['lastname']; ?>
                  </option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="luxcraft-col-4">
            <div class="form-group">
              <label>Salary Month</label>
              <input type="month" name="salary_month" class="form-control" value="<?php echo isset($payslip)?$payslip['salary_month']:date('Y-m'); ?>" required>
            </div>
          </div>

          <div class="luxcraft-col-4">
            <?php echo render_date_input('payment_date','Payment Date', isset($payslip)?$payslip['payment_date']:date('Y-m-d')); ?>
          </div>
        </div>
      </div>

      <div class="luxcraft-form-section mBottom20">
        <h4>Additional Payments / Deductions</h4>
        <div class="luxcraft-form-grid">
          <div class="luxcraft-col-4"><?php echo render_input('commission','Commission', isset($payslip)?$payslip['commission']:'0.00','number',['step'=>'0.01']); ?></div>
          <div class="luxcraft-col-4"><?php echo render_input('allowances','Allowances', isset($payslip)?$payslip['allowances']:'0.00','number',['step'=>'0.01']); ?></div>
          <div class="luxcraft-col-4"><?php echo render_input('deductions','Other Deductions', isset($payslip)?$payslip['deductions']:'0.00','number',['step'=>'0.01']); ?></div>
        </div>
      </div>

      <div class="luxcraft-form-section mBottom20">
        <h4>Payroll Profile</h4>
        <div id="profile-summary">
          <p class="luxcraft-muted mBottom0">Select an employee to preview salary, CPF and payment details.</p>
        </div>
      </div>

      <div class="luxcraft-form-section mBottom20">
        <h4>Remarks</h4>
        <?php echo render_textarea('remarks','', isset($payslip)?$payslip['remarks']:''); ?>
      </div>

      <?php if(isset($payslip) && $payslip['status']=='paid'){ ?>
        <div class="alert alert-warning">This payslip is marked as paid and cannot be edited.</div>
      <?php } else { ?>
        <button class="btn btn-primary"><?php echo isset($payslip) ? 'Save Payslip' : 'Generate Payslip'; ?></button>
      <?php } ?>

      <?php echo form_close(); ?>
    </div>
  </div>
</div>

<script>
(function(){
  function n(v){var x=parseFloat(v);return isNaN(x)?0:x}
  function money(v){return '$'+n(v).toFixed(2)}
  function safe(v){return (v === null || v === undefined || v === '') ? '-' : v}

  function render(d){
    if(!d || !d.profile_found){
      $('#profile-summary').html('<p class="luxcraft-muted mBottom0">No Payroll Profile found. Please create one first.</p>');
      return;
    }

    $('#profile-summary').html(
      '<div class="luxcraft-table-responsive">' +
      '<table class="table table-bordered luxcraft-summary-table">' +
      '<tbody>' +
      '<tr><th>Payroll Name</th><td>'+safe(d.payroll_name)+'</td></tr>' +
      '<tr><th>Job Title</th><td>'+safe(d.job_title)+'</td></tr>' +
      '<tr><th>Base Salary</th><td>'+money(d.base_salary)+'</td></tr>' +
      '<tr><th>Employment Type</th><td>'+safe(d.employment_type)+'</td></tr>' +
      '<tr><th>Payment Method</th><td>'+safe(d.payment_method)+'</td></tr>' +
      '<tr><th>Payment Details</th><td>'+safe(d.payment_details)+'</td></tr>' +
      '<tr><th>CPF Employee</th><td>'+money(d.cpf_employee)+'</td></tr>' +
      '<tr><th>CPF Employer</th><td>'+money(d.cpf_employer)+'</td></tr>' +
      '<tr><th>Total CPF</th><td>'+money(d.cpf_total)+'</td></tr>' +
      (parseInt(d.commission_enabled)==1 ? '<tr><th>Commission Rate</th><td>'+safe(d.commission_rate)+'%</td></tr>' : '') +
      '</tbody></table></div>'
    );
  }

  function refresh(){
    var staffId=$('#staff_id').val();
    if(!staffId){ return render(null); }

    $.post(admin_url+'luxcraft_payslips/calculate_json',{
      staff_id:staffId,
      commission:$('[name="commission"]').val(),
      allowances:$('[name="allowances"]').val(),
      deductions:$('[name="deductions"]').val()
    }, render, 'json').fail(function(xhr){
      console.log(xhr.responseText);
      alert_float('danger','Unable to load Payroll Profile.');
    });
  }

  $(document).on('changed.bs.select change','#staff_id',refresh);
  $(document).on('keyup change','[name="commission"],[name="allowances"],[name="deductions"]',refresh);
  refresh();
})();
</script>
<?php init_tail(); ?>
