<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
// The PDF and CRM intentionally render the same markup and stylesheet. Keeping a
// single document template prevents the two payslip formats from drifting apart.
$stylesheet = file_get_contents(dirname(__DIR__, 2).'/assets/css/luxcraft_payslips.css');
$premium_styles = strstr($stylesheet, '/* Premium payslip statement */');
$premium_styles = strtr($premium_styles, [
    'var(--ps-teal)' => '#0f5b63', 'var(--ps-navy)' => '#163b4e',
    'var(--ps-gold)' => '#c69a5b', 'var(--ps-ink)' => '#1f2328',
    'var(--ps-muted)' => '#5d6470', 'var(--ps-faint)' => '#8c919a',
    'var(--ps-line)' => '#deddd9',
]);
?>
<html>
<head>
  <meta charset="UTF-8">
  <style><?php echo $premium_styles; ?></style>
</head>
<body class="luxcraft-payslip-pdf-body">
  <?php $this->load->view('luxcraft_payslips/templates/payslip_template', ['payslip' => $payslip, 'is_pdf' => true]); ?>
</body>
</html>
