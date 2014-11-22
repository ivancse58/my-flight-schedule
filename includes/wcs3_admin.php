<?php
/**
 * Admin area functions.
 */


/**
 * Register styles and scripts.
 */
function wcs3_load_admin_style() {
	wp_register_style( 'wcs3_admin_css', WCS3_PLUGIN_URL . '/css/wcs3_admin.css', false, '1.0.0' );
	wp_enqueue_style( 'wcs3_admin_css' );
}
add_action( 'admin_enqueue_scripts', 'wcs3_load_admin_style' );
//ivan
function wcs3_load_admin_datepicker() {
	wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

}
add_action( 'admin_enqueue_scripts', 'wcs3_load_admin_datepicker' );
/**
 * Load admin area scripts.
 */
function wcs3_load_admin_script() { 
	wp_register_script('wcs3_admin_js', WCS3_PLUGIN_URL . '/js/wcs3_admin.js', array( 'jquery' ), '1.0.0');
	wp_enqueue_script( 'wcs3_admin_js' );

	wp_localize_script( 'wcs3_admin_js', 'WCS3_AJAX_OBJECT', array(
    	'ajax_error' => __( 'Error', 'wcs3' ),
	    'add_item' => __( 'Add Item', 'wcs3' ),
	    'save_item' => __( 'Save Item', 'wcs3' ),
	    'cancel_editing' => __( 'Exit edit mode', 'wcs3' ),
	    'edit_mode' => __( 'Edit Mode', 'wcs3' ),
	    'delete_warning' => __( 'Are you sure you want to delete this entry?', 'wcs3' ),
	    'import_warning' => __( 'Are you sure you want to to this? This will delete all data added after updating to version 3.', 'wcs3' ),
    	'ajax_url' => admin_url( 'admin-ajax.php' ),
    	'ajax_nonce' => wp_create_nonce( 'wcs3-ajax-nonce' ),
	) );
}
add_action( 'admin_enqueue_scripts', 'wcs3_load_admin_script' );

/**
 * Loads plugins necessary for admin area such as the colorpicker.
 */
function wcs3_load_admin_plugins() {
    // Colorpicker
    wp_register_style( 'wcs3_colorpicker_css', WCS3_PLUGIN_URL . '/plugins/colorpicker/css/colorpicker.min.css' );
    wp_enqueue_style( 'wcs3_colorpicker_css' );
    
    wp_enqueue_script(
    		'wcs3_colorpicker',
    		WCS3_PLUGIN_URL. '/plugins/colorpicker/js/colorpicker.min.js',
    		array( 'jquery' )
    );
}
add_action( 'admin_enqueue_scripts', 'wcs3_load_admin_plugins' );

/**
 * Callback for generating the schedule management page.
 */
