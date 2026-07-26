<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Template contract example. Pass this array to Luxcraft_payslip_pdf::render().
return [
    'company_name' => 'LuxCraft Pte. Ltd.',
    'company_uen' => '202343751W',
    'company_address' => 'Singapore',
    'company_cpf_reference' => 'CPF-2026-07',
    'company_bank' => 'DBS Bank',
    'prepared_by' => 'Payroll Administrator',
    'company_logo' => '',
    'employee_name' => 'Jamie Tan',
    'employee_id' => 'LC-0017',
    'job_title' => 'Senior Designer',
    'department' => 'Design',
    'date_joined' => '2024-02-12',
    'nric_fin' => 'S****123A',
    'payslip_no' => 'LC-PS-202607-0017',
    'pay_date' => '2026-07-31',
    'payroll_month' => 'July 2026',
    'payment_mode' => 'Bank Transfer',
    'status' => 'Paid',
    'currency' => 'SGD',
    'earnings' => [
        ['description' => 'Base Salary', 'note' => 'Standard monthly remuneration', 'amount' => 6500.00],
        ['description' => 'Transport Allowance', 'note' => 'Fixed monthly allowance', 'amount' => 250.00],
    ],
    'deductions' => [
        ['description' => 'Employee CPF Contribution', 'note' => 'Statutory contribution', 'amount' => 1350.00],
    ],
    'gross_earnings' => 6750.00,
    'total_deductions' => 1350.00,
    'employer_cpf' => 1148.00,
    'employee_cpf' => 1350.00,
    'ytd_gross' => 47250.00,
    'ytd_cpf' => 17486.00,
    'net_pay' => 5400.00,
    'remarks' => 'Salary credited to the employee bank account.',
];
