<?php defined('BASEPATH') or exit('No direct script access allowed'); init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="luxcraft-card">
      <h4><?php echo $title; ?></h4>
      <p class="luxcraft-muted">Bulk generate payslips only for employees with Payroll Profiles.</p>
      <hr>

      <?php echo form_open(admin_url('luxcraft_payslips/bulk_create')); ?>

      <div class="luxcraft-form-section mBottom20">
        <h4>Payroll Details</h4>
        <div class="luxcraft-form-grid">
          <div class="luxcraft-col-4">
            <div class="form-group">
              <label>Salary Month</label>
              <input type="month" name="salary_month" class="form-control" value="<?php echo date('Y-m'); ?>" required>
            </div>
          </div>

          <div class="luxcraft-col-4">
            <?php echo render_date_input('payment_date','Payment Date', date('Y-m-d')); ?>
          </div>

          <div class="luxcraft-col-12">
            <label>Employees</label>
            <div class="luxcraft-table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th style="width:40px;"><input type="checkbox" onclick="$('input[name*=staff_ids]').prop('checked', this.checked)"></th>
                    <th>Employee</th>
                    <th>Email</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($staff as $s){ ?>
                    <tr>
                      <td><input type="checkbox" name="staff_ids[]" value="<?php echo $s['staffid']; ?>" checked></td>
                      <td><?php echo !empty($s['payroll_name']) ? $s['payroll_name'] : $s['firstname'].' '.$s['lastname']; ?></td>
                      <td><?php echo $s['email']; ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>

          <div class="luxcraft-col-12">
            <?php echo render_textarea('remarks','Remarks',''); ?>
          </div>
        </div>
      </div>

      <button class="btn btn-primary">
        <i class="fa fa-users"></i> Generate Selected Payslips
      </button>
      <a href="<?php echo admin_url('luxcraft_payslips'); ?>" class="btn btn-default">Cancel</a>

      <?php echo form_close(); ?>
    </div>
  </div>
</div>
<?php init_tail(); ?>