function wcs3_schedule_management_page_callback() {
    ?>
    <h1><?php _e('Schedule Management', 'wcs3'); ?></h1>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">    
        <div id="wcs3-schedule-management-form-wrapper">
            <table id="wcs3-schedule-management-form" class="widefat wp-list-table">
                <tr>
                    <td class="wcs3-col-label"><?php _e('Flight Date*', 'wcs3'); ?></td>
                    <td><input id="wcs3_flight_date" name="wcs3_flight_date" type="text"></td>
                </tr>
                <tr>
                    <td class="wcs3-col-label"><?php _e('Depart Airport', 'wcs3'); ?></td>
                    <td><?php echo wcs3_generate_admin_select_list( 'Airport_list', 'wcs3_Depart_Airport_list',NULL,NULL,$exclude_id ); ?></td>
                </tr>
                <tr>
                    <td class="wcs3-col-label"><?php _e('Arrive Airport', 'wcs3'); ?></td>
                    <td><?php echo wcs3_generate_admin_select_list( 'Airport_list', 'wcs3_Arrive_Airport_list',NULL,$exclude_id); ?></td>
                </tr>
                <tr>
                    <td class="wcs3-col-label"><?php _e('Flight No', 'wcs3'); ?></td>
                    <td><?php echo wcs3_generate_admin_select_list( 'Flight_NO', 'wcs3_Flight_NO' ); ?></td>
                </tr>
                <tr>
                    <td class="wcs3-col-label"><?php _e('Start Time (H:M)', 'wcs3'); ?></td>
                    <td><?php echo wcs3_generate_hour_select_list( 'wcs3_start_time', array( 'hour' => 9, 'minute' => 0 ) ); ?></td>
                </tr>
                <tr>
                    <td class="wcs3-col-label"><?php _e('End Time (H:M)', 'wcs3'); ?></td>
                    <td><?php echo wcs3_generate_hour_select_list( 'wcs3_end_time', array( 'hour' => 10, 'minute' => 0 ) ); ?></td>
                </tr>
                <tr>
                    <td class="wcs3-col-label"><?php _e('Adult Fare', 'wcs3'); ?></td>
                    <td><input id="wcs3_adult_fare" name="wcs3_adult_fare" type="text"></td>
                </tr>
                <tr>
                    <td class="wcs3-col-label"><?php _e('Child Fare', 'wcs3'); ?></td>
                    <td><input id="wcs3_child_fare" name="wcs3_child_fare" type="text"></td>
                </tr>
				<tr>
                    <td class="wcs3-col-label"><?php _e('Tax Per Person', 'wcs3'); ?></td>
                    <td><input id="wcs3_tax_per_person" name="wcs3_tax_per_person" type="text"></td>
                </tr>
            </table>
            
            <div id="wcs3-schedule-buttons-wrapper">
                <input id="wcs3-submit-item" type="submit" class="button-primary" value="<?php _e( 'Add Item', 'wcs3' ); ?>" name="wcs3-submit-item" />
                <span class="wcs3-ajax-loader"><img src="<?php echo WCS3_PLUGIN_URL . '/img/loader.gif'; ?>" alt="Ajax Loader" /></span>
                <div id="wcs3-ajax-text-wrapper" class="wcs3-ajax-text"></div>
            </div>
        </div> <!-- /#schedule-management-form-wrapper -->
    </form>
    
    <?php 
//    echo '<div id="wcs3-schedule-events-list-wrapper">';
//uncomment this code when data grid is done ivan    
//        echo wcs3_schedule_management_page_grid_data();
//      echo '</div>';

    ?>
    <?php 
}
?>
<?php
function wcs3_schedule_management_page_grid_data(){

    //b.sajal showing schedule table
    $day_data = wcs3_get_all_schedule(); 
    if($day_data)
    {
        $output .= '<table id="wcs3-admin-table-day-' . $day . '" class="widefat wcs3-admin-schedule-table">';
        $output .= '<tr bgcolor="#888888">
            <th>' . __( 'Flight Date', 'wcs3' ) . '</th>
            <th>' . __( 'Depart Airport', 'wcs3' ) . '</th>
            <th>' . __( 'Arrive Airport', 'wcs3' ) . '</th>
            <th>' . __( 'Flight No', 'wcs3' ) . '</th>    
            <th>' . __( 'Start', 'wcs3' ) . '</th>
            <th>' . __( 'End', 'wcs3' ) . '</th>
            <th>' . __( 'Adult Fare', 'wcs3') . '</th>
            <th>' . __( 'Child Fare', 'wcs3' ) . '</th>
            <th>' . __( 'Tax Per Person 	', 'wcs3') . '</th>
            <th>' . __( 'Delete', 'wcs3') . '</th>
            <th>' . __( 'Edit', 'wcs3') . '</th>
        </tr>';
            
        foreach ( $day_data as $class ) 
        {
            $output .= '<tr>';

            foreach ( $class as $key => $value ) 
            {
                if ( $key != 'id' ) 
		{
                    if($value=='')
                        $output .= "<td>&nbsp;</td>";
                    else
                        $output .= "<td>$value</td>";
        	}
        	else 
		{
        	    $output .= '<td><a href="#delete" class="wcs3-delete-button wcs3-action-button-day-' . $day . '" 
        	                id="delete-entry-' . $value . '">' . __( 'delete', 'wcs3') . '</a></td>';
                    $output .=  '<td><a href="#" class="wcs3-edit-button wcs3-action-button-day-' . $day . '" 
        	                id="edit-entry-' . $value . '">' . __( 'edit', 'wcs3' ) . '</a>';  
        	}
            }
            $output .= '</tr>';
        }
	$output .= '<tr><td>
		<div id="ajaxContent" align="right"></div>
        	<div id="Pagination"  align="center"></div>
		<div id="Searchresult" align="center"></div>
		</td></tr>';
	$output .= '</table>';
    }
    else
    {
        $output .= '<div class="wcs3-no-classes"><p><h3>' . __( 'No Flight Schedule', 'wcs3' ) . '</h3></p></div>';
    }
    return $output;
?>     

<?php
}

