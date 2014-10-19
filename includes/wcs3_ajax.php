<?php

/**
 * Ajax handlers for WCS3.
 */

/**
 * Performs standard AJAX nonce verification.
 */
function wcs3_verify_nonce() {
    $valid = check_ajax_referer( 'wcs3-ajax-nonce', 'security', FALSE );
    if (!$valid) {
    	$response = __( 'Nonce verification failed. Please report this to the site administrator', 'wcs3' );
    	$result = 'error';
    	wcs3_json_response( array( 'response' => $response, 'result' => $result ) );
    	die();
    }
}

/**
 * Verifies all required fields are available.
 * 
 * @param array $data: list of required fields ( field_name => Field Name ).
 */
function wcs3_verify_required_fields( array $data ) {
    foreach ( $data as $k => $v ) {
    	if ( !isset( $_POST[$k] ) || $_POST[$k] == '_none') {
//            ivan
//            if(empty($_POST[$k]) && ($k!="end_hour" || $k!="end_minute" || $k!="start_hour" || $k!="start_minute")){
                $response = __( "$v field is required");
    		$result = 'error';
    		wcs3_json_response( array( 'response' => $response, 'result' => $result ) );
    		die();
//            }
        }
    }
}

/**
 * Add or update schedule entry handler.
 */
function wcs3_add_or_update_schedule_entry_callback() {
    wcs3_verify_nonce();
    
    global $wpdb;
    $response = __( 'Schedule entry added successfully', 'wcs3' );
    $result = 'updated';
    $update_request = FALSE;
    $row_id = NULL;
    $days_to_update = array();
    
    $table = wcs3_get_table_name();
    
    $required = array(
//        ivan start
        'flight_date' => __( 'Flight Date' ),
        'depart_airport_id' => __( 'Depart Airport' ),
        'arrive_airport_id' => __( 'Arrive Airport' ),
        'flight_no_id' => __( 'Flight No' ),
        
        'start_hour' => __( 'Start Hour' ),
        'start_minute' => __( 'Start Minute' ),
        'end_hour' => __( 'End Hour' ),
        'end_minute' => __( 'End Minute' ),
        
        'adult_fare' => __( 'Adult Fare' ),
        'child_fare' => __( 'Child Fare' ),
        'tax_per_person' => __( 'Tax Per Person' ),
//        ivan end        
    );
    
    wcs3_verify_required_fields( $required );
    
    if ( isset( $_POST['row_id'] ) ) {
    	// This is an update request and not an insert.
    	$update_request = TRUE;
    	$row_id = sanitize_text_field( $_POST['row_id'] );
    }
    
//    $wcs3_options = wcs3_load_settings();
    
    //ivan start
    $flight_date = sanitize_text_field( $_POST['flight_date'] );
    $depart_airport_id = sanitize_text_field( $_POST['depart_airport_id'] );
    $arrive_airport_id = sanitize_text_field( $_POST['arrive_airport_id'] );
    $flight_no_id = sanitize_text_field( $_POST['flight_no_id'] );
    
    $start_hour = sanitize_text_field( $_POST['start_hour'] );
    $start_minute = sanitize_text_field( $_POST['start_minute'] );
    $end_hour = sanitize_text_field( $_POST['end_hour'] );
    $end_minute= sanitize_text_field( $_POST['end_minute'] );
    
    $adult_fare = sanitize_text_field( $_POST['adult_fare'] );
    $child_fare = sanitize_text_field( $_POST['child_fare'] );
    $tax_per_person = sanitize_text_field( $_POST['tax_per_person'] );
    //ivan end
    
    
    $start = $start_hour . ':' . $start_minute . ':00';
    $end = $end_hour . ':' . $end_minute . ':00';
    
    $days_to_update[$weekday] = TRUE;
    
    // Validate time logic
    $timezone = wcs3_get_system_timezone();
    $tz = new DateTimeZone( $timezone );
    $start_dt = new DateTime( WCS3_BASE_DATE . ' ' . $start, $tz );
    $end_dt = new DateTime( WCS3_BASE_DATE . ' ' . $end, $tz );
	
    if(!($flight_date))
    {
     $response = __( 'You must select the flight date', 'wcs3' );
     $result = 'error';
    }
    else if ( $start_dt >= $end_dt ) {
            // Invalid flight time
            $response = __( 'A flight cannot start before it ends', 'wcs3' );
            $result = 'error';
    }
    else if($adult_fare<=0 || $child_fare <=0  || $tax_per_person <=0)
	{
		$response = __( 'Fare and tax amount can not be zero.', 'wcs3' );
        $result = 'error';
	}
    else {
        //data table columns
        $data = array(
            //ivan start
            'flight_date' => $flight_date,
            'depart_airport_id' => $depart_airport_id,
            'arrive_airport_id' => $arrive_airport_id,
            'flight_no_id' => $flight_no_id,
            'start_time' => $start,
            'end_time' => $end,
            'adult_fare' => $adult_fare,
            'child_fare' => $child_fare,
            'tax_per_person' => $tax_per_person,
            //ivan end
        );
        
        if ( $update_request ) {
            
            $r = $wpdb->update(
                    $table, 
                    $data, 
                    array( 'id' => $row_id ),
                    array( 
                        '%s',
                        '%d',
                        '%d',
                        '%d',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                    ),
                    array( '%d' )
                );
            
            if ($r === FALSE) {
            	$response = __( 'Failed to update schedule entry', 'wcs3' );
            	$result = 'error';
            }
            else {
                $response = __( 'Schedule entry updated successfully' );
            }
        }
        else {
            $r = $wpdb->insert(
            		$table,
            		$data,
            		array(
                		'%s',
                                '%d',
                                '%d',
                                '%d',
                                '%s',
                                '%s',
                                '%s',
                                '%s',
                                '%s',
            		)
            );
            if ($r === FALSE) {
            	$response = __( 'Failed to add schedule entry', 'wcs3' );
            	$result = 'error';
            }
        }
        
    }
    
    wcs3_json_response( array( 'response' => $response, 'result' => $result) );
    die();
}
function wcs3_departure_list_change_callback() {
    $response = __( 'Schedule entry added successfully', 'wcs3' );
    $result = 'updated';
    $row_id = NULL;
    $row_id = sanitize_text_field( $_POST['depart_airport_id'] );
            if ($row_id === NULL) {
            	$response = __( 'Failed to Select', 'wcs3' );
            	$result = 'error';
            }
            else {
                $response = __( wcs3_generate_admin_select_list( 'Airport_list', 'wcs3_Arrive_Airport_list',NULL,$row_id) );
//                $response = __( wcs3_generate_arrived_airport_for_depart_airport_list( 'Airport_list', 'wcs3_Arrive_Airport_list',NULL,$row_id) );
                $result = 'success';
            }
        
    
    wcs3_json_response( array( 'response' => $response, 'result' => $result) );
    die();
}
function wcs3_departure_list_change2_callback() {
    $response = __( 'Schedule entry added successfully', 'wcs3' );
    $result = 'updated';
    $row_id = NULL;
    $row_id = sanitize_text_field( $_POST['depart_airport_id'] );
            if ($row_id === NULL) {
            	$response = __( 'Failed to Select', 'wcs3' );
            	$result = 'error';
            }
            else {
//                $response = __( wcs3_generate_admin_select_list( 'Airport_list', 'wcs3_Arrive_Airport_list',NULL,$row_id) );
                $response = __( wcs3_generate_arrived_airport_for_depart_airport_list( 'Airport_list', 'wcs3_Arrive_Airport_list',NULL,$row_id) );
                $result = 'success';
            }
        
    
    wcs3_json_response( array( 'response' => $response, 'result' => $result) );
    die();
}

