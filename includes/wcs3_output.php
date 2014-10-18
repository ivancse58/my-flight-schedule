<?php
/**
 * Shortcodes for WCS3 (standard)
 */

/**
 * Standard [wcs] shortcode
 * 
 * Default:
 *     [wcs layout="normal" location="all"]
 */
require_once WCS3_PLUGIN_DIR . '/includes/Current_Flight_Informations.php';
require_once WCS3_PLUGIN_DIR . '/includes/Departing_Returning_Searched_Data.php';
function wcs3_standard_shortcode( $atts ) {
    global $wcs3_js_data;
    
    $output = '';
    $buffer = '';
    
	extract( shortcode_atts( array(
	    'layout' => 'normal',
	    'location' => 'all',
	    'style' => 'normal',
	), $atts ) );
	
//	$wcs3_options = wcs3_load_settings();
	
	$first_day_of_week = $wcs3_options['first_day_of_week'];
	$mode = ( $wcs3_options['24_hour_mode'] == 'yes' ) ? '24' : '12';
	
	// Get indexed weekday array
	$weekdays = wcs3_get_indexed_weekdays( $abbr = TRUE, $first_day_of_week );
	
	// Render normal layout
	$classes = wcs3_get_classes( $layout, $location, $mode );
	
	// Location
	$location_slug = preg_replace( "/[^A-Za-z0-9]/", '-', $location );
	$location_slug = strtolower( $location_slug );
	 
	$wcs3_js_data['options'] = $wcs3_options;
	$wcs3_js_data['locations'][] = array(
    	'unique_start_times' => array_keys( $classes ),
    	'classes' => $classes,
    	'layout' => $layout,
	    'location_slug' => $location_slug,
	);
	
	
    // Render schedule.
    $output = apply_filters( 'wcs3_pre_render', $output, $style );
	$output .= '<div class="wcs3-schedule-wrapper" id="wcs3-location-' . $location_slug . '">';
	
	if ( $layout == 'normal' ) {
	    // Render normal layout
	    $output .= wcs3_render_normal_schedule( $classes, $location, $weekdays );
	}
	else if ( $layout == 'list' ) {
	    // Render list layout
	    $output .= wcs3_render_list_schedule( $classes, $location, $weekdays );
	}
	else {
	    $buffer = apply_filters( 'wcs3_render_layout', $buffer, $classes, $location, $weekdays, $wcs3_js_data );
	    if ( empty( $buffer ) ) {
	        $output .= __( 'Unsupported layout' );
	    }
	    else {
	        $output .= $buffer;
	    }
	}
	
	$output .= '</div>';
	$output = apply_filters( 'wcs3_post_render', $output, $style, $classes, $location, $weekdays );
	
	// Only load front end scripts and styles if it's our shortcode
	add_action('wp_footer', 'wcs3_localize_front_end_scripts');
	
	return $output;
}
add_shortcode( 'wcs', 'wcs3_standard_shortcode' );

/**
 * Hook into the footer for localizing Javascript.
 */

function wcs3_localize_front_end_scripts() {
    global $wcs3_js_data;
    
	// Load JS and localize.
	wcs3_load_frontend_scripts( $wcs3_js_data );
}

/**
 * Renders normal layout
 * 
 * @param array $classes: classes array as returned by wcs3_get_classes().
 * @param string $location: location to render.
 * @param array $weekdays: indexed weekday array.
 */
function wcs3_render_normal_schedule( $classes, $location, $weekdays ) {   
    if ( empty( $classes ) ) {
        $output = '<div class="wcs3-no-classes-message">' . __( 'No Flight scheduled' ) . '</div>';
        return $output;
    }
    
    $output = '<table class="wcs3-schedule-normal-layout">';
    $output .= '<tr><th class="wcs3-hour-col"></th>';
    foreach ( $weekdays as $day => $index ) {
        $output .= '<th class="wcs3-day-col wcs3-day-col-' . $index . '">' . $day . '</th>';
    }
    $output .= '</tr>';
        
    // Classes are grouped by start hour.
    foreach ( $classes as $hour => $v ) {
        $output .= '<tr><th class="wcs3-hour-row-' . $hour . '">' . $v[0]->start_hour . '</th>';
        $counter = 0;
        foreach ( $weekdays as $day => $index ) {
            $css_name = 'wcs3-hour-row-' . $hour . ' wcs3-day-col-' . $index . ' wcs3-abs-col-' . $counter;
            $output .= '<td class="wcs3-cell ' . $css_name . '"></td>';
            $counter++;
        }
        $output .= '</tr>';
    }
    
    $output .= '</table>';
    
    return $output;
}

