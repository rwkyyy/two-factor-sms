<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Two_Factor_SMS extends Two_Factor_Provider {

	public function get_label() {
		return __( 'SMS Authentication', 'two-factor-sms' );
	}

	public function authentication_page( $user ) {
		$result = self::send_sms_code( $user->ID );
		if ( is_wp_error( $result ) ) {
			echo '<div class="error">' . esc_html( $result->get_error_message() ) . '</div>';
		} else {
			echo '<label for="sms_code">' . __( 'Enter the code sent to your phone:', 'two-factor-sms' ) . '</label>';
			echo '<input type="text" name="sms_code" id="sms_code" class="input authcode" inputmode="numeric" />';
		}
	}

	public function validate_authentication( $user ) {
		$otp         = isset( $_POST['sms_code'] ) ? sanitize_text_field( $_POST['sms_code'] ) : '';
		$stored_code = get_user_meta( $user->ID, '_sms_otp_code', true );

		if ( $otp === $stored_code ) {
			delete_user_meta( $user->ID, '_sms_otp_code' );

			return true;
		}

		return false;
	}

	public function is_available_for_user( $user ) {
		$phone = get_user_meta( $user->ID, 'billing_phone', true );

		return ! empty( $phone );
	}

	public static function send_sms_code( $user_id ) {
		$phone = get_user_meta( $user_id, 'billing_phone', true );
		if ( empty( $phone ) ) {
			return new WP_Error( 'no_phone_number', __( 'No phone number available.', 'two-factor-sms' ) );
		}

		$code = self::get_code( 6 );
		update_user_meta( $user_id, '_sms_otp_code', $code );

		$response = self::send_sms_gateway( $phone, $code );

		return is_wp_error( $response ) ? $response : true;
	}

	private static function send_sms_gateway( $phone, $code ) {
		//@todo move the api token and url to a more secure location
		$api_token = 'xxxx'; // Replace with your actual API token.
		$sms_body  = json_encode( [
			'phone'            => $phone,
			'shortTextMessage' => sprintf( 'Your verification code is: %s', $code ),
			'sendAsShort'      => true,
		] );

		$response = wp_remote_post( 'url_of_gateway', [
			'method'  => 'POST',
			'body'    => $sms_body,
			'headers' => [
				'Authorization' => $api_token,
				'Content-Type'  => 'application/json',
			],
			'timeout' => 10,
		] );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		if ( $status_code !== 200 ) {
			return new WP_Error( 'sms_error', __( 'Failed to send SMS.', 'two-factor-sms' ) );
		}

		return true;
	}
}

