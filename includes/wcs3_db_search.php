<?php

   /**
    * Prepare the table with different parameters, pagination, columns and table elements
    */
   function prepare_items() {
      global $wpdb, $_wp_column_headers;
      $table = wcs3_get_table_name();
      $screen = get_current_screen();

      /* -- Preparing your query -- */
           $query = "SELECT * FROM $table";

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

   function departing_or_returning_list( $depart_airport_id, $arrival_airport_id, $flight_date, $adult_fare, $child_fare ){
      global $wpdb, $_wp_column_headers;
      $table = wcs3_get_table_name();
      $screen = get_current_screen();

      /* -- Preparing your query -- */
      $query = "SELECT * FROM $table";

      $query.=' WHERE `depart_airport_id` ='.$depart_airport_id.
              ' AND `arrive_airport_id` ='.$depart_airport_id.
              ' AND `flight_date` = `'.$flight_date.'`';

      if($adult_fare>0)
        $query.=' ORDER BY `adult_fare` ASC';
      else if($child_fare>0)
        $query.=' ORDER BY `child_fare` ASC';
      
      //Number of elements in your table?
      $totalitems = $wpdb->query($query); //return the total number of affected rows

      $this->items = $wpdb->get_results($query);
   }
   