/**
 * Renders list layout
 * 
 * @param array $classes: classes array as returned by wcs3_get_classes().
 * @param string $location: location to render.
 * @param int $first_day_of_week: index.
 */
function wcs3_render_list_schedule( $classes, $location, $weekdays ) {
    if ( empty( $classes ) ) {
    	$output = '<div class="wcs3-no-classes-message">' . __( 'No Flight scheduled' ) . '</div>';
    	return $output;
    }
    
//    $wcs3_options = wcs3_load_settings();
    $weekdays_dict = wcs3_get_weekdays();
    $template = $wcs3_options['details_template'];
    
    $output = '<div class="wcs3-schedule-list-layout">';
    
    // Classes are grouped by indexed weekdays.
    foreach ( $weekdays as $day => $index ) {
        $day = $weekdays_dict[$index];
        $day_classes = $classes[$index];
        
        if ( !empty( $day_classes ) ) {
            $output .= "<h3>$day</h3>";
            $output .= '<ul class="wcs3-weekday-list wcs3-weekday-list-' . $index . '">';
            
            foreach ( $day_classes as $class ) {
                $output .= '<li class="wcs3-list-item-class">';
                $output .= wcs3_process_template( $class, $template );;
                $output .= '</li>';
            }
            
            $output .= '</ul>';
            echo '';
        }
    }
    
    $output .= '</div>';
    
    return $output;
}

/**
 * Processes a template (replace placeholder, apply plugins).
 * 
 * @param object $class: class object with all required data.
 * @param string $template: user defined template from settings.
 */
function wcs3_process_template( $class, $template ) {
    $class_a = '<span class="wcs3-qtip-box"><a href="#qtip" class="wcs3-qtip">' . $class->class_title . '</a>';
    $class_a .= '<span class="wcs3-qtip-data">' . $class->class_desc . '</span></span>';
    
    $instructor_a = '<span class="wcs3-qtip-box"><a href="#qtip" class="wcs3-qtip">' . $class->instructor_title . '</a>';
    $instructor_a .= '<span class="wcs3-qtip-data">' . $class->instructor_desc . '</span></span>';
    
    $location_a = '<span class="wcs3-qtip-box"><a href="#qtip" class="wcs3-qtip">' . $class->location_title . '</a>';
    $location_a .= '<span class="wcs3-qtip-data">' . $class->location_desc . '</span></span>';
    
    $template = str_replace( '[class]', $class_a, $template );
    $template = str_replace( '[instructor]', $instructor_a, $template );
    $template = str_replace( '[location]', $location_a, $template );
    $template = str_replace( '[start hour]', $class->start_hour, $template );
    $template = str_replace( '[end hour]', $class->end_hour, $template );
    $template = str_replace( '[notes]', $class->notes, $template );
    
    return $template;
}

/**
 * Enqueue and localize styles and scripts for WCS3 front end.
 */
