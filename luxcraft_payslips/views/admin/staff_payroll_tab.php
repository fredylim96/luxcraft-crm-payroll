<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="luxcraft-card">
  <h4>Payroll Profile</h4>
  <?php echo form_open(admin_url('luxcraft_payslips/save_staff_payroll_profile')); ?>
    <input type="hidden" name="profile_id" value="<?php echo isset($profile['id']) ? $profile['id'] : ''; ?>">
    <input type="hidden" name="staff_id" value="<?php echo $staff_id; ?>">
    <div class="luxcraft-form-grid">
      <div class="luxcraft-col-4"><?php echo render_input('payroll_name','Payroll Name', isset($profile['payroll_name']) ? $profile['payroll_name'] : ''); ?></div>
      <div class="luxcraft-col-4"><?php echo render_input('job_title','Job Title', isset($profile['job_title']) ? $profile['job_title'] : ''); ?></div>
      <div class="luxcraft-col-4"><?php echo render_input('monthly_salary','Monthly Salary', isset($profile['monthly_salary']) ? $profile['monthly_salary'] : '0.00','number',['step'=>'0.01']); ?></div>
      <div class="luxcraft-col-4">
        <label>Employment Type</label>
        <select name="employment_type" class="form-control luxcraft-employment-type">
          <?php foreach($employment_types as $key=>$label){ ?>
            <option value="<?php echo $key; ?>" <?php echo isset($profile['employment_type']) && $profile['employment_type']==$key ? 'selected' : ''; ?>><?php echo $label; ?></option>
          <?php } ?>
        </select>
      </div>
      <div class="luxcraft-col-4">
        <label>Payment Method</label>
        <select name="payment_method" class="form-control">
          <?php foreach($payment_methods as $m){ ?>
            <option value="<?php echo $m; ?>" <?php echo isset($profile['payment_method']) && $profile['payment_method']==$m ? 'selected' : ''; ?>><?php echo $m; ?></option>
          <?php } ?>
        </select>
      </div>
      <div class="luxcraft-col-8"><?php echo render_input('payment_details','Payment Details', isset($profile['payment_details']) ? $profile['payment_details'] : ''); ?></div>
      <div class="luxcraft-col-3">
        <div class="checkbox checkbox-primary">
          <input type="checkbox" name="commission_enabled" class="commission-enabled luxcraft-commission-toggle" <?php echo isset($profile['commission_enabled']) && $profile['commission_enabled'] ? 'checked' : ''; ?>>
          <label>Commission Enabled</label>
        </div>
      </div>
      <div class="luxcraft-col-3 luxcraft-commission-rate-wrap commission-rate-wrap <?php echo isset($profile['commission_enabled']) && $profile['commission_enabled'] ? '' : 'luxcraft-hidden'; ?>">
        <?php echo render_input('commission_rate','Commission %', isset($profile['commission_rate']) ? $profile['commission_rate'] : '0.00','number',['step'=>'0.01']); ?>
      </div>
    </div>
    <button class="btn btn-primary">Save Payroll Profile</button>
  <?php echo form_close(); ?>
</div>





<script>
(function luxcraftStaffPayrollCommissionToggle(){
  function toggleCommissionFields(root){
    root = root || document;
    var checkboxes = root.querySelectorAll('input[name="commission_enabled"]');
    for (var i = 0; i < checkboxes.length; i++) {
      var cb = checkboxes[i];
      var form = cb.closest('form') || document;
      var wraps = form.querySelectorAll('.luxcraft-commission-rate-wrap, .commission-rate-wrap');
      for (var j = 0; j < wraps.length; j++) {
        var wrap = wraps[j];
        if (cb.checked) {
          wrap.classList.remove('luxcraft-hidden');
          wrap.style.display = '';
        } else {
          wrap.classList.add('luxcraft-hidden');
          wrap.style.display = 'none';
        }
        var inputs = wrap.querySelectorAll('input, select, textarea');
        for (var k = 0; k < inputs.length; k++) {
          inputs[k].disabled = !cb.checked;
        }
      }
    }
  }

  document.addEventListener('change', function(e){
    if (e.target && e.target.name === 'commission_enabled') {
      toggleCommissionFields(e.target.closest('form') || document);
    }
  });

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function(){ toggleCommissionFields(document); });
  } else {
    toggleCommissionFields(document);
  }
})();
</script>
