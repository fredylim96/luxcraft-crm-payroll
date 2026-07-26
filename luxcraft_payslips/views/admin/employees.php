<?php defined('BASEPATH') or exit('No direct script access allowed'); init_head(); ?>
<div id="wrapper"><div class="content">
<div class="luxcraft-card">
  <div class="luxcraft-actions" style="justify-content:space-between;">
    <h4><?php echo isset($edit_profile) && $edit_profile ? 'Edit Payroll Profile' : 'Payroll Profiles'; ?></h4>
    <?php if(isset($edit_profile) && $edit_profile){ ?><a href="<?php echo admin_url('luxcraft_payslips/employees'); ?>" class="btn btn-default">Cancel Edit</a><?php } ?>
  </div><hr>
  <?php echo form_open(admin_url('luxcraft_payslips/employees')); ?>
    <input type="hidden" name="profile_id" value="<?php echo isset($edit_profile['id']) ? $edit_profile['id'] : ''; ?>">
    <div class="luxcraft-form-grid">
      <div class="luxcraft-col-4">
        <div class="form-group luxcraft-field">
          <label for="profile_staff_id">Employee</label>
          <select name="staff_id" id="profile_staff_id" class="selectpicker form-control" data-live-search="true" required>
          <option value="">Select Employee</option>
          <?php foreach($staff as $s){ ?><option value="<?php echo $s['staffid']; ?>" <?php echo isset($edit_profile['staff_id']) && $edit_profile['staff_id']==$s['staffid'] ? 'selected' : ''; ?>><?php echo $s['firstname'].' '.$s['lastname']; ?></option><?php } ?>
        </select>
        </div>
      </div>
      <div class="luxcraft-col-4"><?php echo render_input('payroll_name','Payroll Name', isset($edit_profile['payroll_name']) ? $edit_profile['payroll_name'] : ''); ?></div>
      <div class="luxcraft-col-4"><?php echo render_input('job_title','Job Title', isset($edit_profile['job_title']) ? $edit_profile['job_title'] : ''); ?></div>
      <div class="luxcraft-col-4"><?php echo render_input('monthly_salary','Monthly Salary', isset($edit_profile['monthly_salary']) ? $edit_profile['monthly_salary'] : '0.00','number',['step'=>'0.01']); ?></div>
      <div class="luxcraft-col-4">
        <label>Employment Type</label>
        <select name="employment_type" class="form-control">
          <?php foreach($employment_types as $key=>$label){ ?><option value="<?php echo $key; ?>" <?php echo isset($edit_profile['employment_type']) && $edit_profile['employment_type']==$key ? 'selected' : ''; ?>><?php echo $label; ?></option><?php } ?>
        </select>
      </div>
      <div class="luxcraft-col-4">
        <label>Payment Method</label>
        <select name="payment_method" class="form-control">
          <?php foreach($payment_methods as $m){ ?><option value="<?php echo $m; ?>" <?php echo isset($edit_profile['payment_method']) && $edit_profile['payment_method']==$m ? 'selected' : ''; ?>><?php echo $m; ?></option><?php } ?>
        </select>
      </div>
      <div class="luxcraft-col-4"><?php echo render_input('payment_details','Payment Details', isset($edit_profile['payment_details']) ? $edit_profile['payment_details'] : ''); ?></div>
      <div class="luxcraft-col-3">
        <div class="checkbox checkbox-primary">
          <input type="checkbox" name="commission_enabled" id="commission_enabled" class="luxcraft-commission-toggle" <?php echo isset($edit_profile['commission_enabled']) && $edit_profile['commission_enabled'] ? 'checked' : ''; ?>>
          <label for="commission_enabled">Commission Enabled</label>
        </div>
      </div>
      <div class="luxcraft-col-3 luxcraft-commission-rate-wrap commission-rate-wrap <?php echo isset($edit_profile['commission_enabled']) && $edit_profile['commission_enabled'] ? '' : 'luxcraft-hidden'; ?>">
        <?php echo render_input('commission_rate','Commission %', isset($edit_profile['commission_rate']) ? $edit_profile['commission_rate'] : '0.00','number',['step'=>'0.01']); ?>
      </div>
      <div class="luxcraft-col-12"><button class="btn btn-primary"><?php echo isset($edit_profile) && $edit_profile ? 'Update Profile' : 'Save Profile'; ?></button></div>
    </div>
  <?php echo form_close(); ?>
</div>
<div class="luxcraft-card">
<h4>Saved Payroll Profiles</h4>
<div class="luxcraft-table-responsive"><table class="table dt-table">
<thead><tr><th>CRM Staff Name</th><th>Payroll Name</th><th>Job Title</th><th>Monthly Salary</th><th>Employment Type</th><th>Payment</th><th>Commission</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($profiles as $p){ ?><tr>
<td><?php echo $p['firstname'].' '.$p['lastname']; ?></td>
<td><?php echo $p['payroll_name'] ?: $p['firstname'].' '.$p['lastname']; ?></td>
<td><?php echo $p['job_title']; ?></td>
<td><?php echo app_format_money($p['monthly_salary'], 'SGD'); ?></td>
<td><?php echo isset($employment_types[$p['employment_type']]) ? $employment_types[$p['employment_type']] : $p['employment_type']; ?></td>
<td><?php echo $p['payment_method'].' - '.$p['payment_details']; ?></td>
<td><?php echo $p['commission_enabled'] ? $p['commission_rate'].'%' : 'No'; ?></td>
<td><a href="<?php echo admin_url('luxcraft_payslips/employees?edit='.$p['id']); ?>" class="btn btn-default btn-sm"><i class="fa fa-edit"></i> Edit</a> <a href="<?php echo admin_url('luxcraft_payslips/delete_profile/'.$p['id']); ?>" class="btn btn-danger btn-sm _delete"><i class="fa fa-trash"></i> Delete</a></td>
</tr><?php } ?>
</tbody></table></div></div>
</div></div>




<?php init_tail(); ?>

<script>
(function(){
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

