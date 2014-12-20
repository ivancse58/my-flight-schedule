<?php
/**
 * Data Grid page.
 */
require_once WCS3_PLUGIN_DIR . '/includes/FlightSchedule_ListTable.php';

//$wp_list_table = new Link_List_Table();
//$wp_list_table->prepare_items();
////Table of elements
////echo '<table>';
//$wp_list_table->display();
function wcs3_standard_datagrid_page_callback(){
    
    //Create an instance of our package class...
    $testListTable = new FlightSchedule_ListTable();
    //Fetch, prepare, sort, and filter our data...
    $testListTable->prepare_items();
    
    ?>
    <div class="wrap">
        
        <div id="icon-users" class="icon32"><br/></div>
        <form id="data-filter" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <!-- Now we can render the completed list table -->
            <?php $testListTable->display2() ?>
        </form>
        
        <h2>Flight Schedule Data Table </h2>
      <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
        <form id="movies-filter" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <!-- Now we can render the completed list table -->
            <?php $testListTable->display() ?>
        </form>
        
    </div>
    <?php
}
