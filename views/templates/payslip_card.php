<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('luxcraft_payslips/templates/payslip_template', ['payslip' => $payslip, 'is_pdf' => false]); ?>
