<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
// PDF output deliberately uses the exact CRM document markup and premium CSS.
// There is only one payslip design to maintain for screen, print, and download.
$stylesheet = file_get_contents(dirname(__DIR__, 2).'/assets/css/luxcraft_payslips.css');
$premium_styles = strstr($stylesheet, '/* Premium payslip statement */');

// TCPDF does not resolve CSS custom properties, so resolve their values without
// changing any selectors, dimensions, spacing, or document markup.
$premium_styles = strtr($premium_styles, [
    'var(--ps-teal)' => '#0f5b63',
    'var(--ps-navy)' => '#163b4e',
    'var(--ps-gold)' => '#c69a5b',
    'var(--ps-ink)' => '#1f2328',
    'var(--ps-muted)' => '#5d6470',
    'var(--ps-faint)' => '#8c919a',
    'var(--ps-line)' => '#deddd9',
]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <style><?php echo $premium_styles; ?></style>
</head>
<body>
  <?php $this->load->view('luxcraft_payslips/templates/payslip_template', [
      'payslip' => $payslip,
      'is_pdf' => true,
  ]); ?>
</body>
</html>
