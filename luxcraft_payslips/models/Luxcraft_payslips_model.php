<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Luxcraft_payslips_model extends App_Model
{
    public function get_staff_with_profiles()
    {
        $this->db->select('s.staffid, s.firstname, s.lastname, s.email, p.payroll_name');
        $this->db->from(db_prefix().'luxcraft_employee_profiles p');
        $this->db->join(db_prefix().'staff s', 's.staffid = p.staff_id', 'left');
        $this->db->where('s.active', 1);
        $this->db->order_by('s.firstname', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_profiles()
    {
        $this->db->select('p.*, s.firstname, s.lastname, s.email');
        $this->db->from(db_prefix().'luxcraft_employee_profiles p');
        $this->db->join(db_prefix().'staff s', 's.staffid = p.staff_id', 'left');
        $this->db->order_by('s.firstname', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_profile_by_id($id)
    {
        return $this->db->where('id', $id)->get(db_prefix().'luxcraft_employee_profiles')->row_array();
    }

    public function get_profile($staff_id)
    {
        return $this->db->where('staff_id', $staff_id)->get(db_prefix().'luxcraft_employee_profiles')->row_array();
    }

    public function save_profile($data, $profile_id = null)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        if ($profile_id) {
            $this->db->where('id', $profile_id)->update(db_prefix().'luxcraft_employee_profiles', $data);
            return $profile_id;
        }

        $exists = $this->get_profile($data['staff_id']);
        if ($exists) {
            $this->db->where('staff_id', $data['staff_id'])->update(db_prefix().'luxcraft_employee_profiles', $data);
            return $exists['id'];
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert(db_prefix().'luxcraft_employee_profiles', $data);
        return $this->db->insert_id();
    }

    public function delete_profile($id)
    {
        $this->db->where('id', $id)->delete(db_prefix().'luxcraft_employee_profiles');
        return true;
    }

    public function get_payslips($filters = [])
    {
        $this->db->select('p.*, s.firstname, s.lastname, s.email, ep.payroll_name');
        $this->db->from(db_prefix().'luxcraft_payslips p');
        $this->db->join(db_prefix().'staff s', 's.staffid = p.staff_id', 'left');
        $this->db->join(db_prefix().'luxcraft_employee_profiles ep', 'ep.staff_id = p.staff_id', 'left');

        if (!empty($filters['staff_id'])) {
            $this->db->where('p.staff_id', $filters['staff_id']);
        }

        if (!empty($filters['salary_month'])) {
            $this->db->where('p.salary_month', $filters['salary_month']);
        }

        if (!empty($filters['status'])) {
            $this->db->where('p.status', $filters['status']);
        }

        $this->db->order_by('p.salary_month', 'DESC');
        $this->db->order_by('p.id', 'DESC');

        return $this->db->get()->result_array();
    }

    public function get($id)
    {
        $this->db->select('p.*, s.firstname, s.lastname, s.email, ep.payroll_name');
        $this->db->from(db_prefix().'luxcraft_payslips p');
        $this->db->join(db_prefix().'staff s', 's.staffid = p.staff_id', 'left');
        $this->db->join(db_prefix().'luxcraft_employee_profiles ep', 'ep.staff_id = p.staff_id', 'left');
        $this->db->where('p.id', $id);
        return $this->db->get()->row_array();
    }

    public function create($data)
    {
        $data = $this->add_record_snapshots($data);
        $data['created_by'] = get_staff_user_id();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['status'] = 'draft';

        $ok = $this->db->insert(db_prefix().'luxcraft_payslips', $data);
        if (!$ok) {
            $error = $this->db->error();
            log_message('error', 'LuxCraft Payroll insert error: '.json_encode($error));
            return false;
        }

        $id = $this->db->insert_id();
        if (empty($data['payslip_no'])) {
            $month = preg_replace('/[^0-9]/', '', isset($data['salary_month']) ? $data['salary_month'] : date('Y-m'));
            $this->db->where('id', $id)->update(db_prefix().'luxcraft_payslips', [
                'payslip_no' => 'LC-PS-' . $month . '-' . str_pad((string)$id, 5, '0', STR_PAD_LEFT),
            ]);
        }

        return $id;
    }

    public function update($id, $data)
    {
        $existing = $this->get($id);
        if (!$existing || $existing['status'] === 'paid') {
            return false;
        }

        $data['updated_at'] = date('Y-m-d H:i:s');
        $ok = $this->db->where('id', $id)->update(db_prefix().'luxcraft_payslips', $data);

        if (!$ok) {
            $error = $this->db->error();
            log_message('error', 'LuxCraft Payroll update error: '.json_encode($error));
            return false;
        }

        return true;
    }

    public function delete($id)
    {
        $existing = $this->get($id);
        if (!$existing || $existing['status'] === 'paid') {
            return false;
        }

        $this->db->where('id', $id)->delete(db_prefix().'luxcraft_payslips');
        return true;
    }

    public function mark_paid($id)
    {
        $this->db->where('id', $id)->update(db_prefix().'luxcraft_payslips', [
            'status' => 'paid',
            'paid_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function mark_unpaid($id)
    {
        $this->db->where('id', $id)->update(db_prefix().'luxcraft_payslips', [
            'status' => 'draft',
            'paid_at' => null,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Freeze mutable issuer/staff values at creation time. Perfex has no core
     * NRIC/FIN field, so integrations may still explicitly provide nric_fin.
     */
    private function add_record_snapshots(array $data)
    {
        $staffId = isset($data['staff_id']) ? (int)$data['staff_id'] : 0;
        $staff = $staffId
            ? $this->db->where('staffid', $staffId)->get(db_prefix().'staff')->row_array()
            : [];

        $defaults = [
            'employee_id'          => $staffId ?: null,
            'date_joined'          => !empty($staff['datecreated']) ? substr($staff['datecreated'], 0, 10) : null,
            'currency'             => 'SGD',
            'company_name'         => get_option('companyname') ?: 'LuxCraft Pte. Ltd.',
            'company_uen'          => get_option('company_vat'),
            'company_address'      => trim(get_option('companyaddress') . ' ' . get_option('companycity') . ' ' . get_option('companyzip')),
            'company_cpf_reference'=> get_option('luxcraft_company_cpf_reference'),
            'company_bank'         => get_option('luxcraft_company_bank'),
            'prepared_by'          => function_exists('get_staff_full_name') ? get_staff_full_name(get_staff_user_id()) : '',
        ];

        foreach ($defaults as $field => $value) {
            if (!array_key_exists($field, $data) || $data[$field] === '' || $data[$field] === null) {
                $data[$field] = $value;
            }
        }

        return $data;
    }
}
