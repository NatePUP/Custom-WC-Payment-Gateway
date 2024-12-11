<?php
// includes/class-wc-custom-gateway.php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class WC_Custom_Gateway extends WC_Payment_Gateway
{
    /**
     * Indicates whether the gateway is in test mode.
     *
     * @var bool
     */
    private $test_mode;
    public function __construct()
    {
        $this->id = 'custom_gateway';
        $this->method_title = __('Custom Payment Gateway', 'custom-wc-payment-gateway');
        $this->method_description = __('Custom Payment Gateway for WooCommerce', 'custom-wc-payment-gateway');
        $this->has_fields = true;

        // Load the settings
        $this->init_form_fields();
        $this->init_settings();

        // Retrieve the test mode setting
        $this->test_mode = $this->get_option('test_mode') === 'yes';

        // Save admin options when updated
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
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
        if ($this->test_mode) {
            echo '<p><strong>' . __('Test mode is enabled. Payments are not real.', 'custom-wc-payment-gateway') . '</strong></p>';
        }

        // Display mock payment fields
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

        // Add custom JavaScript for form validation
        echo '<script>
    (function($){
        $("form.checkout").on("checkout_place_order_custom_gateway", function() {
            var cardNumber = $("#mock_card_number").val().trim();
            var expiryDate = $("#mock_expiry_date").val().trim();
            var cvv = $("#mock_cvv").val().trim();

            // Basic validation for each field
            if (!/^\d{16}$/.test(cardNumber.replace(/\s+/g, ""))) {
                alert("' . esc_js(__('Invalid card number. Please enter a 16-digit card number.', 'custom-wc-payment-gateway')) . '");
                return false;
            }

            if (!/^\d{2}\/\d{2}$/.test(expiryDate)) {
                alert("' . esc_js(__('Invalid expiration date. Please use the MM/YY format.', 'custom-wc-payment-gateway')) . '");
                return false;
            }

            if (!/^\d{3}$/.test(cvv)) {
                alert("' . esc_js(__('Invalid CVV. Please enter a 3-digit CVV.', 'custom-wc-payment-gateway')) . '");
                return false;
            }

            return true; // Allow the checkout process to proceed
        });
    })(jQuery);
</script>';
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

        if ($this->test_mode) {
            // Test mode: Set the order status to "processing" and include a note
            $order->update_status('processing', __('Payment successful (test mode).', 'custom-wc-payment-gateway'));
        } else {
            // Live mode: Set the order status to "processing" as usual
            $order->update_status('processing', __('Payment successful.', 'custom-wc-payment-gateway'));
        }

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