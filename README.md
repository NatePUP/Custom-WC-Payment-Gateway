# Custom WooCommerce Payment Gateway Plugin

## Description

This plugin adds a custom payment gateway for WooCommerce to simulate a payment process. It allows customers to input mock payment details directly on the checkout page, offering an easy way to test and demonstrate the checkout functionality without real payment processing.

## Installation and Activation Steps

1. **Download the Plugin:**

   - Download the `custom-woocommerce-payment-gateway` folder and ensure it contains the files: `custom-woocommerce-payment-gateway.php` and `class-cwg-payment-gateway.php`.

2. **Upload the Plugin:**

   - Log in to your WordPress admin dashboard.
   - Go to `Plugins` > `Add New`.
   - Click on the `Upload Plugin` button.
   - Choose the downloaded plugin ZIP file or upload the folder to the `/wp-content/plugins/` directory using an FTP client.

3. **Activate the Plugin:**

   - Go to `Plugins` in your WordPress dashboard.
   - Find `Custom WooCommerce Payment Gateway` and click `Activate`.

4. **Configure the Plugin:**
   - Navigate to `WooCommerce` > `Settings` > `Payments`.
   - Find `Custom Payment` (or whatever you set the title to) in the list of payment methods.
   - Click on the `Manage` button to configure the settings.
   - Enter the desired title, enable or disable the gateway, and adjust any additional settings as needed.

## How to Test the Functionality

1. **Place a Test Order:**

   - Visit the frontend of your WooCommerce store and add a product to your cart.
   - Proceed to checkout.
   - Select the `Custom Payment` option.
   - Fill in the mock payment details (mock card number and expiration date).

2. **Review the Order:**
   - Upon successful submission of the order, you should be redirected to the order confirmation page.
   - Check that the order status is set to `Processing` in the WooCommerce orders list.

## Plugin Architecture

- **Custom Payment Gateway Class:**
  - The main class `CWG_Payment_Gateway` extends `WC_Payment_Gateway` to define the payment gateway functionalities.
- **Settings Page:**

  - The plugin provides an admin settings page for enabling/disabling the gateway and customizing its title.

- **Payment Fields:**

  - The plugin adds a form to the checkout page to collect mock payment information.

- **Order Processing:**
  - On form submission, the order status is updated to `Processing` to simulate successful payment.

## Notes

- This plugin does not process real payments. It is for demonstration and testing purposes only.
- Ensure proper sanitization and validation of input fields if this plugin is extended for production use.

## Support

For support or feature requests, please open an issue on the plugin's repository.
