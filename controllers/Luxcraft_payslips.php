<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Luxcraft_payslips extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        // Hotfix: ensure new/changed DB columns exist after module replacement upgrades.
        // Perfex activation hooks do not always re-run when a module folder is replaced.
        require_once(dirname(__DIR__) . '/install/install.php');

        $this->load->model('luxcraft_payslips/luxcraft_payslips_model');
        $this->load->helper('luxcraft_payslips/luxcraft_payslips');
    }

    public function index()
    {
        if (!$this->can_view_all_payslips()) {
            access_denied('Payslips');
        }

        $data['title'] = 'Payslips';
        $data['payslips'] = $this->luxcraft_payslips_model->get_payslips();
        $this->load->view('luxcraft_payslips/admin/list', $data);
    }

    public function my_payslips()
    {
        if (!$this->can_view_own_payslips()) {
            access_denied('My Payslips');
        }

        $data['title'] = 'My Payslips';
        $data['payslips'] = $this->luxcraft_payslips_model->get_payslips(['staff_id' => get_staff_user_id(), 'status' => 'paid']);
        $this->load->view('luxcraft_payslips/staff/my_payslips', $data);
    }

    public function employees()
    {
        if (!$this->can_manage_profiles()) access_denied('Payroll Profiles');

        $edit_id = $this->input->get('edit');
        $data['edit_profile'] = $edit_id ? $this->luxcraft_payslips_model->get_profile_by_id($edit_id) : null;

        if ($this->input->post()) {
            $this->save_profile_from_post();
            redirect(admin_url('luxcraft_payslips/employees'));
        }

        $data['title'] = 'Payroll Profiles';
        $data['profiles'] = $this->luxcraft_payslips_model->get_profiles();
        $data['staff'] = $this->staff_model->get('', ['active' => 1]);
        $data['payment_methods'] = luxcraft_payment_methods();
        $data['employment_types'] = luxcraft_employment_types();
        $this->load->view('luxcraft_payslips/admin/employees', $data);
    }

    public function save_staff_payroll_profile()
    {
        if (!$this->can_manage_profiles()) access_denied('Payroll Profile');
        $this->save_profile_from_post();
        set_alert('success', 'Payroll profile saved.');
        redirect($_SERVER['HTTP_REFERER']);
    }

    private function save_profile_from_post()
    {
        $post = $this->input->post();

        $employment_type = $post['employment_type'];
        $rates = luxcraft_get_cpf_rates_by_type($employment_type);

        $payload = [
            'staff_id' => $post['staff_id'],
            'payroll_name' => isset($post['payroll_name']) ? $post['payroll_name'] : '',
            'job_title' => $post['job_title'],
            'monthly_salary' => $post['monthly_salary'],
            'cpf_applicable' => $rates['cpf_applicable'] ? 1 : 0,
            'employment_type' => $employment_type,
            'cpf_employee_rate' => $rates['employee'],
            'cpf_employer_rate' => $rates['employer'],
            'payment_method' => $post['payment_method'],
            'payment_details' => $post['payment_details'],
            'commission_enabled' => isset($post['commission_enabled']) ? 1 : 0,
            'commission_rate' => isset($post['commission_rate']) ? $post['commission_rate'] : 0,
        ];

        $this->luxcraft_payslips_model->save_profile($payload, !empty($post['profile_id']) ? $post['profile_id'] : null);
        set_alert('success', 'Payroll profile saved.');
    }

    public function delete_profile($id)
    {
        if (!$this->can_manage_profiles()) access_denied('Payroll Profiles');
        $this->luxcraft_payslips_model->delete_profile($id);
        set_alert('success', 'Payroll profile deleted.');
        redirect(admin_url('luxcraft_payslips/employees'));
    }


    public function bulk_create()
    {
        if (!$this->can_bulk_generate()) {
            access_denied('Bulk Generate Payslips');
        }

        $data['title'] = 'Bulk Generate Payslips';
        $data['staff'] = $this->luxcraft_payslips_model->get_staff_with_profiles();

        if ($this->input->post()) {
            $post = $this->input->post();
            $staff_ids = isset($post['staff_ids']) ? $post['staff_ids'] : [];

            if (empty($staff_ids)) {
                set_alert('warning', 'Please select at least one employee.');
                redirect(admin_url('luxcraft_payslips/bulk_create'));
            }

            $created = 0;

            foreach ($staff_ids as $staff_id) {
                $profile = $this->luxcraft_payslips_model->get_profile($staff_id);
                if (!$profile) {
                    continue;
                }

                $base_salary = (float)$profile['monthly_salary'];
                $commission = 0;
                $allowances = 0;
                $deductions = 0;

                $cpf = luxcraft_calculate_cpf($base_salary + $commission + $allowances, $profile['employment_type']);
                $take_home = luxcraft_take_home($base_salary, $commission, $allowances, $deductions, $cpf['employee']);

                $payload = [
                    'staff_id' => $staff_id,
                    'salary_month' => $this->normalize_salary_month($post['salary_month']),
                    'payment_date' => to_sql_date($post['payment_date']),
                    'job_title' => $profile['job_title'],
                    'payment_method' => $profile['payment_method'],
                    'payment_details' => $profile['payment_details'],
                    'base_salary' => $base_salary,
                    'commission' => $commission,
                    'allowances' => $allowances,
                    'deductions' => $deductions,
                    'cpf_employee' => $cpf['employee'],
                    'cpf_employer' => $cpf['employer'],
                    'cpf_total' => $cpf['total'],
                    'take_home_pay' => $take_home,
                    'remarks' => isset($post['remarks']) ? $post['remarks'] : '',
                ];

                $new_id = $this->luxcraft_payslips_model->create($payload);
                if ($new_id) {
                    $created++;
                }
            }

            set_alert('success', $created . ' payslip(s) generated.');
            redirect(admin_url('luxcraft_payslips'));
        }

        $this->load->view('luxcraft_payslips/admin/bulk_form', $data);
    }

    public function create($id = '')
    {
        if ($id && !$this->can_edit_payslips()) {
            access_denied('Edit Payslip');
        }
        if (!$id && !$this->can_create_payslips()) {
            access_denied('Generate Payslip');
        }

        $data['title'] = $id ? 'Edit Payslip' : 'Generate Payslip';
        $data['staff'] = $this->luxcraft_payslips_model->get_staff_with_profiles();

        if ($id) {
            $data['payslip'] = $this->luxcraft_payslips_model->get($id);
            if (!$data['payslip']) show_404();
        }

        if ($this->input->post()) {
            $post = $this->input->post();
            $profile = $this->luxcraft_payslips_model->get_profile($post['staff_id']);

            if (!$profile) {
                $this->payroll_error('No Payroll Profile found for this employee.', ['staff_id' => $post['staff_id']]);
            }

            $base_salary = (float)$profile['monthly_salary'];
            $commission = isset($post['commission']) ? (float)$post['commission'] : 0;
            $allowances = isset($post['allowances']) ? (float)$post['allowances'] : 0;
            $deductions = isset($post['deductions']) ? (float)$post['deductions'] : 0;

            $cpf_base = $base_salary + $commission + $allowances;
            $cpf = luxcraft_calculate_cpf($cpf_base, $profile['employment_type']);
            $take_home = luxcraft_take_home($base_salary, $commission, $allowances, $deductions, $cpf['employee']);

            $payload = [
                'staff_id' => $post['staff_id'],
                'salary_month' => $this->normalize_salary_month($post['salary_month']),
                'payment_date' => to_sql_date($post['payment_date']),
                'job_title' => $profile['job_title'],
                'payment_method' => $profile['payment_method'],
                'payment_details' => $profile['payment_details'],
                'base_salary' => $base_salary,
                'commission' => $commission,
                'allowances' => $allowances,
                'deductions' => $deductions,
                'cpf_employee' => $cpf['employee'],
                'cpf_employer' => $cpf['employer'],
                'cpf_total' => $cpf['total'],
                'take_home_pay' => $take_home,
                'remarks' => isset($post['remarks']) ? $post['remarks'] : '',
            ];

            if ($id) {
                $ok = $this->luxcraft_payslips_model->update($id, $payload);
                set_alert($ok ? 'success' : 'warning', $ok ? 'Payslip updated.' : 'Paid payslips cannot be edited.');
                redirect(admin_url('luxcraft_payslips'));
            }

            $new_id = $this->luxcraft_payslips_model->create($payload);
            if (!$new_id) {
                $this->payroll_error('Database insert failed while creating payslip.', [
                    'payload' => $payload,
                    'db_error' => $this->db->error(),
                ]);
            }
            set_alert('success', 'Payslip generated.');
            redirect(admin_url('luxcraft_payslips/view/'.$new_id));
        }

        $this->load->view('luxcraft_payslips/admin/form', $data);
    }

    public function calculate_json()
    {
        $post = $this->input->post();
        $profile = !empty($post['staff_id']) ? $this->luxcraft_payslips_model->get_profile($post['staff_id']) : null;

        if (!$profile) {
            echo json_encode(['profile_found' => false]);
            exit;
        }

        $base_salary = (float)$profile['monthly_salary'];
        $commission = isset($post['commission']) ? (float)$post['commission'] : 0;
        $allowances = isset($post['allowances']) ? (float)$post['allowances'] : 0;
        $deductions = isset($post['deductions']) ? (float)$post['deductions'] : 0;

        $cpf = luxcraft_calculate_cpf($base_salary + $commission + $allowances, $profile['employment_type']);
        $take_home = luxcraft_take_home($base_salary, $commission, $allowances, $deductions, $cpf['employee']);

        header('Content-Type: application/json');
        echo json_encode([
            'profile_found' => true,
            'payroll_name' => !empty($profile['payroll_name']) ? $profile['payroll_name'] : '',
            'job_title' => $profile['job_title'],
            'base_salary' => $base_salary,
            'payment_method' => $profile['payment_method'],
            'payment_details' => $profile['payment_details'],
            'employment_type' => $profile['employment_type'],
            'cpf_employee' => $cpf['employee'],
            'cpf_employer' => $cpf['employer'],
            'cpf_total' => $cpf['total'],
            'take_home_pay' => $take_home,
            'commission_enabled' => (int)$profile['commission_enabled'],
            'commission_rate' => $profile['commission_rate'],
        ]);
        exit;
    }

    public function view($id)
    {
        $data['payslip'] = $this->luxcraft_payslips_model->get($id);
        if (!$data['payslip']) show_404();
        if ($data['payslip']['staff_id'] == get_staff_user_id()) {
            if (!$this->can_view_own_payslips()) {
                access_denied('Payslip');
            }
            if (!$this->can_view_all_payslips() && $data['payslip']['status'] != 'paid') {
                access_denied('Payslip not available until paid');
            }
        } else {
            if (!$this->can_view_all_payslips()) {
                access_denied('Payslip');
            }
        }
        $data['is_employee_view'] = (!is_admin() && $data['payslip']['staff_id'] == get_staff_user_id());
        $this->load->view('luxcraft_payslips/admin/view', $data);
    }

    public function mark_paid($id)
    {
        if (!$this->can_mark_payslips()) access_denied('Payslip');
        $this->luxcraft_payslips_model->mark_paid($id);
        set_alert('success', 'Payslip marked as paid and locked.');
        redirect(admin_url('luxcraft_payslips/view/'.$id));
    }

    public function mark_unpaid($id)
    {
        if (!$this->can_mark_payslips()) access_denied('Payslip');
        $this->luxcraft_payslips_model->mark_unpaid($id);
        set_alert('success', 'Payslip marked as unpaid.');
        redirect(admin_url('luxcraft_payslips/view/'.$id));
    }

    public function delete($id)
    {
        if (!$this->can_delete_payslips()) access_denied('Payslip');
        $ok = $this->luxcraft_payslips_model->delete($id);
        set_alert($ok ? 'success' : 'warning', $ok ? 'Payslip deleted.' : 'Paid payslips cannot be deleted.');
        redirect(admin_url('luxcraft_payslips'));
    }


    public function bulk_mark_paid()
    {
        if (!$this->can_mark_payslips()) {
            access_denied('Payslip');
        }

        $ids = $this->input->post('ids');

        if (!$ids) {
            set_alert('warning', 'Please select at least one payslip.');
            redirect(admin_url('luxcraft_payslips'));
        }

        foreach ($ids as $id) {
            $this->luxcraft_payslips_model->mark_paid($id);
        }

        set_alert('success', 'Selected payslips marked as paid and locked.');
        redirect(admin_url('luxcraft_payslips'));
    }

    public function bulk_mark_unpaid()
    {
        if (!$this->can_mark_payslips()) {
            access_denied('Payslip');
        }

        $ids = $this->input->post('ids');

        if (!$ids) {
            set_alert('warning', 'Please select at least one payslip.');
            redirect(admin_url('luxcraft_payslips'));
        }

        foreach ($ids as $id) {
            $this->luxcraft_payslips_model->mark_unpaid($id);
        }

        set_alert('success', 'Selected payslips marked as unpaid.');
        redirect(admin_url('luxcraft_payslips'));
    }

    public function download($id)
    {
        show_404();
    }

    public function bulk_download()
    {
        if (!is_admin()) access_denied('Payslip');

        $ids = $this->input->post('ids');
        if (!$ids) {
            set_alert('warning', 'Please select at least one payslip.');
            redirect(admin_url('luxcraft_payslips'));
        }

        $zip_path = sys_get_temp_dir() . '/payslips_' . time() . '.zip';
        $zip = new ZipArchive();
        $zip->open($zip_path, ZipArchive::CREATE);

        foreach ($ids as $id) {
            $payslip = $this->luxcraft_payslips_model->get($id);
            if (!$payslip) continue;
            $html = $this->load->view('luxcraft_payslips/templates/pdf', ['payslip' => $payslip], true);
            $tmp = sys_get_temp_dir() . '/payslip_' . $id . '.html';
            file_put_contents($tmp, $html);
            $zip->addFile($tmp, 'Payslip_'.$payslip['salary_month'].'_'.$payslip['firstname'].'_'.$payslip['lastname'].'.html');
        }

        $zip->close();
        force_download($zip_path, null);
    }


    public function debug()
    {
        if (!is_admin()) {
            access_denied('Payroll Debug');
        }

        $profiles = db_prefix().'luxcraft_employee_profiles';
        $payslips = db_prefix().'luxcraft_payslips';

        $data = [
            'php_version' => PHP_VERSION,
            'profiles_table_exists' => $this->db->table_exists($profiles),
            'payslips_table_exists' => $this->db->table_exists($payslips),
            'profiles_fields' => $this->db->table_exists($profiles) ? $this->db->list_fields($profiles) : [],
            'payslips_fields' => $this->db->table_exists($payslips) ? $this->db->list_fields($payslips) : [],
            'salary_month_note' => 'salary_month should be CHAR(7) storing YYYY-MM, e.g. 2026-06',
            'last_db_error' => $this->db->error(),
        ];

        header('Content-Type: text/plain');
        echo print_r($data, true);
        exit;
    }



    private function make_pdf_document($title = 'Payslip')
    {
        if (!class_exists('TCPDF')) {
            $tcpdf_path = APPPATH . 'third_party/tcpdf/tcpdf.php';
            if (file_exists($tcpdf_path)) {
                require_once($tcpdf_path);
            }
        }

        if (!class_exists('TCPDF')) {
            show_error('TCPDF library not found. Please confirm Perfex TCPDF is installed under application/third_party/tcpdf/.', 500);
        }

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(get_option('companyname'));
        $pdf->SetAuthor(get_option('companyname'));
        $pdf->SetTitle($title);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        return $pdf;
    }

    private function normalize_salary_month($value)
    {
        $value = trim((string)$value);

        if (preg_match('/^\d{4}-\d{2}$/', $value)) {
            return $value;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return date('Y-m', strtotime($value));
        }

        $timestamp = strtotime($value);
        if ($timestamp) {
            return date('Y-m', $timestamp);
        }

        return date('Y-m');
    }


    private function can_view_all_payslips()
    {
        return is_admin() || has_permission('luxcraft_payslips', '', 'view_all');
    }

    private function can_view_own_payslips()
    {
        return is_admin() || has_permission('luxcraft_payslips', '', 'view_own');
    }

    private function can_create_payslips()
    {
        return is_admin() || has_permission('luxcraft_payslips', '', 'create');
    }

    private function can_edit_payslips()
    {
        return is_admin() || has_permission('luxcraft_payslips', '', 'edit');
    }

    private function can_delete_payslips()
    {
        return is_admin() || has_permission('luxcraft_payslips', '', 'delete');
    }

    private function can_mark_payslips()
    {
        return is_admin() || has_permission('luxcraft_payslips', '', 'mark_paid');
    }

    private function can_bulk_generate()
    {
        return is_admin() || has_permission('luxcraft_payslips', '', 'bulk_generate');
    }

    private function can_manage_profiles()
    {
        return is_admin() || has_permission('luxcraft_payslips', '', 'manage_profiles');
    }

    private function payroll_error($message, $context = [])
    {
        log_message('error', 'LuxCraft Payroll Error: ' . $message . ' | ' . json_encode($context));

        if (function_exists('set_alert')) {
            set_alert('danger', 'Payroll Error: ' . $message);
        }

        show_error('LuxCraft Payroll Error: ' . $message . '<br><br><pre>' . html_escape(print_r($context, true)) . '</pre>', 500);
    }

}
