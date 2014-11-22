<?php
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
   