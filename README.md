# Two Factor WordPress Plugin - SMS Authentication Option
I’ve worked with several clients who needed WooCommerce Two-Factor Authentication via SMS. Since many of them use a local SMS gateway, I developed this small extension that only requires an updated SMS gateway call to function properly.

## Requirements
- [Two-Factor plugin](https://github.com/WordPress/two-factor)  
- [WooCommerce](https://github.com/woocommerce/woocommerce)

## What does it do?
This extension adds a new gateway option to the [Two-Factor plugin](https://github.com/WordPress/two-factor) for SMS-based authentication.

## What do I need to change for it to work in my setup?
You’ll need to update the API call to integrate with your specific SMS gateway provider.

You can find it here: https://github.com/rwkyyy/two-factor-sms/blob/main/inc/two-factor-sms-class-extension.php#L56

## Anything else?
Out of the box, the plugin retrieves the `billing_phone` from the user profile and sends the authentication SMS to this number. Because of this, **WooCommerce is required**.

## What if I do not want/have WooCommerce?
You can edit the "plugin" and change it's phone number field to whatever you wish!

## Can you help me set it up?
Absolutely! Just reach out, and I’ll assist you.
