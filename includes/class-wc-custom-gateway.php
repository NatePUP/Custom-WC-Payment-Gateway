<?php
defined('ABSPATH') || exit;

class WC_Custom_Gateway extends WC_Payment_Gateway
{
    public function __construct()
    {
        $this->id = 'custom_gateway';
        $this->method_title = __('Custom Payment Gateway', 'custom-wc-payment-gateway');
        $this->method_description = __('Allows payments with the Custom Gateway.', 'custom-wc-payment-gateway');

        $this->init_form_fields();
        $this->init_settings();

        $this->title = $this->get_option('title');
        $this->enabled = $this->get_option('enabled');
        $this->description = $this->get_option('description');

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);
        add_action('woocommerce_receipt_' . $this->id, [$this, 'receipt_page']);
        add_action('woocommerce_api_' . $this->id, [$this, 'handle_callback']);
    }

    public function init_form_fields()
    {
        $this->form_fields = [
            'enabled' => [
                'title' => __('Enable/Disable', 'custom-wc-payment-gateway'),
                'type' => 'checkbox',
                'label' => __('Enable Custom Gateway', 'custom-wc-payment-gateway'),
                'default' => 'yes',
            ],
            'title' => [
                'title' => __('Title', 'custom-wc-payment-gateway'),
                'type' => 'text',
                'description' => __('This controls the title shown at checkout.', 'custom-wc-payment-gateway'),
                'default' => __('Custom Payment', 'custom-wc-payment-gateway'),
            ],
            'description' => [
                'title' => __('Description', 'custom-wc-payment-gateway'),
                'type' => 'textarea',
                'description' => __('This controls the description shown at checkout.', 'custom-wc-payment-gateway'),
                'default' => __('Pay with our custom gateway.', 'custom-wc-payment-gateway'),
            ],
        ];
    }

    public function payment_fields()
    {
        echo '<div>
            <label for="mock_card_number">Card Number</label>
            <input type="text" id="mock_card_number" name="mock_card_number" placeholder="1234 5678 9012 3456" />
            <label for="mock_card_expiry">Expiry Date</label>
            <input type="text" id="mock_card_expiry" name="mock_card_expiry" placeholder="MM/YY" />
        </div>';
    }

    public function validate_fields()
    {
        if (empty($_POST['mock_card_number']) || empty($_POST['mock_card_expiry'])) {
            wc_add_notice(__('Please enter valid payment details.', 'custom-wc-payment-gateway'), 'error');
            return false;
        }
        return true;
    }

    public function process_payment($order_id)
    {
        $order = wc_get_order($order_id);
        $order->update_status('processing', __('Payment received.', 'custom-wc-payment-gateway'));
        $order->reduce_order_stock();
        WC()->cart->empty_cart();

        return [
            'result' => 'success',
            'redirect' => $this->get_return_url($order),
        ];
    }
}
