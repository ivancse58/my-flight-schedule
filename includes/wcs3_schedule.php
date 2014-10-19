<?php
/**
 * Schedule specific functions.
 */
//b.sajal Retrieving all the rows from schedule table
function wcs3_get_all_schedule($location_id = NULL, $limit = NULL ) 
{
    global $wpdb;
    $table = wcs3_get_table_name();
    $results = array();
    $query = "SELECT * FROM $table ";
    $query_arr = array();
    
    if ( $limit !== NULL ) {
        $query .= "LIMIT %d";
        $query_arr[] = $limit;
    }
    
    $r = $wpdb->get_results( $wpdb->prepare( $query, $query_arr) );
    
    if ( !empty( $r ) ) 
	{
        foreach ( $r as $entry ) 
	{
            $results[] = array(
                'flight_date' => $entry->flight_date,
                'depart_airport' => get_post( $entry->depart_airport_id )->post_title,
                'arrive_airport' => get_post( $entry->arrive_airport_id )->post_title,
                'flight_no' => get_post( $entry->flight_no_id )->post_title,
                'start_time' => $entry->start_time,
		'end_time' => $entry->end_time,
		'adult_fare' => $entry->adult_fare,
		'child_fare' => $entry->child_fare,
		'tax_per_person' => $entry->tax_per_person,
                'id' => $entry->id,
            );
        }
        return $results;
    }
    else 
	{
        return FALSE;
    }
}