/**
 * Returns the schedule for a specific day.
 */
function wcs3_reload_schedule_entry_callback() {
    wcs3_verify_nonce();

    $response = __( 'All flight schedule retrieved successfully', 'wcs3' );
    $result = 'updated';

    wcs3_json_response( array( 'html' => wcs3_schedule_management_page_grid_data() ) );
    die();
}

// Register AJAX handler for add_or_update_schedule_entry.
add_action( 'wp_ajax_add_or_update_schedule_entry', 'wcs3_add_or_update_schedule_entry_callback' );
add_action( 'wp_ajax_departure_list_change', 'wcs3_departure_list_change_callback' );
add_action( 'wp_ajax_departure_list_change2', 'wcs3_departure_list_change2_callback' );
add_action( 'wp_ajax_reload_schedule_entry', 'wcs3_reload_schedule_entry_callback' );




/**
 * Schedule entry delete handler.
 */
function wcs3_delete_schedule_entry_callback() {
    wcs3_verify_nonce();
    
	global $wpdb;
	$response = __( 'Schedule entry deleted successfully', 'wcs3' );
	$result = 'updated';
	
	$table = wcs3_get_table_name();
	
	$required = array(
	    'row_id' => __( 'Row ID' ),
	);
	
	wcs3_verify_required_fields( $required );
	
	$row_id = sanitize_text_field( $_POST['row_id'] );
	
	$result = $wpdb->delete( $table, array( 'id' => $row_id ), array( '%d' ) );
	
	if ($result == 0) {
	    $response = __( 'Failed to delete entry', 'wcs3' );
	    $result = 'error';
	}
	
	wcs3_json_response( array( 'response' => $response, 'result' => $result ) );
    die();
}

// Register AJAX handler for delete_schedule_entry.
add_action( 'wp_ajax_delete_schedule_entry', 'wcs3_delete_schedule_entry_callback' );


/**
 * Schedule entry edit handler.
 */
function wcs3_edit_schedule_entry_callback() {
    wcs3_verify_nonce();
    
	global $wpdb;
	$response = new stdClass();

	$table = wcs3_get_table_name();

	$required = array(
	    'row_id' => __( 'Row ID' ),
	);

	wcs3_verify_required_fields( $required );

	$row_id = sanitize_text_field( $_POST['row_id'] );
	
	$result = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table WHERE id = %d", $row_id ), ARRAY_A );
	if ($result) {
	    $response = $result;
	}

	wcs3_json_response( array( 'response' => $response ) );
	die();
}

// Register AJAX handler for delete_schedule_entry.
add_action( 'wp_ajax_edit_schedule_entry', 'wcs3_edit_schedule_entry_callback' );


?>