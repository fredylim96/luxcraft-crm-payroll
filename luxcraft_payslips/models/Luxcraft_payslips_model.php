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

        return $this->db->insert_id();
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
}