/**
 * Generates a select list of id => titles from the array of WP_Post objects.
 * query mlis_flight_schedule table and find all DISTINCT arrive_airport_id for particular depart_airport_id
 * loop this step 2 more times.
 * and add the values in array.
 */
function wcs3_generate_arrived_airport_for_depart_airport_list( $type, $name = '', $default = NULL, $main_depart_airport_id=NULL) {
         global $wpdb;
         $table = wcs3_get_table_name();
         $Departurequery = "SELECT DISTINCT `arrive_airport_id` FROM $table";
         $Departurequery.= " where `depart_airport_id` = '".$main_depart_airport_id."'";
         $departure_data = $wpdb->get_results($Departurequery); 
         
         $arrived_airport_ids = array();
         
         if (!empty($departure_data)) {
         foreach ( $departure_data as $departure ) //1st step
            {
             $depart_airport_id = $departure->arrive_airport_id;
             if($main_depart_airport_id==$depart_airport_id) continue;
             $arrived_airport_ids[$depart_airport_id] = $depart_airport_id;
             $Departurequery = "SELECT DISTINCT `arrive_airport_id` FROM $table where `depart_airport_id` = '".$depart_airport_id."'";
            

             $departure_data2 = $wpdb->get_results($Departurequery); 
             if (!empty($departure_data2)) {
                 foreach ( $departure_data2 as $departure2 ) //2nd step
                 { 
                     $depart_airport_id = $departure2->arrive_airport_id;
                     if($main_depart_airport_id==$depart_airport_id) continue;
                     if (empty($arrived_airport_ids[$depart_airport_id])) {
                         $arrived_airport_ids[$depart_airport_id] = $depart_airport_id;
                         $Departurequery = "SELECT DISTINCT `arrive_airport_id` FROM $table where `depart_airport_id` = '".$depart_airport_id."'";
                         $departure_data3 = $wpdb->get_results($Departurequery); 
                         if (!empty($departure_data3)) {
                            foreach ( $departure_data3 as $departure3 ) //3rd step
                            {
                                $depart_airport_id = $departure3->arrive_airport_id;
                                if($main_depart_airport_id==$depart_airport_id) continue;
                                if (empty($arrived_airport_ids[$depart_airport_id])) {
                                    $arrived_airport_ids[$depart_airport_id] = $depart_airport_id;
                                }
                            }//3rd step loop end
                         }//if end
                      }
                  }//2nd step loop end
            }//if end
         }//1st loop end
        }//if end
        
        $type = 'Airport_list';
	$t = 'wcs3_' . $type;
	$posts = wcs3_get_posts_of_type( $t );

	$values = array();

	if (!empty($posts)) {
	    $exclude_id=$posts[0]->ID;
		foreach ( $posts as $post ) 
		{
                    if(array_key_exists ( $post->ID , $arrived_airport_ids ))
                        if($post->ID!=$main_depart_airport_id)
                            $values[$post->ID] = $post->post_title;
		}
	}

	return wcs3_select_list( $values, $name, $default );
}