function wcs3_load_frontend_scripts( $js_data = array() ) {
    // Load qTip plugin
    wp_register_style( 'wcs3_qtip_css', WCS3_PLUGIN_URL . '/plugins/qtip/jquery.qtip.min.css', false, '1.0.0' );
    wp_enqueue_style( 'wcs3_qtip_css' );
    
    wp_register_script('wcs3_qtip_js', WCS3_PLUGIN_URL . '/plugins/qtip/jquery.qtip.min.js', array( 'jquery' ), '1.0.0');
    wp_enqueue_script( 'wcs3_qtip_js' );
    
    wp_register_script('wcs3_qtip_images_js', WCS3_PLUGIN_URL . '/plugins/qtip/imagesloaded.pkg.min.js', array( 'jquery' ), '1.0.0');
    wp_enqueue_script( 'wcs3_qtip_images_js' );
    
    // Load hoverintent
    wp_register_script('wcs3_hoverintent_js', WCS3_PLUGIN_URL . '/plugins/hoverintent/jquery.hoverIntent.minified.js', array( 'jquery' ), '1.0.0');
    wp_enqueue_script( 'wcs3_hoverintent_js' );
    
    // Load common WCS3 JS
    wp_register_script('wcs3_common_js', WCS3_PLUGIN_URL . '/js/wcs3_common.js', array( 'jquery' ), '1.0.0');
    wp_enqueue_script( 'wcs3_common_js' );
        
    // Load custom scripts
    wp_register_style( 'wcs3_front_css', WCS3_PLUGIN_URL . '/css/wcs3_front.css', false, '1.0.0' );
    wp_enqueue_style( 'wcs3_front_css' );
    
    wp_register_script('wcs3_front_js', WCS3_PLUGIN_URL . '/js/wcs3_front.js', array( 'jquery' ), '1.0.0');
    wp_enqueue_script( 'wcs3_front_js' );
    
    // Localize script
    wp_localize_script( 'wcs3_front_js', 'WCS3_DATA', $js_data);
}
function add_query_vars_filter( $vars ){
  $vars[] = "TripType";
  $vars[] = "wcs3_Depart_Airport_list";
  $vars[] = "wcs3_Arrive_Airport_list";
  $vars[] = "wcs3_DepartureDate";
  $vars[] = "wcs3_ReturnDate";
  $vars[] = "wcs3_Adult_list";
  $vars[] = "wcs3_Children_list";
  $vars[] = "ReturnShow";
  $vars[] = "DepartureShow";
      
  return $vars;
}
add_filter( 'query_vars', 'add_query_vars_filter' );
function wcs3_check_sit_availability(){
    
}
function wcs3_return_modified_date($depart_or_return_date,$day_count){
    return(strftime("%Y-%m-%d", strtotime("$depart_or_return_date +$day_count day")));
}
function wcs3_total_fare_calculator($adultFare, $childFare, $wcs3_Adult_list, $wcs3_Children_list, $taxPerPerson){
    return (($wcs3_Adult_list*$adultFare)+($childFare*$wcs3_Children_list)+($taxPerPerson*($wcs3_Adult_list+$wcs3_Children_list)));
}
function wcs3_load_fare_info_query( $TripType, $wcs3_Depart_Airport_list, $wcs3_Arrive_Airport_list , $wcs3_DepartureDate,
        $wcs3_ReturnDate) {
    
}

