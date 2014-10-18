<?php
/**
 * WCS3 Widgets
 */

/**
 * Adds Foo_Widget widget.
 */
function advancedSearchWidget_Form($title) {
         $form = '<form role="search" method="get" id="'.$widget_id.'-searchform" action="' . esc_url( home_url( '/' ) ) . '" >
        	<div class="widget_search"><table>';
         $form .='<tr>
                    <td colspan="2">'.wcs3_generate_round_oneway_radio_list().'</td>
		</tr>';
         $form .='<tr>
                    <td class="wcs3-col-label">Where from</td>
                    <td>'.wcs3_generate_admin_select_list( 'Airport_list', 'wcs3_Depart_Airport_list' ).'</td>
		</tr>';
         $form .='<tr>
                    <td class="wcs3-col-label">Where to</td>
                    <td>'.wcs3_generate_admin_select_list( 'Airport_list', 'wcs3_Arrive_Airport_list' ).'</td>
		</tr>';
	 $form .='<tr>
                    <td class="wcs3-col-label">Departure date</td>
                    <td><input id="wcs3_DepartureDate" name="wcs3_DepartureDate" type="text"></td>
		</tr>';		
         $form .='<tr>
                    <td class="wcs3-col-label">Return date</td>
                    <td><input id="wcs3_ReturnDate" name="wcs3_ReturnDate" type="text"></td>
		</tr>';		
	 $form .='<tr>
                    <td colspan="2">'.wcs3_generate_adult_child_select_list( 'wcs3_Adult_list', '#Adult' ).'
                        '.wcs3_generate_adult_child_select_list( 'wcs3_Children_list', '#Children' ).'</td>
		</tr>';
         $form .='<tr align="center">
                    <td colspan="2"><input id="wcs3_book_flight_searchwidget" class="" type="submit" value="Find Flights" name="findwidget"></td>
		</tr>';
	  $form .='</table></div>
        	</form>';
//          return $form;
          return new_search_form($title);
}
function new_search_form($title){
    $form = '<form id="srchflights" style="display:inline;" name="srchflights" 
        method="get" action="./search-results/">
        <div class="searchFrom" id="">
	<div style="clear: both;"></div>
	<input type="hidden" id="DepartureShow" name="DepartureShow" value="0">
	<input type="hidden" id="ReturnShow" name="ReturnShow" value="0">
	<div style="padding-bottom:-1px;"></div>
	

		
	<div class="float_left padd3">
		<div class="tilte_wh float_left" style="color: rgb(255, 255, 255);"><i class="imgcontainer arrowDown_Border_Orange" alt="Book flight" style="margin-bottom: -4px;"></i>'.$title.'</div>
	</div>
	<div style="clear: both;"></div>
        
	<div class="bg_form_wrap"> 
		<div class="float_left"> 
                <div class="wcs3_r1c1"> <input type="checkbox" name="pointnpay" value="pointnpay">Use Points + Pay</div></div>
                <div class="float_left"> 
                <div class="wcs3_r1c2">'.wcs3_generate_round_oneway_radio_list().'</div></div>
	</div>
	<div class="bg_form_wrap"> 
		<div class="float_left">
			'.wcs3_generate_admin_select_list( 'Airport_list', 'wcs3_Depart_Airport_list' ).'
		</div>
		<div class="bg_form"></div>
	</div>
	<div class="bg_form_wrap"> 
		<div class="float_left">
			'.wcs3_generate_admin_select_list( 'Airport_list', 'wcs3_Arrive_Airport_list' ).'
		</div>
		<div class="bg_form"></div>
	</div>
	<div class="bg_form_wrap"> 
		<div class="float_left">
			<input id="wcs3_DepartureDate" name="wcs3_DepartureDate" type="text"  value="Departure date">
		</div>
		<div class="bg_form"></div>
	</div>
	<div class="bg_form_wrap"> 
		<div class="float_left">
			<input id="wcs3_ReturnDate" name="wcs3_ReturnDate" type="text" value="Return date">
		</div>
		<div class="bg_form"></div>
	</div>
        <div class="bg_form_wrap"> 
		<div class="float_left"> 
                <div class="wcs3_r2c1"> <input type="checkbox" name="flexidates" value="flexi">Flexible with Dates</div></div>
                
	</div>
        <div class="bg_form_wrap"> 
		<div class="float_left"><div class="wcs3_r3c1">Travel Class</div></div>
                <div class="float_left"><div class="wcs3_r3c2">Adults</div> </div>
                <div class="float_left"><div class="wcs3_r3c3">Children</div> </div>
                <div class="float_left"><div class="wcs3_r3c4">Infants</div> </div>
        </div>
	<div class="bg_form_wrap"> 
                <div class="float_left"><div class="wcs3_r3c1">'.wcs3_generate_travel_classs_list( 'wcs3_travel_class_list').'</div> </div>
                <div class="float_left"><div class="wcs3_r3c2">'.wcs3_generate_adult_child_select_list( 'wcs3_Adult_list', '#Adult' ).'</div> </div>
                <div class="float_left"><div class="wcs3_r3c3">'.wcs3_generate_adult_child_select_list( 'wcs3_Children_list', '#Children' ).'</div> </div>
                <div class="float_left"><div class="wcs3_r3c4">'.wcs3_generate_adult_child_select_list( 'wcs3_Children_list', '#Children' ).'</div> </div>
		
	</div>
	<div class="bg_form_wrap">
        <div class="float_left"><div class="wcs3_r5c1">&nbsp;</div> </div>
        <div class="float_left"><div class="wcs3_r5c2">2-11 yrs</div></div>
        <div class="float_left"><div class="wcs3_r5c3">< 2 yrs</div></div>
	</div>
        
	<div class="bg_form_wrap">
        <div class="float_left"><div class="wcs3_r4c1"><input type="text" placeholder="Promo Code" class="coupon"></div> </div>
        <div class="float_left"><div class="wcs3_r4c2"><div class="Largebutton_Orange" onclick="checkSearch();" id="b_SEARCH">Find Flights</div></div> </div>
	</div>
	
    </div>
    </form>';
    return $form;
}
function my_scripts_method() {
        wp_register_script('custom_script', WCS3_PLUGIN_URL . '/js/custom_script.js', array( 'jquery' ));
	wp_enqueue_script( 'custom_script' );

        wp_localize_script( 'custom_script', 'WCS3_AJAX_OBJECT', array(
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
        wp_register_style( 'front_end_regionaltanzania', WCS3_PLUGIN_URL . '/css/front_end_regionaltanzania.css', false, '1.0.0' );
	wp_enqueue_style( 'front_end_regionaltanzania' );
        wp_register_style( 'front_end_general', WCS3_PLUGIN_URL . '/css/general.css', false, '1.0.0' );
	wp_enqueue_style( 'front_end_general' );
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

}
add_action( 'wp_enqueue_scripts', 'my_scripts_method' );


class WCS3_BookFlightWidget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
				'wcs3_today_classes_widget', // Base ID
				__( 'Book Flight Search Widget', 'wcs3' )
		);
		
		// IMPORTANT
		wcs3_set_global_timezone();
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
	    global $wpdb;
	    $table = wcs3_get_table_name();
	    
		$title = apply_filters( 'widget_title', $instance['title'] );

//		echo $args['before_widget'];
//		if ( ! empty( $title ) )
//			echo $args['before_title'] . $title . $args['after_title'];
		echo advancedSearchWidget_Form($title);
//		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
	    $title = ( isset( $instance[ 'title' ] ) ) ? $instance[ 'title' ] : __( "BOOK FLIGHT", 'wcs3' );
//	    $max_classes = ( isset( $instance[ 'max_classes'] ) ) ? $instance[ 'max_classes'] : 5;
//	    $location = ( isset( $instance[ 'location' ] ) ) ? $instance[ 'location'] : 'all';
        $no_entries_text = ( isset( $instance[ 'no_entries_text' ] ) ) ? $instance[ 'no_entries_text'] : __( 'No Data' );
		
		/* Print Form */
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'wcs3' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		
		<p>
		<label for="<?php echo $this->get_field_id( 'no_entries_text' ); ?>"><?php _e( 'No entries message', 'wcs3' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'no_entries_text' ); ?>" name="<?php echo $this->get_field_name( 'no_entries_text' ); ?>" type="text" value="<?php echo esc_attr( $no_entries_text ); ?>" />
		</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['max_classes'] = strip_tags( $new_instance['max_classes'] );
		$instance['location'] = strip_tags( $new_instance['location'] );
		$instance['no_entries_text'] = strip_tags( $new_instance['no_entries_text'] );

		return $instance;
	}

} // class WCS3_BookFlightWidget

// Register WCS3 widgets
function register_wcs3_widgets() {
    // Register today's classes widget
	register_widget( 'WCS3_BookFlightWidget' );
}
add_action( 'widgets_init', 'register_wcs3_widgets' );