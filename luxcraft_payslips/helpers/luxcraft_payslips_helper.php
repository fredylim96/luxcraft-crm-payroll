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

function luxcraft_pdf_escape($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function luxcraft_payslip_filename(array $data)
{
    $name = 'Payslip_' . $data['payroll_month'] . '_' . $data['employee_name'] . '_' . $data['payslip_no'] . '.pdf';
    return preg_replace('/[^A-Za-z0-9_.-]+/', '_', $name);
}

/**
 * Convert a database row into the versioned, template-facing PDF contract.
 * Values stored on the payslip take priority so historical documents remain
 * reproducible when company or staff settings later change.
 */
function luxcraft_payslip_pdf_data(array $payslip)
{
    $employeeName = !empty($payslip['payroll_name'])
        ? $payslip['payroll_name']
        : trim((isset($payslip['firstname']) ? $payslip['firstname'] : '') . ' ' . (isset($payslip['lastname']) ? $payslip['lastname'] : ''));
    $currency = !empty($payslip['currency']) ? $payslip['currency'] : 'SGD';

    $earnings = luxcraft_decode_lines(isset($payslip['earnings_json']) ? $payslip['earnings_json'] : null);
    if (!$earnings) {
        $earnings = [['description' => 'Base Salary', 'amount' => (float)$payslip['base_salary']]];
        if (!empty($payslip['commission'])) {
            $earnings[] = ['description' => 'Commission', 'amount' => (float)$payslip['commission']];
        }
        if (!empty($payslip['allowances'])) {
            $earnings[] = ['description' => 'Allowances', 'amount' => (float)$payslip['allowances']];
        }
    }

    $deductions = luxcraft_decode_lines(isset($payslip['deductions_json']) ? $payslip['deductions_json'] : null);
    if (!$deductions) {
        if (!empty($payslip['deductions'])) {
            $deductions[] = ['description' => 'Other Deductions', 'amount' => (float)$payslip['deductions']];
        }
        if (!empty($payslip['cpf_employee'])) {
            $deductions[] = ['description' => 'Employee CPF Contribution', 'amount' => (float)$payslip['cpf_employee']];
        }
    }

    $gross = isset($payslip['gross_earnings']) && $payslip['gross_earnings'] !== null
        ? (float)$payslip['gross_earnings']
        : luxcraft_sum_lines($earnings);
    $totalDeductions = isset($payslip['total_deductions']) && $payslip['total_deductions'] !== null
        ? (float)$payslip['total_deductions']
        : luxcraft_sum_lines($deductions);

    return [
        'company_name'          => luxcraft_value($payslip, 'company_name', get_option('companyname') ?: 'LuxCraft Pte. Ltd.'),
        'company_uen'           => luxcraft_value($payslip, 'company_uen', get_option('company_vat')),
        'company_address'       => luxcraft_value($payslip, 'company_address', trim(get_option('companyaddress') . ' ' . get_option('companycity') . ' ' . get_option('companyzip'))),
        'company_cpf_reference' => luxcraft_value($payslip, 'company_cpf_reference', ''),
        'company_bank'          => luxcraft_value($payslip, 'company_bank', ''),
        'prepared_by'           => luxcraft_value($payslip, 'prepared_by', ''),
        // Match the browser/print template, which uses the compact favicon mark.
        'company_logo'          => luxcraft_company_favicon_url() ?: luxcraft_company_logo_url(),
        'employee_name'         => $employeeName,
        'employee_id'           => luxcraft_value($payslip, 'employee_id', isset($payslip['staff_id']) ? $payslip['staff_id'] : ''),
        'job_title'             => luxcraft_value($payslip, 'job_title', ''),
        'department'            => luxcraft_value($payslip, 'department', ''),
        'date_joined'           => luxcraft_value($payslip, 'date_joined', ''),
        'nric_fin'              => luxcraft_value($payslip, 'nric_fin', ''),
        'payslip_no'            => luxcraft_value($payslip, 'payslip_no', 'LC-' . str_pad((string)$payslip['id'], 6, '0', STR_PAD_LEFT)),
        'pay_date'              => luxcraft_value($payslip, 'pay_date', isset($payslip['payment_date']) ? $payslip['payment_date'] : ''),
        'payroll_month'         => luxcraft_format_salary_month(isset($payslip['salary_month']) ? $payslip['salary_month'] : ''),
        'payment_mode'          => luxcraft_value($payslip, 'payment_mode', isset($payslip['payment_method']) ? $payslip['payment_method'] : ''),
        'payment_details'       => luxcraft_value($payslip, 'payment_details', ''),
        'status'                => ucfirst(luxcraft_value($payslip, 'status', 'draft')),
        'currency'              => $currency,
        'earnings'              => $earnings,
        'deductions'            => $deductions,
        'gross_earnings'        => $gross,
        'total_deductions'      => $totalDeductions,
        'employer_cpf'          => (float)luxcraft_value($payslip, 'employer_cpf', isset($payslip['cpf_employer']) ? $payslip['cpf_employer'] : 0),
        'employee_cpf'          => (float)luxcraft_value($payslip, 'employee_cpf', isset($payslip['cpf_employee']) ? $payslip['cpf_employee'] : 0),
        'total_cpf'             => (float)luxcraft_value($payslip, 'total_cpf', isset($payslip['cpf_total']) ? $payslip['cpf_total'] : 0),
        'ytd_gross'             => (float)luxcraft_value($payslip, 'ytd_gross', 0),
        'ytd_cpf'               => (float)luxcraft_value($payslip, 'ytd_cpf', 0),
        'net_pay'               => (float)luxcraft_value($payslip, 'net_pay', isset($payslip['take_home_pay']) ? $payslip['take_home_pay'] : ($gross - $totalDeductions)),
        'remarks'               => luxcraft_value($payslip, 'remarks', ''),
    ];
}

function luxcraft_value(array $source, $key, $fallback = '')
{
    return isset($source[$key]) && $source[$key] !== '' && $source[$key] !== null ? $source[$key] : $fallback;
}

function luxcraft_decode_lines($json)
{
    if (is_array($json)) {
        $decoded = $json;
    } else {
        $decoded = $json ? json_decode($json, true) : [];
    }
    if (!is_array($decoded)) {
        return [];
    }
    $lines = [];
    foreach ($decoded as $line) {
        if (is_array($line) && isset($line['description'], $line['amount'])) {
            $lines[] = [
                'description' => (string)$line['description'],
                'note'        => isset($line['note']) ? (string)$line['note'] : '',
                'amount'      => (float)$line['amount'],
            ];
        }
    }
    return $lines;
}

function luxcraft_sum_lines(array $lines)
{
    $total = 0.0;
    foreach ($lines as $line) {
        $total += isset($line['amount']) ? (float)$line['amount'] : 0;
    }
    return round($total, 2);
}
