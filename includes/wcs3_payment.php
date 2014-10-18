<?php
/**
 * payment_Settings page.
 */

function wcs3_standard_payment_settings_page_callback() {
    $wcs3_payment_options = wcs3_load_payment_settings();
    
    if ( isset( $_POST['wcs3_options_nonce'] ) ) {
        // We got a submission
        $nonce = sanitize_text_field( $_POST['wcs3_options_nonce'] );
        $valid = wp_verify_nonce( $nonce, 'wcs3_save_options' );
        
        if ($valid === FALSE) {
        	// Nonce verification failed.
        	wcs3_options_message( __('Nonce verification failed', 'wcs3'), 'error' );
        }
        else {
//                print_r($wcs3_payment_options);
        	wcs3_options_message( __('Options updated', 'wcs3') );
        
        	// Create a validataion fields array:
        	// id_of_field => validation_function_callback
//        	$fields = array(
//        	    'seller_account' => 'wcs3_validate_seller_account',
//        	    'currency_code' => 'wcs3_validate_currency_code',
//        	    'item_name' => 'wcs3_validate_item_name',
//        	    'return_url' => 'wcs3_validate_return_url',
//        	);
        	
//        	$wcs3_payment_options = wcs3_perform_validation( $fields, $wcs3_payment_options );
        	
        	wcs3_save_payment_settings( $wcs3_payment_options );
        }
    }
    
    ?>
    
    <h2><?php _e( 'Here is the Payment Gateway Settings', 'wcs3' ); ?></h2>
    
    <p> 
        <?php _e( 'A finalized shortcode may look something like', 'wcs3' ); ?> :
    </p>  
    
    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" name="wcs3_general_settings">
        <h3> <?php _e( 'General Settings', 'wcs3' ); ?></h3>
        <table class="form-table">
            <tr>
                <th>
                    <?php _e( 'Seller Account', 'wcs3' ); ?><br/>
                    <div class="wcs3-description"><?php _e( 'Seller Account/email address', 'wcs3' ); ?></div>
                </th>
                <td><input type="text" id="wc3_seller_account" name="wc3_seller_account" value="<?php echo $wcs3_payment_options['seller_account']; ?>">
				</td>
            </tr>
            <tr>
                <th>
                    <?php _e( 'Currency Code', 'wcs3' ); ?>
                    <div class="wcs3-description"><?php _e( 'Currency Code like USD/EURO', 'wcs3' ); ?></div>    
                </th>
                <td><input type="text" id="wc3_currency_code" name="wc3_currency_code" value="<?php echo $wcs3_payment_options['currency_code']; ?>"></td>
            </tr>
            <tr>
                <th>
                    <?php _e( 'Item Name', 'wcs3' ); ?>
                    <div class="wcs3-description"><?php _e( 'Item Name', 'wcs3' ); ?></div>    
                </th>
                <td><input type="text" id="wc3_item_name" name="wc3_item_name" value="<?php echo $wcs3_payment_options['item_name']; ?>"></td>
            </tr>
            <tr>
                <th>
                    <?php _e( 'Return Url', 'wcs3' ); ?>
                    <div class="wcs3-description"><?php _e( 'Return Url', 'wcs3' ); ?></div>    
                </th>
                <td><input type="text" id="wc3_return_url" name="wc3_return_url" value="<?php echo $wcs3_payment_options['return_url']; ?>"></td>
            </tr>
            
        </table>
 
        <?php submit_button( __( 'Save Settings' ) ); ?>
        <?php wp_nonce_field( 'wcs3_save_options', 'wcs3_options_nonce' ); ?>
    </form>
    
    <?php 
}

/**
 * Gets the standard wcs3 payment_settings from the database and return as an array.
 */
function wcs3_load_payment_settings() {
    wcs3_set_default_payment_settings();
    $settings = get_option( 'wcs3_payment_settings' );
    return unserialize( $settings );
}

/**
 * Saves the settings array
 * 
 * @param array $settings: 'option_name' => 'value'
 */
function wcs3_save_payment_settings( $settings ) {
    $settings = serialize( $settings );
    update_option( 'wcs3_payment_settings', $settings );
}

/**
 * Set default WCS3 payment_settings.
 */
function wcs3_set_default_payment_settings() {
    $settings = get_option( 'wcs3_payment_settings' );
    if ( $settings === FALSE ) {
        // No settings yet, let's load up the default.
        $options = array(
            'seller_account' => 'test@example.com',
            'currency_code' => 'USD',
            'item_name' => 'Item1',
            'return_url' => 'http://www.example.com/paypal/success.php',
        );
        
        $serialized = serialize( $options );
        add_option( 'wcs3_payment_settings', $serialized );
    }
}
add_action( 'wcs3_default_payment_settings', 'wcs3_set_default_payment_settings' );