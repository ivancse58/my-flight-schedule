<?php
/**
 * WCS3 Database operations
 */

/**
 * Creates the required WCS3 db tables.
 */
function wcs3_create_db_tables() {
    $table_name = wcs3_get_table_name();
    
    $sql = "CREATE TABLE `$table_name` (
        `id` bigint(20) NOT NULL AUTO_INCREMENT,
        `flight_date` date NOT NULL DEFAULT '0000-00-00',
        `depart_airport_id` bigint(20) DEFAULT NULL,
        `arrive_airport_id` bigint(20) DEFAULT NULL,
        `flight_no_id` bigint(20) DEFAULT NULL,
        `start_time` time NOT NULL DEFAULT '00:00:00',
        `end_time` time NOT NULL DEFAULT '00:00:00',
        `adult_fare` float DEFAULT NULL,
        `child_fare` float DEFAULT NULL,
        `tax_per_person` float DEFAULT NULL,
        PRIMARY KEY (`id`),
        )";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    
    add_option( "wcs3_db_version", WCS3_DB_VERSION );
}
