# **Custom WC Payment Gateway**

## **Description**

Custom WC Payment Gateway is a simple plugin that adds a mock payment gateway to WooCommerce. It allows customers to simulate payments on the checkout page and provides admin settings for customization.

---

## **Features**

- Allows customers to make mock payments during checkout.
- Includes admin settings for enabling/disabling the gateway, setting a custom gateway name, and other configurations.
- Processes and validates mock payment data.
- Updates WooCommerce order status to "Processing" upon successful payment.

---

## **Installation**

### **From the WordPress Admin Dashboard**

1. Navigate to **Plugins > Add New**.
2. Click **Upload Plugin** and select the plugin `.zip` file.
3. Click **Install Now** and activate the plugin.

### **Manually via FTP**

1. Extract the plugin `.zip` file.
2. Upload the `custom-wc-payment-gateway` folder to the `/wp-content/plugins/` directory.
3. Go to **Plugins** in the WordPress admin dashboard and activate the plugin.

---

## **Configuration**

1. Ensure WooCommerce is installed and activated.
2. Navigate to **WooCommerce > Settings > Payments**.
3. Locate **Custom WC Payment Gateway** and click **Manage**.
4. Configure the following settings:
   - **Enable/Disable**: Toggle to enable or disable the gateway.
   - **Payment Gateway Name**: Set the name displayed at checkout.
   - **Test Mode**: Enable test mode for mock transactions.

---

## **Testing the Plugin**

1. Go to your WooCommerce store checkout page.
2. Select the **Custom Payment Gateway** during checkout.
3. Enter the mock payment details:
   - **Card Number**: Use any numeric value.
   - **Expiration Date**: Use any valid date.
   - **CVC**: Use any numeric value.
4. Place the order. The WooCommerce order status will update to "Processing" upon successful validation.

---

## **Plugin Architecture**

### **Main Files**

- `custom-wc-payment-gateway.php`: Main plugin file that initializes the plugin.
- `includes/class-wc-custom-gateway.php`: Contains the custom payment gateway class extending `WC_Payment_Gateway`.

### **Hooks and Filters**

- `plugins_loaded`: Ensures WooCommerce is loaded before initializing the plugin.
- `woocommerce_payment_gateways`: Adds the custom payment gateway to WooCommerce.

---

## **Demo**

1. **Admin Settings**: Configure the plugin under **WooCommerce > Settings > Payments**.
2. **Checkout Process**: Payment gateway in action with mock data.
3. **Order Processing**: Verify that the order status changes to "Processing" after a successful mock payment.

---

## **Notes**

- This plugin is for demonstration purposes and does not process real payments.
