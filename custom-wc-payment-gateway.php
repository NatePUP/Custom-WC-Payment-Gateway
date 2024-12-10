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

// Initialize the payment gateway on 'plugins_loaded' action with priority 11
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

    // Add the gateway to WooCommerce (with block-based support)
    add_filter('woocommerce_payment_gateways', function ($gateways) {
        $gateways[] = 'WC_Custom_Gateway';
        return $gateways;
    });

    // Ensure compatibility with block-based checkout
    add_filter('woocommerce_blocks_checkout_payment_gateways', function ($gateways) {
        $gateways[] = 'WC_Custom_Gateway';
        return $gateways;
    });
}


/**
 * Display an admin notice if WooCommerce is not active
 */
function custom_wc_payment_gateway_missing_wc_notice()
{
    echo '<div class="error"><p>' . esc_html__('WooCommerce must be active for the Custom WC Payment Gateway plugin to work.', 'custom-wc-payment-gateway') . '</p></div>';
}

/**
 * Add the Custom Gateway to WooCommerce payment methods
 *
 * @param array $gateways Existing gateways.
 * @return array Updated gateways.
 */
function custom_wc_add_gateway_class($gateways)
{
    $gateways[] = 'WC_Custom_Gateway';
    return $gateways;
}


function custom_gateway_debug_log($message)
{
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log($message);
    }
}

function custom_gateway_init()
{
    custom_gateway_debug_log('Custom Gateway Init triggered.');
    if (class_exists('WC_Payment_Gateway')) {
        require_once plugin_dir_path(__FILE__) . 'includes/class-wc-custom-gateway.php';
    } else {
        custom_gateway_debug_log('WC_Payment_Gateway class not found.');
    }
}
add_action('plugins_loaded', 'custom_gateway_init');
