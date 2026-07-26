<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: LuxCraft Payroll
Description: Staff Payroll tab, payslip generation, CPF rounding, and PDF downloads for Perfex CRM.
Version: 50.1.0
Requires at least: 2.9.*
*/

define('LUXCRAFT_PAYSLIPS_MODULE_NAME', 'luxcraft_payslips');

hooks()->add_action('admin_init', 'luxcraft_payslips_permissions');
hooks()->add_action('admin_init', 'luxcraft_payslips_admin_menu');
hooks()->add_action('admin_init', 'luxcraft_payslips_staff_payroll_tab');
hooks()->add_action('app_admin_head', 'luxcraft_payslips_head_assets');
register_activation_hook(LUXCRAFT_PAYSLIPS_MODULE_NAME, 'luxcraft_payslips_activation_hook');

function luxcraft_payslips_activation_hook()
{
    require_once(__DIR__ . '/install/install.php');
}


function luxcraft_payslips_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'view_own'        => 'View Own Payslips',
        'view_all'        => 'View All Payslips',
        'create'          => 'Generate Payslips',
        'edit'            => 'Edit Payslips',
        'delete'          => 'Delete Payslips',
        'mark_paid'       => 'Mark Paid / Unpaid',
        'bulk_generate'   => 'Bulk Generate Payslips',
        'manage_profiles' => 'Manage Payroll Profiles',
    ];

    register_staff_capabilities('luxcraft_payslips', $capabilities, 'LuxCraft Payroll');
}

function luxcraft_payslips_admin_menu()
{
    $CI = &get_instance();

    $can_view_all        = is_admin() || has_permission('luxcraft_payslips', '', 'view_all');
    $can_view_own        = is_admin() || has_permission('luxcraft_payslips', '', 'view_own');
    $can_create          = is_admin() || has_permission('luxcraft_payslips', '', 'create');
    $can_bulk_generate   = is_admin() || has_permission('luxcraft_payslips', '', 'bulk_generate');
    $can_manage_profiles = is_admin() || has_permission('luxcraft_payslips', '', 'manage_profiles');

    // Do not show the Payroll parent menu unless at least one permitted child is visible.
    if (!$can_view_all && !$can_view_own && !$can_create && !$can_bulk_generate && !$can_manage_profiles) {
        return;
    }

    $CI->app_menu->add_sidebar_menu_item('luxcraft-payroll', [
        'name'     => 'Payroll',
        'href'     => '#',
        'position' => 35,
        'icon'     => 'fa fa-file-invoice-dollar',
    ]);

    if ($can_view_all) {
        $CI->app_menu->add_sidebar_children_item('luxcraft-payroll', [
            'slug'     => 'luxcraft-payroll-payslips',
            'name'     => 'Payslips',
            'href'     => admin_url('luxcraft_payslips'),
            'position' => 1,
            'icon'     => 'fa fa-list',
        ]);
    }

    if ($can_create) {
        $CI->app_menu->add_sidebar_children_item('luxcraft-payroll', [
            'slug'     => 'luxcraft-payroll-generate',
            'name'     => 'Generate Payslip',
            'href'     => admin_url('luxcraft_payslips/create'),
            'position' => 2,
            'icon'     => 'fa fa-plus-circle',
        ]);
    }

    if ($can_bulk_generate) {
        $CI->app_menu->add_sidebar_children_item('luxcraft-payroll', [
            'slug'     => 'luxcraft-payroll-bulk-generate',
            'name'     => 'Bulk Generate',
            'href'     => admin_url('luxcraft_payslips/bulk_create'),
            'position' => 3,
            'icon'     => 'fa fa-users',
        ]);
    }

    if ($can_manage_profiles) {
        $CI->app_menu->add_sidebar_children_item('luxcraft-payroll', [
            'slug'     => 'luxcraft-payroll-profiles',
            'name'     => 'Payroll Profiles',
            'href'     => admin_url('luxcraft_payslips/employees'),
            'position' => 4,
            'icon'     => 'fa fa-users-cog',
        ]);
    }

    if ($can_view_own) {
        $CI->app_menu->add_sidebar_children_item('luxcraft-payroll', [
            'slug'     => 'luxcraft-payroll-my-payslips',
            'name'     => 'My Payslips',
            'href'     => admin_url('luxcraft_payslips/my_payslips'),
            'position' => 5,
            'icon'     => 'fa fa-file-alt',
        ]);
    }
}

/**
 * Adds a Payroll tab inside Staff Profile when Perfex exposes the profile tab hook.
 * If your Perfex version uses a different hook name, the fallback Payroll Profiles page still works.
 */
function luxcraft_payslips_staff_payroll_tab()
{
    hooks()->add_action('after_staff_profile_tabs', 'luxcraft_payslips_staff_tab_link');
    hooks()->add_action('after_staff_profile_tabs_content', 'luxcraft_payslips_staff_tab_content');
}

function luxcraft_payslips_staff_tab_link($staff)
{
    echo '<li role="presentation"><a href="#luxcraft_payroll_tab" aria-controls="luxcraft_payroll_tab" role="tab" data-toggle="tab"><i class="fa fa-money-check-alt"></i> Payroll</a></li>';
}

function luxcraft_payslips_staff_tab_content($staff)
{
    $CI = &get_instance();
    $CI->load->model('luxcraft_payslips/luxcraft_payslips_model');
    $CI->load->helper('luxcraft_payslips/luxcraft_payslips');
    $profile = $CI->luxcraft_payslips_model->get_profile($staff->staffid ?? $staff['staffid']);
    $staff_id = $staff->staffid ?? $staff['staffid'];
    echo '<div role="tabpanel" class="tab-pane" id="luxcraft_payroll_tab">';
    $CI->load->view('luxcraft_payslips/admin/staff_payroll_tab', [
        'profile' => $profile,
        'staff_id' => $staff_id,
        'payment_methods' => luxcraft_payment_methods(),
        'employment_types' => luxcraft_employment_types(),
    ]);
    echo '</div>';
}

function luxcraft_payslips_head_assets()
{
    echo '<link href="' . module_dir_url(LUXCRAFT_PAYSLIPS_MODULE_NAME, 'assets/css/luxcraft_payslips.css') . '" rel="stylesheet" type="text/css" />';
    echo '<style>#side-menu a i.fa,#side-menu a i.fab,#side-menu a i.far,#side-menu a i.fas{margin-right:8px;}</style>';
}