function wcs3_all_steps(){
?>
<div class="float_left paddt10">
				<div class="float_left"><div onclick="location.href='../';" title="Back" class="Backbutton_Orange" onmouseup="this.className='Backbutton_Orange_onclick';"></div></div>
				<div style="background-color:#70a5ae;behavior: url(https://storage.aerocrs.com/74/PIE.htc);border-radius: 6px;" id="progress">
					<ul> 
						<li><span style="color:#007174" class="t_10blprogress paddl10"><a href="../index.asp"> Search </a></span></li>
						<li><span class="t_11wh paddrl15">|</span></li>
						<li><span class="t_10blackprogress"> Select </span></li>
						<li><span class="t_11wh paddrl15">|</span></li> 
						<li> Itinerary </li>
						<li><span class="t_11wh paddrl15">|</span></li>
						<li> Payment </li>
						<li><span class="t_11wh paddrl15">|</span></li>
						<li> Confirmation </li>
					</ul>
				</div>
			</div>
<?php
}
function wcs3_display_date($wcs3_DepartureDate,$x){
	
	$wcs3_DepartureDate_Temp=strftime("%Y-%m-%d", strtotime("$wcs3_DepartureDate +$x day"));
	$dt = strtotime($wcs3_DepartureDate_Temp);
	$day = date("D", $dt);
	$wcs3_DepartureDate_Temp =  strtoupper($day) .' '. $wcs3_DepartureDate_Temp;
	return $wcs3_DepartureDate_Temp;
}
function wcs3_DepartureShow_ReturnShow_Days_Titles($TripType,$wcs3_Depart_Airport_list,$wcs3_Arrive_Airport_list,
							$wcs3_DepartureDate,$wcs3_ReturnDate,$wcs3_Adult_list,$wcs3_Children_list,$ReturnShow,$DepartureShow,$type){
?>
			<div class="dayswrap">
			<?php
				for ($x=0; $x<=6; $x++) {
					echo '<div class="days">';
					if($type==0){//for DepartureShow
						if(($x-3)!=$DepartureShow)
							echo '<a style="color:#007174;" class="dayslink" href="search-results/?
							TripType='.$TripType.'&wcs3_Depart_Airport_list='.$wcs3_Depart_Airport_list.'&wcs3_Arrive_Airport_list='.$wcs3_Arrive_Airport_list.'&
							wcs3_DepartureDate='.$wcs3_DepartureDate.'&wcs3_ReturnDate='.$wcs3_ReturnDate.'&wcs3_Adult_list='.$wcs3_Adult_list.'&
							wcs3_Children_list='.$wcs3_Children_list.'&DepartureShow='.($x-3).'&ReturnShow='.$ReturnShow.'">';
						echo wcs3_display_date($wcs3_DepartureDate,$x-3);
						if(($x-3)!=$DepartureShow)
							echo '</a>';
					}
					else if($type==1)//for ReturnShow
					{
						if(($x-3)!=$ReturnShow)
							echo '<a style="color:#007174;" class="dayslink" href="search-results/?
							TripType='.$TripType.'&wcs3_Depart_Airport_list='.$wcs3_Depart_Airport_list.'&wcs3_Arrive_Airport_list='.$wcs3_Arrive_Airport_list.'&
							wcs3_DepartureDate='.$wcs3_DepartureDate.'&wcs3_ReturnDate='.$wcs3_ReturnDate.'&wcs3_Adult_list='.$wcs3_Adult_list.'&
							wcs3_Children_list='.$wcs3_Children_list.'&DepartureShow='.$DepartureShow.'&ReturnShow='.($x-3).'">';
						echo wcs3_display_date($wcs3_ReturnDate,$x-3);
						if(($x-3)!=$ReturnShow)
							echo '</a>';
					}
					
					echo '</div>';
					if($x!=6)
						echo '<div class="float_left t_11whd paddrl5">|</div>';
				} 
			?>
			</div>
<?php
}
function wcs3_DepartureShow_ReturnShow_NextWk($TripType,$wcs3_Depart_Airport_list,$wcs3_Arrive_Airport_list,
							$wcs3_DepartureDate,$wcs3_ReturnDate,$wcs3_Adult_list,$wcs3_Children_list,$ReturnShow,$DepartureShow,$type){
//$type can be 		DFPW, DFNW, RFPW, RFNW					
	if($type=='DFPW'){
		//echo 'DEPARTING FLIGHT ';
		$wcs3_Temp_Date=strftime("%Y-%m-%d", strtotime("$wcs3_DepartureDate -7 day"));
		echo '<div class="bt_previous">';
		echo '<a style="color:#007174;" class="dayslink" href="search-results/?
			TripType='.$TripType.'&wcs3_Depart_Airport_list='.$wcs3_Depart_Airport_list.'&wcs3_Arrive_Airport_list='.$wcs3_Arrive_Airport_list.'&
			wcs3_DepartureDate='.$wcs3_Temp_Date.'&wcs3_ReturnDate='.$wcs3_ReturnDate.'&wcs3_Adult_list='.$wcs3_Adult_list.'&
			wcs3_Children_list='.$wcs3_Children_list.'&DepartureShow=0&ReturnShow='.$ReturnShow.'">';
		echo 'Previous 7 flight days ';
		echo '</a>';	
		echo '</div>';		
	}
	else if($type=='DFNW'){	
	    $wcs3_Temp_Date=strftime("%Y-%m-%d", strtotime("$wcs3_DepartureDate +7 day"));
		echo '<div class="bt_next">';
		echo '<a style="color:#007174;" class="dayslink" href="search-results/?
			TripType='.$TripType.'&wcs3_Depart_Airport_list='.$wcs3_Depart_Airport_list.'&wcs3_Arrive_Airport_list='.$wcs3_Arrive_Airport_list.'&
			wcs3_DepartureDate='.$wcs3_Temp_Date.'&wcs3_ReturnDate='.$wcs3_ReturnDate.'&wcs3_Adult_list='.$wcs3_Adult_list.'&
			wcs3_Children_list='.$wcs3_Children_list.'&DepartureShow=0&ReturnShow='.$ReturnShow.'">';
		echo 'Next 7 flight days ';
		echo '</a>';						
		
		echo '</div>';	
	}
	else if($type=='RFPW'){//RETURNING FLIGHT 
		//echo 'RETURNING FLIGHT ';
		$wcs3_Temp_Date=strftime("%Y-%m-%d", strtotime("$wcs3_ReturnDate -7 day"));
		echo '<div class="bt_previous">';
		echo '<a style="color:#007174;" class="dayslink" href="search-results/?
			TripType='.$TripType.'&wcs3_Depart_Airport_list='.$wcs3_Depart_Airport_list.'&wcs3_Arrive_Airport_list='.$wcs3_Arrive_Airport_list.'&
			wcs3_DepartureDate='.$wcs3_DepartureDate.'&wcs3_ReturnDate='.$wcs3_Temp_Date.'&wcs3_Adult_list='.$wcs3_Adult_list.'&
			wcs3_Children_list='.$wcs3_Children_list.'&DepartureShow='.$DepartureShow.'&ReturnShow=0">';
		echo 'Previous 7 flight days ';
		echo '</a>';	
		echo '</div>';			
	}
	else if($type=='RFNW'){//RETURNING FLIGHT 
		$wcs3_Temp_Date=strftime("%Y-%m-%d", strtotime("$wcs3_ReturnDate +7 day"));
		echo '<div class="bt_next">';
		echo '<a style="color:#007174;" class="dayslink" href="search-results/?
			TripType='.$TripType.'&wcs3_Depart_Airport_list='.$wcs3_Depart_Airport_list.'&wcs3_Arrive_Airport_list='.$wcs3_Arrive_Airport_list.'&
			wcs3_DepartureDate='.$wcs3_DepartureDate.'&wcs3_ReturnDate='.$wcs3_Temp_Date.'&wcs3_Adult_list='.$wcs3_Adult_list.'&
			wcs3_Children_list='.$wcs3_Children_list.'&DepartureShow='.$DepartureShow.'&ReturnShow=0">';
		echo 'Next 7 flight days ';
		echo '</a>';						
		echo '</div>';	
	}
}
function wcs3_display_no_data(){
?>
<div style="padding-left:150px;padding-top:10px;">
<p><em style="margin-top: 10px;" class="imgcontainer noSeats"></em><br><br></p>
<p>No flights available, please select a different date,
<br> Or send us your query on our contact form, 
<a href="./Contact">Click here</a></p>
</div>
<?php
}
function wcs3_display_final_data_table($departure_return_data,$type,$total_fare) {
$i=1;
foreach ($departure_return_data as $value) {
//  echo $value->flight_date;
    ?>
                <div class="search_left">
		<input type="radio" checked="" value="1216943" name=
                       "<?php if($type=="departure") echo 'departfltc'; else echo 'returnfltc';?>" id=
                       "<?php if($type=="departure") echo 'departfltc'; else echo 'returnfltc';?>"> 
                <?php echo '$270.00';?>
		</div>
		<div id="search">
		<div class="t_12bl paddt15 paddb10"><strong>Tuesday, 16 September 2014
                    <?php echo $value->flight_date; ?></strong></div>
		
		<ul>
		<li style="width:70px;">08:00</li>
		<li style="width:60px;"><span class="t_10gr"> DEPART </span></li>
		<li style="width:85px;"><?php echo get_post( $value->depart_airport_id )->post_title; ?></li>
		<li style="width:100px;"><?php echo get_post( $value->flight_no_id )->post_title; ?></li>
		</ul>
		
		<ul>
		<li style="width:70px;">10:00</li>
		<li style="width:60px;"><span class="t_10gr">  ARRIVE  </span></li>
		<li style="width:85px;"><?php echo get_post( $value->arrive_airport_id )->post_title; ?></li>
		<li style="width:100px;"></li>
		</ul>
		<div class="paddt5 paddb5 t_10gr">
                    Adult Fare: $<?php echo $value->adult_fare; ?>    &nbsp;&nbsp;&nbsp; 
                    Child Fare: $<?php echo $value->child_fare; ?>  &nbsp;&nbsp;   
                    TAX: $<?php echo $value->tax_per_person; ?> (per person)&nbsp;&nbsp;
                </div>
		</div>
                
<?php
  if(sizeof($departure_return_data)>1 && $i!=sizeof($departure_return_data)){
?>  
		<div style="background-color:#007174;" class="lineblue">
			<img width="1" height="1" src="https://storage.aerocrs.com/images/pix.gif">
                </div>
<?php
  }//if loop run more then once
  $i++;
  }//end for loop
  if(sizeof($departure_return_data)==0) wcs3_display_no_data();
}

