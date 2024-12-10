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

/**
 * Initialize the custom payment gateway after WooCommerce is loaded.
 */
add_action('plugins_loaded', 'custom_wc_payment_gateway_init', 11);

function custom_wc_payment_gateway_init()
{
    if (!class_exists('WC_Payment_Gateway')) {
        // Display admin notice if WooCommerce is not active
        add_action('admin_notices', function () {
            echo '<div class="error"><p>' . esc_html__('WooCommerce must be active for Custom WC Payment Gateway.', 'custom-wc-payment-gateway') . '</p></div>';
        });
        return;
    }

    // Include the payment gateway class file
    require_once plugin_dir_path(__FILE__) . 'includes/class-wc-custom-gateway.php';

    // Add the custom payment gateway to WooCommerce
    add_filter('woocommerce_payment_gateways', function ($gateways) {
        $gateways[] = 'WC_Custom_Gateway'; // Adding the custom gateway class to the list of available gateways
        return $gateways;
    });
}

/**
 * Add custom payment gateway support for WooCommerce Blocks (Gutenberg).
 */
add_action('init', function () {
    if (class_exists('Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry')) {
        add_filter('woocommerce_blocks_checkout_payment_gateways', function ($gateways) {
            $gateways['custom_wc_payment_gateway'] = [
                'title' => __('Custom WC Payment Gateway', 'custom-wc-payment-gateway'),
                'description' => __('Mock payment gateway for testing.', 'custom-wc-payment-gateway'),
                'supports' => ['products'], // Specify supported features
            ];
            return $gateways;
        });
    }
});

/**
 * Register custom payment gateway for WooCommerce Blocks
 */
add_action('woocommerce_blocks_checkout_payment_method_register', function ($payment_method_registry) {
    $payment_method_registry->register(
        'custom_wc_payment_gateway',
        [
            'title' => __('Custom WC Payment Gateway', 'custom-wc-payment-gateway'),
            'description' => __('Mock payment gateway for testing.', 'custom-wc-payment-gateway'),
            'supports' => ['products'],
            'script_handles' => ['custom-payment-blocks'], // Link the JS file for the blocks
        ]
    );
});

/**
 * Debug utility for logging messages if WP_DEBUG is enabled.
 *
 * @param string $message The message to log.
 */
function custom_gateway_debug_log($message)
{
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log($message);
    }
}

/**
 * Handle plugin activation hooks and initialization.
 */
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

/**
 * Process custom payment via REST API.
 *
 * @param WP_REST_Request $request The request object.
 */
function custom_wc_process_payment($request)
{
    $parameters = $request->get_json_params();
    $order_id = $parameters['order_id'];

    // Process the payment if order ID is provided
    if ($order_id) {
        return rest_ensure_response(['success' => true, 'message' => 'Payment processed.']);
    }

    return new WP_Error('invalid_data', 'Invalid order ID.', ['status' => 400]);
}