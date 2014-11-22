<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Link_List_Table
 *
 * @author r.karim
 */
//Our class extends the WP_List_Table class, so we need to make sure that it's there
if(!class_exists('WP_List_Table')){
   require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

function prepare_where_condition() {
        $where =  ' where `id` IS NOT NULL ';
        
        
        $DAID = !empty($_POST["DAID"]) ? mysql_real_escape_string($_POST["DAID"]) : '';
        if(empty($DAID) || !is_numeric($DAID) || $DAID<=0 ){ $DAID=0; }
        if($DAID>0)
            $where .= 'AND `depart_airport_id` = '.$DAID;
        $AAID = !empty($_POST["AAID"]) ? mysql_real_escape_string($_POST["AAID"]) : '';
        if(empty($AAID) || !is_numeric($AAID) || $AAID<=0 ){ $AAID=0; }
        if($AAID>0)
            $where .= ' AND `arrive_airport_id` = '.$AAID;
        $fromDate = !empty($_POST["fromDate"]) ? mysql_real_escape_string($_POST["fromDate"]) : '';
        
        //if(empty($fromDate) || !is_numeric($fromDate) || $fromDate<=0 ){ $fromDate=0; }
        
        $toDate = !empty($_POST["toDate"]) ? mysql_real_escape_string($_POST["toDate"]) : '';
        //if(empty($toDate) || !is_numeric($toDate) || $toDate<=0 ){ $toDate=0; }
        if($fromDate!='From' && $toDate!='To'){
            if(!empty($fromDate) && empty($toDate)){
                $where .= " AND `flight_date` > '".$fromDate."'";
            }
            if(empty($fromDate) && !empty($toDate)){
                $where .= " AND `flight_date` < '".$toDate."'";
            }
            if(!empty($fromDate)&&!empty($toDate)){
                $where .= " AND `flight_date` Between '".$fromDate."' AND '".$toDate."'";
            }
        }
       return  $where;
   }
class Link_List_Table extends WP_List_Table {

   /**
    * Constructor, we override the parent to pass our own arguments
    * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
    */
    function __construct() {
       parent::__construct( array(
      'singular'=> 'data', //Singular label
      'plural' => 'datas', //plural label, also this well be one of the table css class
      'ajax'   => false //We won't support Ajax for this table
      ) );
    }
    
    function column_default($item, $column_name){
        switch($column_name){
               case "id":  
               case "flight_date":  
               case "start_time": 
               case "end_time": 
               case "adult_fare": 
               case "child_fare": 
               case "tax_per_person": 
                    return $item->$column_name;
               case "depart_airport_id": 
               case "arrive_airport_id": 
               case "flight_no_id":      
                   return get_post( $item->$column_name )->post_title;
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }
    /**
     * Add extra markup in the toolbars before or after the list
     * @param string $which, helps you decide if you add the markup after (bottom) or before (top) the list
     */
    function extra_tablenav( $which ) {
            if ( $which == "top" ){
            }
            if ( $which == "bottom" ){
                //The code that goes after the table is there
            }
    }
    function display2() {
        $form = "Airport: ";
        $form .= wcs3_generate_admin_select_list( 'Airport_list', 'DAID' );
        $form .= wcs3_generate_admin_select_list( 'Airport_list', 'AAID' ).'<br/>';
        $form .='Flight Date: ';
        $form .='<input size="17" id="fromDate" name="fromDate" type="text"  value="From">';		
                
        $form .='<input size="17" id="toDate" name="toDate" type="text" value="To">';
        $form .='&nbsp&nbsp&nbsp';
        $form .='<input type="submit" onclick="checkSearch();" value="Filter" class="button action" id="doaction" name="">';
        echo $form;
    }
    /**
    * Define the columns that are going to be used in the table
    * @return array $columns, the array of columns to use with the table
    */
   function get_columns() {
       $columns = array(
            'cb'                => '<input type="checkbox" />', //Render a checkbox instead of text
            'id'                => 'ID',
            'flight_date'       => 'Flight Date',
            'depart_airport_id' => 'Depart Airport',
            'arrive_airport_id' => 'Arrive Airport',
            'flight_no_id'      => 'Flight No',
            'start_time'        => 'Start Time',
            'end_time'          => 'End Time',
            'adult_fare'        => 'Adult Fare',
            'child_fare'        => 'Child Fare',
            'tax_per_person'    => 'Tax(Per Person)'
        );
        return $columns;
   }
   /**
    * Decide which columns to activate the sorting functionality on
    * @return array $sortable, the array of columns that can be sorted by the user
    */
   function get_sortable_columns() {
        $sortable_columns = array(
            'flight_date'       => array('Flight Date',false),     //true means it's already sorted
            'start_time'        => array('Start Time',false),
            'end_time'          => array('End Time',false),
            'adult_fare'        => array('Adult Fare',false),
            'child_fare'        => array('Child Fare',false),
            'tax_per_person'    => array('Tax(Per Person)',false),
            'depart_airport_id' => array('Depart Airport',false),
            'arrive_airport_id' => array('Arrive Airport',false),
            'flight_no_id'      => array('Flight No',false)
        );
        return $sortable_columns;
    }
    function column_id($item){
        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=wcs3-schedule&row_id=%s">Edit</a>',$item->id),
            'delete'    => sprintf('<a href="?page=%s&action=%s&data=%s">Delete</a>',$_REQUEST['page'],'delete',$item->id),
        );
        
        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item->id,
            /*$2%s*/ $item->id,
            /*$3%s*/ $this->row_actions($actions)
        );
    }
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item->id                //The value of the checkbox should be the record's id
        );
    }
    function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }    
    function process_bulk_action() {
        
        //Detect when a bulk action is being triggered...
        if( 'delete'===$this->current_action() ) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
        
    }
    
   
   /**
    * Prepare the table with different parameters, pagination, columns and table elements
    */
   function prepare_items() {
      global $wpdb, $_wp_column_headers;
      $table = wcs3_get_table_name();
      $screen = get_current_screen();

      /* -- Preparing your query -- */
           $query = "SELECT * FROM $table";
           $where = prepare_where_condition();
//           echo $where;
           $query.= $where;
      /* -- Ordering parameters -- */
          //Parameters that are going to be used to order the result
          $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'id'; //If no sort, default to title
          $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
          
          if($orderby=='Tax(PerPerson)')        $orderby = tax_per_person;
          else if($orderby=='ChildFare')        $orderby = child_fare;
          else if($orderby=='AdultFare')        $orderby = adult_fare;
          else if($orderby=='EndTime')          $orderby = end_time;
          else if($orderby=='StartTime')        $orderby = start_time;
          else if($orderby=='FlightNo')         $orderby = flight_no_id;
          else if($orderby=='ArriveAirport')    $orderby = arrive_airport_id;
          else if($orderby=='DepartAirport')    $orderby = depart_airport_id;
          else if($orderby=='FlightDate')       $orderby = flight_date;
          if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }

      /* -- Pagination parameters -- */
           //Number of elements in your table?
           $totalitems = $wpdb->query($query); //return the total number of affected rows
           //How many to display per page?
           $perpage = 20;
           //Which page is this?
           $paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
           //Page Number
           if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
           //How many pages do we have in total?
           $totalpages = ceil($totalitems/$perpage);
           //adjust the query to take pagination into account
          if(!empty($paged) && !empty($perpage)){
             $offset=($paged-1)*$perpage;
            $query.=' LIMIT '.(int)$offset.','.(int)$perpage;
          }

      /* -- Register the pagination -- */
         $this->set_pagination_args( array(
            "total_items" => $totalitems,
            "total_pages" => $totalpages,
            "per_page" => $perpage,
         ) );
         //The pagination links are automatically built according to those parameters

      /* -- Register the Columns -- */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

         $_wp_column_headers[$screen->id]=$columns;

      /* -- Fetch the items -- */
         $this->items = $wpdb->get_results($query);
   }
   
}