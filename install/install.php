<?php
defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

$profiles = db_prefix() . 'luxcraft_employee_profiles';
$payslips = db_prefix() . 'luxcraft_payslips';

if (!$CI->db->table_exists($profiles)) {
    $CI->db->query("CREATE TABLE `{$profiles}` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `staff_id` int(11) NOT NULL,
        `payroll_name` varchar(191) DEFAULT NULL,
        `job_title` varchar(191) DEFAULT NULL,
        `monthly_salary` decimal(15,2) NOT NULL DEFAULT '0.00',
        `cpf_applicable` tinyint(1) NOT NULL DEFAULT '1',
        `employment_type` varchar(50) NOT NULL DEFAULT 'singapore_citizen',
        `cpf_employee_rate` decimal(6,4) NOT NULL DEFAULT '0.2000',
        `cpf_employer_rate` decimal(6,4) NOT NULL DEFAULT '0.1700',
        `payment_method` varchar(50) NOT NULL DEFAULT 'PayNow',
        `payment_details` varchar(255) DEFAULT NULL,
        `commission_enabled` tinyint(1) NOT NULL DEFAULT '0',
        `commission_rate` decimal(6,2) NOT NULL DEFAULT '0.00',
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `staff_id` (`staff_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";");
} else {
    $profileColumns = [
        'payroll_name'       => "ADD `payroll_name` varchar(191) DEFAULT NULL AFTER `staff_id`",
        'employment_type'    => "ADD `employment_type` varchar(50) NOT NULL DEFAULT 'singapore_citizen' AFTER `cpf_applicable`",
        'commission_rate'    => "ADD `commission_rate` decimal(6,2) NOT NULL DEFAULT '0.00' AFTER `commission_enabled`",
    ];

    foreach ($profileColumns as $field => $sql) {
        if (!$CI->db->field_exists($field, $profiles)) {
            $CI->db->query("ALTER TABLE `{$profiles}` {$sql}");
        }
    }

    if ($CI->db->field_exists('payment_details', $profiles)) {
        $CI->db->query("ALTER TABLE `{$profiles}` MODIFY `payment_details` varchar(255) DEFAULT NULL");
    }
}

if (!$CI->db->table_exists($payslips)) {
    $CI->db->query("CREATE TABLE `{$payslips}` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `staff_id` int(11) NOT NULL,
        `salary_month` char(7) NOT NULL,
        `payment_date` date NOT NULL,
        `job_title` varchar(191) DEFAULT NULL,
        `payment_method` varchar(50) DEFAULT NULL,
        `payment_details` varchar(255) DEFAULT NULL,
        `base_salary` decimal(15,2) NOT NULL DEFAULT '0.00',
        `commission` decimal(15,2) NOT NULL DEFAULT '0.00',
        `allowances` decimal(15,2) NOT NULL DEFAULT '0.00',
        `deductions` decimal(15,2) NOT NULL DEFAULT '0.00',
        `cpf_employee` decimal(15,2) NOT NULL DEFAULT '0.00',
        `cpf_employer` decimal(15,2) NOT NULL DEFAULT '0.00',
        `cpf_total` decimal(15,2) NOT NULL DEFAULT '0.00',
        `take_home_pay` decimal(15,2) NOT NULL DEFAULT '0.00',
        `remarks` text DEFAULT NULL,
        `status` varchar(20) NOT NULL DEFAULT 'draft',
        `created_by` int(11) DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL,
        `paid_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `staff_id` (`staff_id`),
        KEY `salary_month` (`salary_month`),
        KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";");
} else {
    /*
     * Compatibility migration:
     * Earlier builds used columns like gross_salary/net_pay/other_deductions.
     * Current code uses base_salary/take_home_pay/deductions/job_title.
     * Missing columns are added here safely.
     */
    $payslipColumns = [
        'job_title'      => "ADD `job_title` varchar(191) DEFAULT NULL AFTER `payment_date`",
        'base_salary'    => "ADD `base_salary` decimal(15,2) NOT NULL DEFAULT '0.00' AFTER `payment_details`",
        'allowances'     => "ADD `allowances` decimal(15,2) NOT NULL DEFAULT '0.00' AFTER `commission`",
        'deductions'     => "ADD `deductions` decimal(15,2) NOT NULL DEFAULT '0.00' AFTER `allowances`",
        'cpf_total'      => "ADD `cpf_total` decimal(15,2) NOT NULL DEFAULT '0.00' AFTER `cpf_employer`",
        'take_home_pay'  => "ADD `take_home_pay` decimal(15,2) NOT NULL DEFAULT '0.00' AFTER `cpf_total`",
    ];

    foreach ($payslipColumns as $field => $sql) {
        if (!$CI->db->field_exists($field, $payslips)) {
            $CI->db->query("ALTER TABLE `{$payslips}` {$sql}");
        }
    }

    if ($CI->db->field_exists('payment_details', $payslips)) {
        $CI->db->query("ALTER TABLE `{$payslips}` MODIFY `payment_details` varchar(255) DEFAULT NULL");
    }

    if ($CI->db->field_exists('salary_month', $payslips)) {
        // Older versions accidentally allowed DATE-like storage which can become 0000-00-00.
        // Current module stores YYYY-MM only.
        $CI->db->query("ALTER TABLE `{$payslips}` MODIFY `salary_month` char(7) NOT NULL");
    }


    // Backfill values from old column names when present.
    if ($CI->db->field_exists('gross_salary', $payslips) && $CI->db->field_exists('base_salary', $payslips)) {
        $CI->db->query("UPDATE `{$payslips}` SET `base_salary` = `gross_salary` WHERE (`base_salary` IS NULL OR `base_salary` = 0) AND `gross_salary` IS NOT NULL");
    }

    if ($CI->db->field_exists('net_pay', $payslips) && $CI->db->field_exists('take_home_pay', $payslips)) {
        $CI->db->query("UPDATE `{$payslips}` SET `take_home_pay` = `net_pay` WHERE (`take_home_pay` IS NULL OR `take_home_pay` = 0) AND `net_pay` IS NOT NULL");
    }

    if ($CI->db->field_exists('other_deductions', $payslips) && $CI->db->field_exists('deductions', $payslips)) {
        $CI->db->query("UPDATE `{$payslips}` SET `deductions` = `other_deductions` WHERE (`deductions` IS NULL OR `deductions` = 0) AND `other_deductions` IS NOT NULL");
    }
}
