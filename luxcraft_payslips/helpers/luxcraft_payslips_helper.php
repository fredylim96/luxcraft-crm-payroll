<?php
defined('BASEPATH') or exit('No direct script access allowed');

function luxcraft_payment_methods()
{
    return ['PayNow', 'Bank Transfer', 'Cheque', 'CASH'];
}

function luxcraft_employment_types()
{
    return [
        'singapore_citizen' => 'Singapore Citizen',
        'spr_year_1'        => 'SPR Year 1',
        'spr_year_2'        => 'SPR Year 2',
        'spr_year_3'        => 'SPR Year 3+',
        'work_permit'       => 'Work Permit',
        's_pass'            => 'S Pass',
        'foreigner'         => 'Foreigner / No CPF',
    ];
}

/**
 * CPF rounding rules:
 * 1. Total CPF Contribution: Round to nearest dollar.
 *    - drop amounts below $0.50
 *    - round up for $0.50 and above
 * 2. Employee Share: Round down to nearest dollar.
 * 3. Employer Share: Total Contribution - Employee Share.
 *
 * This mirrors the user's CPF calculator rounding requirements.
 */
function luxcraft_round_cpf_total($amount)
{
    return floor((float)$amount + 0.5);
}

function luxcraft_round_cpf_employee($amount)
{
    return floor((float)$amount);
}

function luxcraft_get_cpf_rates_by_type($employment_type)
{
    switch ($employment_type) {
        case 'work_permit':
        case 's_pass':
        case 'foreigner':
            return ['employee' => 0.00, 'employer' => 0.00, 'cpf_applicable' => false];

        case 'spr_year_1':
            // Placeholder default. Configure if needed.
            return ['employee' => 0.05, 'employer' => 0.04, 'cpf_applicable' => true];

        case 'spr_year_2':
            // Placeholder default. Configure if needed.
            return ['employee' => 0.15, 'employer' => 0.09, 'cpf_applicable' => true];

        case 'spr_year_3':
        case 'singapore_citizen':
        default:
            return ['employee' => 0.20, 'employer' => 0.17, 'cpf_applicable' => true];
    }
}

function luxcraft_calculate_cpf($ordinary_wages, $employment_type = 'singapore_citizen')
{
    $ordinary_wages = (float) $ordinary_wages;
    $rates = luxcraft_get_cpf_rates_by_type($employment_type);

    if (!$rates['cpf_applicable'] || $ordinary_wages <= 0) {
        return ['employee' => 0.00, 'employer' => 0.00, 'total' => 0.00];
    }

    $raw_total = $ordinary_wages * ($rates['employee'] + $rates['employer']);
    $raw_employee = $ordinary_wages * $rates['employee'];

    $total = luxcraft_round_cpf_total($raw_total);
    $employee = luxcraft_round_cpf_employee($raw_employee);
    $employer = $total - $employee;

    return [
        'employee' => (float) $employee,
        'employer' => (float) $employer,
        'total'    => (float) $total,
    ];
}

function luxcraft_take_home($base, $commission, $allowances, $deductions, $cpf_employee)
{
    return round(((float)$base + (float)$commission + (float)$allowances) - (float)$deductions - (float)$cpf_employee, 2);
}


function luxcraft_format_salary_month($salary_month)
{
    if (!$salary_month || $salary_month === '0000-00-00' || $salary_month === '0000-00') {
        return '-';
    }

    if (preg_match('/^\d{4}-\d{2}$/', $salary_month)) {
        return date('F Y', strtotime($salary_month . '-01'));
    }

    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $salary_month)) {
        return date('F Y', strtotime($salary_month));
    }

    return $salary_month;
}

function luxcraft_company_logo_url()
{
    $logo = get_option('company_logo');
    if (!$logo) {
        return '';
    }

    return base_url('uploads/company/' . $logo);
}

function luxcraft_company_favicon_url()
{
    $favicon = get_option('favicon');
    if (!$favicon) {
        return '';
    }

    return base_url('uploads/company/' . $favicon);
}


function luxcraft_pdf_money($amount)
{
    return '$' . number_format((float)$amount, 2);
}
