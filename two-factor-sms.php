<?php
/**
 * Plugin Name: Two Factor SMS Authentication Option
 * Plugin URI: https://github.com/rwkyyy/two-factor-sms/
 * Description: Adds SMS-based two-factor authentication option to Two Factor WordPress Plugin
 * Version: 1.0
 * Author: Uprise.ro Team
 * Author URI: https://uprise.ro
 * License: GPL2
 * Text Domain: two-factor-sms
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Check if required plugins are active and add necessary actions.
add_action( 'plugins_loaded', function () {
	// Check if the Two Factor plugin is active.
	if ( ! is_plugin_active( 'two-factor/two-factor.php' ) ) {
		return; // Do nothing if Two Factor is not active.
	}

	// Check if WooCommerce is active.
	if ( ! class_exists( 'WooCommerce' ) ) {
		// Display an admin notice if WooCommerce is not active.
		add_action( 'admin_notices', function () {
			if ( ! get_option( 'two_factor_sms_dismissed_woocommerce_notice' ) ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Two Factor SMS Authentication requires WooCommerce to be active.', 'two-factor-sms' );
				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}
		} );

		// Handle dismiss action for the admin notice.
		add_action( 'wp_ajax_two_factor_sms_dismiss_notice', function () {
			update_option( 'two_factor_sms_dismissed_woocommerce_notice', true );
			wp_die();
		} );

		add_action( 'admin_footer', function () {
			?>
            <script>
                jQuery(document).on('click', '.notice.is-dismissible .notice-dismiss', function () {
                    jQuery.post(ajaxurl, {action: 'two_factor_sms_dismiss_notice'});
                });
            </script>
			<?php
		} );
	}

	// Register the custom provider with the Two Factor plugin.
	add_filter( 'two_factor_providers', function ( $providers ) {
		$providers['Two_Factor_SMS'] = plugin_dir_path( __FILE__ ) . 'inc/two-factor-sms-class-extension.php';

		return $providers;
	} );
}, 20 );

// Enable translations.
add_action( 'plugins_loaded', function () {
	load_plugin_textdomain( 'two-factor-sms', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
} );