<?php
/*
Plugin Name: LearnPress Midtrans Payment Gateway
Plugin URI: http://lkpnaura.com/
Description: Midtrans Payment Gateway integration for LearnPress.
Version: 1.0.0
Author: Nevh Devh
Author URI: http://lkpnaura.com/
*/
// Load Midtrans settings
require_once 'includes/settings.php';

// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

// Add Midtrans payment gateway to LearnPress
add_filter('learn_press_payment_method', 'register_midtrans_payment_gateway', 10, 1);

function register_midtrans_payment_gateway($methods)
{
    require_once 'includes/class-lp-gateway-midtrans.php';
    $methods['midtrans'] = 'LP_Gateway_Midtrans';
    return $methods;
}
