<?php
// includes/class-wc-custom-gateway.php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class WC_Custom_Gateway extends WC_Payment_Gateway
{
    public function __construct()
    {
        $this->id = 'custom_gateway'; // Gateway ID
        $this->method_title = __('Custom Payment Gateway', 'custom-wc-payment-gateway'); // Title to display in admin
        $this->method_description = __('Custom Payment Gateway for WooCommerce', 'custom-wc-payment-gateway'); // Description for the gateway
        $this->has_fields = true; // Whether the gateway requires custom fields

        // Load the settings and initialize form fields
        $this->init_form_fields();
        $this->init_settings();

        // Action to save settings when updated
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

        // Add support for WooCommerce blocks
        add_filter('woocommerce_gateway_get_supported_features', [$this, 'add_blocks_support']);
    }

    /**
     * Add WooCommerce Blocks support.
     *
     * @param array $features Array of supported features.
     * @return array Updated features array.
     */
    public function add_blocks_support($features)
    {
        $features[] = 'woocommerce_blocks_support'; // Ensure the gateway supports WooCommerce blocks
        return $features;
    }

    /**
     * Get the title of the payment gateway.
     * Sanitizes the title before returning it.
     *
     * @return string Payment gateway title.
     */
    public function get_title()
    {
        // Get the title from the plugin's settings
        $settings = get_option('woocommerce_' . $this->id . '_settings');

        // Return the sanitized title, or fallback to default if empty
        return isset($settings['title']) && !empty($settings['title']) ? sanitize_text_field($settings['title']) : __('Custom Payment Gateway', 'custom-wc-payment-gateway');
    }

    /**
     * Initialize the payment gateway's form fields.
     */
    public function init_form_fields()
    {
        $this->form_fields = array(
            'title' => array(
                'title' => __('Title', 'custom-wc-payment-gateway'),
                'type' => 'text',
                'description' => __('The title shown to customers during checkout.', 'custom-wc-payment-gateway'),
                'default' => __('Custom Payment Gateway', 'custom-wc-payment-gateway'),
                'desc_tip' => true,
            ),
            'enabled' => array(
                'title' => __('Enable/Disable', 'custom-wc-payment-gateway'),
                'type' => 'checkbox',
                'label' => __('Enable Custom WC Payment Gateway', 'custom-wc-payment-gateway'),
                'default' => 'yes',
            ),
            'test_mode' => array(
                'title' => __('Test Mode', 'custom-wc-payment-gateway'),
                'type' => 'checkbox',
                'label' => __('Enable Test Mode', 'custom-wc-payment-gateway'),
                'default' => 'yes',
                'desc_tip' => true,
            ),
        );
    }

    /**
     * Display the payment fields on the checkout page.
     */
    public function payment_fields()
    {
        // Display a brief message about the custom payment gateway
        echo '<p>' . __('This is a custom payment gateway. Please enter your details below.', 'custom-wc-payment-gateway') . '</p>';

        // Card Number Field
        echo '<p>';
        echo '<label for="mock_card_number">' . __('Card Number', 'custom-wc-payment-gateway') . ':</label>';
        echo '<input type="text" id="mock_card_number" name="mock_card_number" placeholder="1234 5678 9012 3456" maxlength="19" pattern="\d*" required />';
        echo '</p>';

        // Expiration Date Field (MM/YY)
        echo '<p>';
        echo '<label for="mock_expiry_date">' . __('Expiration Date (MM/YY)', 'custom-wc-payment-gateway') . ':</label>';
        echo '<input type="text" id="mock_expiry_date" name="mock_expiry_date" placeholder="MM/YY" maxlength="5" pattern="\d{2}/\d{2}" required />';
        echo '</p>';

        // CVV Field
        echo '<p>';
        echo '<label for="mock_cvv">' . __('CVV', 'custom-wc-payment-gateway') . ':</label>';
        echo '<input type="text" id="mock_cvv" name="mock_cvv" placeholder="123" maxlength="3" pattern="\d{3}" required />';
        echo '</p>';
    }

    /**
     * Process the payment for the order.
     *
     * @param int $order_id The order ID.
     * @return array Result of the payment process.
     */
    public function process_payment($order_id)
    {
        $order = wc_get_order($order_id);

        // Update the order status to "processing"
        $order->update_status('processing', __('Payment successful.', 'custom-wc-payment-gateway'));

        // Reduce stock levels
        wc_reduce_stock_levels($order_id);

        // Clear the cart
        WC()->cart->empty_cart();

        // Return the payment result
        return array(
            'result' => 'success',
            'redirect' => $this->get_return_url($order),
        );
    }
}