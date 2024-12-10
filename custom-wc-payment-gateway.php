<?php
/**
 * Plugin Name: Custom WC Payment Gateway
 * Description: A custom payment gateway for WooCommerce to simulate a payment process.
 * Version: 1.0
 * Author: Nate Panares
 * Author URI: https://natepanares.vercel.app/
 * Text Domain: custom-wc-payment-gateway
 * Requires Plugins: woocommerce
 */

defined('ABSPATH') || exit;

// Ensure WooCommerce is active
add_action('plugins_loaded', 'custom_wc_payment_gateway_init', 11);

function custom_wc_payment_gateway_init()
{
    if (!class_exists('WC_Payment_Gateway')) {
        // Show an admin notice if WooCommerce is not active
        add_action('admin_notices', function () {
            echo '<div class="error"><p>' . esc_html__('WooCommerce must be active for Custom WC Payment Gateway.', 'custom-wc-payment-gateway') . '</p></div>';
        });
        return;
    }

    // Include the payment gateway class
    require_once plugin_dir_path(__FILE__) . 'includes/class-wc-custom-gateway.php';

    // Add the gateway to WooCommerce
    add_filter('woocommerce_payment_gateways', function ($gateways) {
        $gateways[] = 'WC_Custom_Gateway';
        return $gateways;
    });
}
