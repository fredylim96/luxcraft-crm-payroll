<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * TCPDF adapter for LuxCraft payslips.
 *
 * Keeping TCPDF construction here prevents controllers from depending on the
 * location of Perfex's bundled third-party library and gives future payroll
 * documents one stable rendering API.
 */
class Luxcraft_payslip_pdf
{
    /** @var CI_Controller */
    private $ci;

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->loadTcpdf();
    }

    /**
     * @param array  $data       Normalized payslip payload.
     * @param string $destination TCPDF destination: D, I, S or F.
     * @param string $filename
     * @return string|null PDF bytes for S, otherwise TCPDF's return value.
     */
    public function render(array $data, $destination = 'D', $filename = '')
    {
        $title = 'Payslip ' . $data['payslip_no'] . ' - ' . $data['employee_name'];
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('Perfex CRM');
        $pdf->SetAuthor($data['company_name']);
        $pdf->SetTitle($title);
        $pdf->SetSubject('Payroll record for ' . $data['payroll_month']);
        $pdf->SetKeywords('payslip, payroll, ' . $data['employee_id']);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(12, 12, 12);
        $pdf->SetAutoPageBreak(true, 12);
        $pdf->setImageScale(1.25);
        $pdf->AddPage('P', 'A4');

        $html = $this->ci->load->view(
            'luxcraft_payslips/templates/pdf',
            ['data' => $data],
            true
        );
        $pdf->writeHTML($html, true, false, true, false, '');

        if ($filename === '') {
            $filename = luxcraft_payslip_filename($data);
        }

        return $pdf->Output($filename, $destination);
    }

    private function loadTcpdf()
    {
        if (class_exists('TCPDF')) {
            return;
        }

        $paths = [
            APPPATH . 'third_party/tcpdf/tcpdf.php',
            APPPATH . 'libraries/pdf/Tcpdf.php',
        ];

        foreach ($paths as $path) {
            if (is_file($path)) {
                require_once $path;
                if (class_exists('TCPDF')) {
                    return;
                }
            }
        }

        show_error('TCPDF was not found. Restore the TCPDF package bundled with Perfex CRM.', 500);
    }
}