/**
 * Generates a select list of id => titles from the array of WP_Post objects.
 *
 * @param string $type: can be either Airport_list or Flight_NO
 */
function wcs3_generate_admin_select_list( $type, $name = '', $default = NULL, $exclude=NULL, &$exclude_id=NULL) {
	$t = 'wcs3_' . $type;
	$posts = wcs3_get_posts_of_type( $t );

	$values = array();

	if (!empty($posts)) {
	    $exclude_id=$posts[0]->ID;
		foreach ( $posts as $post ) 
		{
		    if($post->ID!=$exclude)
			$values[$post->ID] = $post->post_title;
		}
	}

	return wcs3_select_list( $values, $name, $default );
}

function wcs3_generate_hour_select_list( $name = '', 
    $default = array( 'hour' => NULL, 'minute' => NULL ) ) {
    
    $output = '';
    
    $hours = wcs3_select_list( range( 0, 24, 1 ) , $name . '_hours', $default['hour'] );
    
    $minutes_arr = array();
    foreach ( range(0, 59, 5) as $key => $value ) {
        $minutes_arr[$value] = $value;
    }
    
    $minutes = wcs3_select_list( $minutes_arr , $name . '_minutes', $default['minute'] );
    
    $output .= $hours . $minutes;
    
    return $output;
}

function wcs3_generate_round_oneway_radio_list() {
    $values = array(
            'OW' => __( 'One way'),
            'RT' => __( 'Return'),
        );
    $onclickFunctions = array(
            'setretunonclick(0)',
            'setretunonclick(1)',
        );
        if ( !empty( $values ) ) {
            $loopCounter =0;
	    foreach ( $values as $key => $value ){
                $output .= "<input id='$key' type='radio' checked='' onclick = '$onclickFunctions[$loopCounter]' value='$key' name='TripType'>$value &nbsp";
                $loopCounter ++;
            }
        }
    return $output;
}

/**
 * Generates the simple #Travel Class/#Adults/#Children/#Infants list.
*/
function wcs3_generate_widget_selectlist( $name = '', $default = NULL ) {
    if($default=='#Economy')
        $values = array(
            '0' => __( 'Economy', 'wcs3' ),
            '1' => __( 'Business', 'wcs3' ),
            '2' => __( 'First', 'wcs3' ),
        );
    if($default=='#Adult' || $default=='#Children' || $default=='#Infants')
        $values = array(
            '0' => __( '0', 'wcs3' ),
            '1' => __( '1', 'wcs3' ),
            '2' => __( '2', 'wcs3' ),
            '3' => __( '3', 'wcs3' ),
            '4' => __( '4', 'wcs3' ),
            '5' => __( '5', 'wcs3' ),
            '6' => __( '6', 'wcs3' ),
            '7' => __( '7', 'wcs3' ),
            '8' => __( '8', 'wcs3' ),
            '9' => __( '9', 'wcs3' ),
            '10' => __( '10', 'wcs3' ),
        );
    return wcs3_select_list( $values, $name, $default );
}

/**
 * Delete schedule entries when depart_airport_id, arrive_airport_id, or flight_no_id gets deleted.
 */
function wcs3_schedule_sync( $post_id ) {
    global $wpdb;
    $table = wcs3_get_table_name();
    
    // Since all three custom post types are in the same table, we can
    // assume the the ID will be unique so there's no need to check for
    // post type.
    $query = "DELETE FROM $table 
                WHERE depart_airport_id = %d OR arrive_airport_id = %d 
                OR flight_no_id = %d";
    
    $wpdb->query( $wpdb->prepare(
            $query, 
            array( $post_id, $post_id, $post_id )
            ) );
}
add_action( 'delete_post', 'wcs3_schedule_sync', 10 );