function wcs3_display_final_form($TripType,$wcs3_Depart_Airport_list,$wcs3_Arrive_Airport_list,
		$wcs3_DepartureDate,$wcs3_ReturnDate,$wcs3_Adult_list,$wcs3_Children_list,$ReturnShow,$DepartureShow,$type,
                        $departure_data,$return_data) {
?>
<form id="SR" name="SR" action="../includes/actions/flights-book.asp" method="post">
<div style="width:680px;border:0px solid red;" class="float_right">
<div style="float:left;" class="itinerary_wrap paddt10"> 
    <div style="width:675px;" class="float_left"> 
        <div class="t_title"> Select from search results </div> 	
        <div style="clear:both;"></div>
    </div>
    <div class="float_right paddt5">
	<div  id="search_inner">
            <div style="background-color:#e8e8e8;;height:70px;" class="result_top">
                <div style="color:#000;font-size:12px;line-height:20px;padding-left:10px;padding-top:2px;font-weight:bold;">
                    DEPARTING FLIGHT 
                    <i style="margin-bottom:-3px;" height="17" width="17" class="imgcontainer planeRight_Black"></i> 
		</div>
                <div style="clear:both;"></div>
<?php
		$type  = 'DFPW';
		wcs3_DepartureShow_ReturnShow_NextWk($TripType,$wcs3_Depart_Airport_list,$wcs3_Arrive_Airport_list,
		$wcs3_DepartureDate,$wcs3_ReturnDate,$wcs3_Adult_list,$wcs3_Children_list,$ReturnShow,$DepartureShow,$type);							
		$type  = 'DFNW';
		wcs3_DepartureShow_ReturnShow_NextWk($TripType,$wcs3_Depart_Airport_list,$wcs3_Arrive_Airport_list,
		$wcs3_DepartureDate,$wcs3_ReturnDate,$wcs3_Adult_list,$wcs3_Children_list,$ReturnShow,$DepartureShow,$type);
?>
                <div style="clear:both;"></div>
<?php
		$type=0;
		 wcs3_DepartureShow_ReturnShow_Days_Titles($TripType,$wcs3_Depart_Airport_list,$wcs3_Arrive_Airport_list,
		$wcs3_DepartureDate,$wcs3_ReturnDate,$wcs3_Adult_list,$wcs3_Children_list,$ReturnShow,$DepartureShow,$type);
?>
            </div>
<?php	
//		echo wcs3_display_final_data_table($departure_total_items,$departure_data);
                echo wcs3_display_final_data_table($departure_data,"departure",$total_fare);
?>
        </div>
    </div>
<?php 
if($TripType=='RT'){
?>

        <div style="clear:both;"><br></div>
        <div class="float_right paddt5">
	<div  id="search_inner">
		<div style="background-color:#e8e8e8;;height:70px;" class="result_top">
                    <div style="color:#000;font-size:12px;line-height:20px;padding-left:10px;padding-top:2px;font-weight:bold;">
			RETURNING FLIGHT <i style="margin-bottom:-3px;" height="17" width="17" class="imgcontainer planeLeft_Black"></i> 
                    </div>
                    <div style="clear:both;"></div>
<?php
		$type  = 'RFPW';
		wcs3_DepartureShow_ReturnShow_NextWk($TripType,$wcs3_Depart_Airport_list,$wcs3_Arrive_Airport_list,
		$wcs3_DepartureDate,$wcs3_ReturnDate,$wcs3_Adult_list,$wcs3_Children_list,$ReturnShow,$DepartureShow,$type);							
		$type  = 'RFNW';
		wcs3_DepartureShow_ReturnShow_NextWk($TripType,$wcs3_Depart_Airport_list,$wcs3_Arrive_Airport_list,
		$wcs3_DepartureDate,$wcs3_ReturnDate,$wcs3_Adult_list,$wcs3_Children_list,$ReturnShow,$DepartureShow,$type);
?>			
<?php
		$type=1;
		 wcs3_DepartureShow_ReturnShow_Days_Titles($TripType,$wcs3_Depart_Airport_list,$wcs3_Arrive_Airport_list,
		$wcs3_DepartureDate,$wcs3_ReturnDate,$wcs3_Adult_list,$wcs3_Children_list,$ReturnShow,$DepartureShow,$type);
?>
		</div>
<?php	
//		echo wcs3_display_final_data_table2($return_total_items,$return_data);
                echo wcs3_display_final_data_table($return_data,"return",$total_fare);
?>
</div>
</div>
<?php
}//for return flight informations
?>
</div>
				<div style="clear:both;"></div>
					<input type="hidden" name="gotoextras" id="gotoextras" value="">
				<div class="float_right paddt5 paddr5"><div class="Largebutton_Orange" onclick="SR.submit();"> Continue </div></div>
			</div>
			
			</form>
<?php		
}

