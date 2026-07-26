<?php defined('BASEPATH') or exit('No direct script access allowed'); init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="luxcraft-card">
      <div class="luxcraft-list-header">
        <h4>All Payslips</h4>

        <div class="luxcraft-top-buttons">
          <?php if(is_admin() || has_permission('luxcraft_payslips', '', 'create')){ ?>
            <a href="<?php echo admin_url('luxcraft_payslips/create'); ?>" class="btn btn-primary">
              <i class="fa fa-plus-circle"></i> Generate Payslip
            </a>
          <?php } ?>

          <?php if(is_admin() || has_permission('luxcraft_payslips', '', 'bulk_generate')){ ?>
            <a href="<?php echo admin_url('luxcraft_payslips/bulk_create'); ?>" class="btn btn-default">
              <i class="fa fa-users"></i> Bulk Generate
            </a>
          <?php } ?>

          <?php if(is_admin() || has_permission('luxcraft_payslips', '', 'mark_paid')){ ?>
            <button type="submit" form="bulk-payslip-form" formaction="<?php echo admin_url('luxcraft_payslips/bulk_mark_paid'); ?>" class="btn btn-success">
              <i class="fa fa-lock"></i> Mark Selected as Paid
            </button>

            <button type="submit" form="bulk-payslip-form" formaction="<?php echo admin_url('luxcraft_payslips/bulk_mark_unpaid'); ?>" class="btn btn-warning">
              <i class="fa fa-unlock"></i> Mark Selected as Unpaid
            </button>
          <?php } ?>
        </div>
      </div>

      <hr>

      <?php echo form_open(admin_url('luxcraft_payslips/bulk_mark_paid'), ['id'=>'bulk-payslip-form']); ?>
        <div class="luxcraft-table-responsive">
          <table class="table dt-table">
            <thead>
              <tr>
                <th><input type="checkbox" onclick="$('input[name*=ids]').prop('checked', this.checked)"></th>
                <th>Employee</th>
                <th>Month</th>
                <th>Payment Date</th>
                <th>Status</th>
                <th>Options</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($payslips as $p){ ?>
                <tr>
                  <td><input type="checkbox" name="ids[]" value="<?php echo $p['id']; ?>"></td>
                  <td><?php echo !empty($p['payroll_name']) ? $p['payroll_name'] : $p['firstname'].' '.$p['lastname']; ?></td>
                  <td><?php echo luxcraft_format_salary_month($p['salary_month']); ?></td>
                  <td><?php echo _d($p['payment_date']); ?></td>
                  <td>
                    <span class="label label-<?php echo $p['status']=='paid'?'success':'default'; ?>">
                      <?php echo ucfirst($p['status']); ?>
                    </span>
                  </td>
                  <td>
                    <a href="<?php echo admin_url('luxcraft_payslips/view/'.$p['id']); ?>" class="btn btn-default btn-sm">
                      <i class="fa fa-eye"></i>
                    </a>

                    <?php if($p['status']!='paid'){ ?>
                      <?php if(is_admin() || has_permission('luxcraft_payslips', '', 'edit')){ ?>
                        <a href="<?php echo admin_url('luxcraft_payslips/create/'.$p['id']); ?>" class="btn btn-default btn-sm">
                          <i class="fa fa-edit"></i>
                        </a>
                      <?php } ?>

                      <?php if(is_admin() || has_permission('luxcraft_payslips', '', 'delete')){ ?>
                        <a href="<?php echo admin_url('luxcraft_payslips/delete/'.$p['id']); ?>" class="btn btn-danger btn-sm _delete">
                          <i class="fa fa-trash"></i>
                        </a>
                      <?php } ?>
                    <?php } else { ?>
                      <?php if(is_admin() || has_permission('luxcraft_payslips', '', 'mark_paid')){ ?>
                        <a href="<?php echo admin_url('luxcraft_payslips/mark_unpaid/'.$p['id']); ?>" class="btn btn-warning btn-sm">
                          <i class="fa fa-unlock"></i>
                        </a>
                      <?php } ?>
                    <?php } ?>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>
<?php init_tail(); ?>
