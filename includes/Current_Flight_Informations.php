<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of wcs3_current_flight_informations
 *
 * @author r.karim
 */
class Current_Flight_Informations {
    //put your code here
    const PRIVIOUS_WK_TITLE = 'Previous 7 flight days';
    const NEXT_WK_TITLE = 'Next 7 flight days';
    
    const DFPW = 1;
    const DFNW = 2;
    const RFPW = 3;
    const RFNW = 4;
    
    const WEEK_DAYS = 6;
    const MID_WK_DAYS = 3;
    const DEPART_TITLE_TYPE = 0;
    const RETURN_TITLE_TYPE = 1;

    public $Selected_TripType;
    
    public $Depart_Airport_Id; 
    public $Depart_Airport_Name; 
    
    public $Arrive_Airport_Id;
    public $Arrive_Airport_Name;
    
    public $Selected_DepartureDate;
    public $Modified_DepartureDate;
    
    public $Selected_ReturnDate;
    public $Modified_ReturnDate;
    
    public $Selected_Adult_No;
    public $Selected_Children_No;
    
    public $ReturnShow;
    public $DepartureShow;
    
    function display_date_with_link($date,$ReturnShow,$x){
        $wcs3_DepartureDate_Temp=strftime("%Y-%m-%d", strtotime("$date +$x day"));
	$dt = strtotime($wcs3_DepartureDate_Temp);
	$day = date("D", $dt);
	$temp_date =  strtoupper($day) .' '. $wcs3_DepartureDate_Temp;
        
	if(($x-3)!=$this->$DepartureShow){
		echo '<a style="color:#007174;" class="dayslink" href="search-results/?
                    TripType='.$this->$Selected_TripType.'&wcs3_Depart_Airport_list='.$this->$Depart_Airport_Id.'&wcs3_Arrive_Airport_list='
                        .$this->$Arrive_Airport_Id.'&wcs3_DepartureDate='.$this->$Selected_DepartureDate.'&wcs3_ReturnDate='.$this->$Selected_ReturnDate.'&wcs3_Adult_list='
                        .$this->$Selected_Adult_No.'&wcs3_Children_list='.$this->$Selected_Children_No.
                        '&DepartureShow='.($x-3).'&ReturnShow='.$ReturnShow.'">';
		echo $temp_date;
		echo '</a>';
        }
	return $wcs3_DepartureDate_Temp;
    }
    function display_date_in_main_table(){
        
    }
    function set_flight_nextwk_type(){
        
    }
    function check_sit_availability(){
        
    }
    static function return_modified_date($depart_or_return_date,$day_count){
        return(strftime("%Y-%m-%d", strtotime("$depart_or_return_date +$day_count day")));
    }
    function total_fare_calculator($adultFare, $childFare, $taxPerPerson){
        return (($this->$Selected_Adult_No*$adultFare)+($this->$Selected_Children_No*$childFare)+($taxPerPerson*($this->$Selected_Adult_No+$this->$Selected_Children_No)));
    }
}