function wcs3_load_search_items( $query ) {
    $TripType =  get_query_var( TripType );
    $wcs3_Depart_Airport_list =  get_query_var( wcs3_Depart_Airport_list );
    $wcs3_Arrive_Airport_list =  get_query_var( wcs3_Arrive_Airport_list );
    
    $wcs3_DepartureDate =  get_query_var( wcs3_DepartureDate );
    $wcs3_DepartureDate = date('Y-m-d', strtotime($wcs3_DepartureDate));
    
    $wcs3_ReturnDate =  get_query_var( wcs3_ReturnDate );
    $wcs3_ReturnDate = date('Y-m-d', strtotime($wcs3_ReturnDate));
    
    $wcs3_Adult_list =  get_query_var( wcs3_Adult_list );
    $wcs3_Children_list =  get_query_var( wcs3_Children_list );
    $ReturnShow =  get_query_var( ReturnShow );
    $DepartureShow =  get_query_var( DepartureShow );
    
    $departure_search_flight_date =  wcs3_return_modified_date($wcs3_DepartureDate, $DepartureShow);
    $return_search_flight_date =  wcs3_return_modified_date($wcs3_ReturnDate, $ReturnShow);
      
    $full_flight_info = new Current_Flight_Informations();
    $full_flight_info->Selected_TripType = $TripType;
    $full_flight_info->Depart_Airport_Id = $wcs3_Depart_Airport_list;
    $full_flight_info->Arrive_Airport_Id = $wcs3_Arrive_Airport_list;
    
    $full_flight_info->Depart_Airport_Name = get_post( $wcs3_Depart_Airport_list )->post_title;
    $full_flight_info->Arrive_Airport_Name = get_post( $wcs3_Arrive_Airport_list )->post_title;
    
    $full_flight_info->Selected_DepartureDate = $wcs3_DepartureDate;
    $full_flight_info->Selected_ReturnDate = $wcs3_ReturnDate;
    $full_flight_info->DepartureShow = $DepartureShow;
    $full_flight_info->ReturnShow = $ReturnShow;
    
    $full_flight_info->Modified_DepartureDate = Current_Flight_Informations::return_modified_date($wcs3_DepartureDate, $DepartureShow);
    $full_flight_info->Modified_ReturnDate = Current_Flight_Informations::return_modified_date($wcs3_ReturnDate, $ReturnShow);
    
    $full_flight_info->Selected_Adult_No = $wcs3_Adult_list;
    $full_flight_info->Selected_Children_No = $wcs3_Children_list;
    
    
    global $wpdb;
    $all_searched_data = new Departing_Returning_Searched_Data();
    
    $all_searched_data->table = wcs3_get_table_name();
    $all_searched_data->Departurequery = "SELECT * FROM $table";
    $all_searched_data->Returnquery = "SELECT * FROM $table";
    
    $all_searched_data->orderby  = 'start_time';
    $all_searched_data->order = 'ASC';
    
      
      $table = wcs3_get_table_name();
//      $screen = get_current_screen();

      $Departurequery = "SELECT * FROM $table";
      $Returnquery = "SELECT * FROM $table";
      
        
      /* -- Search parameters -- */   
      $Departurequery.= " where `flight_date` = '".$departure_search_flight_date."' AND "
                   . "`depart_airport_id` = '".$wcs3_Depart_Airport_list."' "
                   . "AND `arrive_airport_id` = '".$wcs3_Arrive_Airport_list."'";
      $Returnquery.= " where `flight_date` = '".$return_search_flight_date."' AND "
                   . "`depart_airport_id` = '".$wcs3_Arrive_Airport_list."' "
                   . "AND `arrive_airport_id` = '".$wcs3_Depart_Airport_list."'";
    
    $all_searched_data->Departurequery.= " where `flight_date` = '".$departure_search_flight_date."' AND "
                   . "`depart_airport_id` = '".$wcs3_Depart_Airport_list."' "
                   . "AND `arrive_airport_id` = '".$wcs3_Arrive_Airport_list."'";
    $all_searched_data->Returnquery.= " where `flight_date` = '".$return_search_flight_date."' AND "
                   . "`depart_airport_id` = '".$wcs3_Arrive_Airport_list."' "
                   . "AND `arrive_airport_id` = '".$wcs3_Depart_Airport_list."'";            
      /* -- Ordering parameters -- */
          $orderby  = 'start_time';
          $order = 'ASC';
//SELECT * FROM mlis_flight_schedule where flight_date='2014-09-17' AND depart_airport_id='76' AND
//arrive_airport_id='75' ORDER BY  start_time ASC
          $Departurequery.=' ORDER BY '.$orderby.' '.$order;
          $Returnquery.=' ORDER BY '.$orderby.' '.$order;
    
    $all_searched_data->Departurequery.=' ORDER BY '.$orderby.' '.$order;
    $all_searched_data->Returnquery.=' ORDER BY '.$orderby.' '.$order;

//Number of elements in your table?
    $departure_data = $wpdb->get_results($Departurequery); 
    $return_data = $wpdb->get_results($Returnquery); 
    
    $all_searched_data->departure_data = $wpdb->get_results($all_searched_data->Departurequery); 
    $all_searched_data->return_data = $wpdb->get_results($all_searched_data->Returnquery); 
/* -- Fetch the items -- */
//          echo $query;
//         $this->items = $wpdb->get_results($query);
    wcs3_all_steps();
    wcs3_display_final_form($TripType,$wcs3_Depart_Airport_list,$wcs3_Arrive_Airport_list,
        $wcs3_DepartureDate,$wcs3_ReturnDate,$wcs3_Adult_list,$wcs3_Children_list,$ReturnShow,$DepartureShow,$type,
        $departure_data,$return_data);


		
}
add_shortcode( 'wcsnew', 'wcs3_load_search_items' );

