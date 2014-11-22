/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function checkSearch(){
            var postform=true;
            if(jQuery("#wcs3_Depart_Airport_list").val()=='0')
		{
		alert('Please select departure origin');
		postform=false
		}
		if(postform==true && (!jQuery.isNumeric(jQuery("#wcs3_Arrive_Airport_list").val())||jQuery("#wcs3_Arrive_Airport_list").val()=='0'))
		{
		alert('Please select destination');
		postform=false
		}
		if(jQuery("#wcs3_DepartureDate").val()=='Departure date' && postform==true)
		{
		alert('Please select departure date');
		postform=false
		}
		if(jQuery("#wcs3_ReturnDate").val()=='Return date' && postform==true &&  jQuery("#RT").is(':checked')==true)
		{
		alert('Please select return date');
		postform=false
		}
                
		if(jQuery("#wcs3_Adult_list").val()==0 && 
		postform==true)
		{
		alert('Please select number of adults flying');
		postform=false
		}
		var ADL=jQuery("#wcs3_Adult_list").val();
		var KID=jQuery("#wcs3_Children_list").val();
		if((parseInt(ADL)+parseInt(KID))>9)
		{
		alert('Maximum number of passengers is 9');
		postform=false
		}
        if(postform==true)
		{
			jQuery( "#srchflights" ).submit();
        }
        }
function setretunonclick(status)
{
//for one way trip
if(status==0)
	{
	document.getElementById('wcs3_ReturnDate').value='Return date'
	document.getElementById('wcs3_ReturnDate').disabled=true
	document.getElementById('wcs3_ReturnDate').onclick=function(){}
	document.getElementById('wcs3_ReturnDate').style.cursor='default'
	}
//for round trip
if(status==1)
	{
	document.getElementById('wcs3_ReturnDate').disabled=false
	document.getElementById('wcs3_ReturnDate').onclick=function(){CallCalendar('Return',2014,9,'2014/09/08','')}
	document.getElementById('wcs3_ReturnDate').style.cursor='pointer'
	}
	
}        
jQuery(document).ready( function($) {
	
	// WCS3_AJAX_OBJECT available
	
	$(document).ready(function() {
            jQuery("#RT").prop("checked",true);    
            
            wcs3_bind_search_widget_datepicker_handler();

            $('#wcs3_Depart_Airport_list').prepend($('<option>', {
                selected:'selected',
                value: 0,
                text: 'Where From            '
            }));
            $('#wcs3_Arrive_Airport_list').prepend($('<option>', {
                selected:'selected',
                value: 0,
                html: 'Where To'
            }));
            jQuery( '#wcs3_Depart_Airport_list' ).change(function() {
				entry = {
				action: 'departure_list_change2',
				security: WCS3_AJAX_OBJECT.ajax_nonce,
                                depart_airport_id: $('#wcs3_Depart_Airport_list option:selected').val()
			    };
				jQuery.post(WCS3_AJAX_OBJECT.ajax_url, entry, function(data) {
                                jQuery('#wcs3_Arrive_Airport_list').parent().html(data['response']);
                                jQuery('#wcs3_Arrive_Airport_list').prepend(jQuery('<option>', {selected:'selected',value: 0,html: 'Where To'}));
			}).fail(function(err) {
				// Failed
				console.error(err);
				schedule_item_message(WCS3_AJAX_OBJECT.ajax_error, 'error');
				
			});
		
                });
	});
        //ivan
	var wcs3_bind_search_widget_datepicker_handler = function() {
                var dateToday = new Date();
            var dates = $("#wcs3_DepartureDate").datepicker({
                changeMonth: true,
                minDate: dateToday,
                onSelect: function(selectedDate) {
                    var option = this.id == "from" ? "maxDate" : "minDate",
                        instance = $(this).data("datepicker"),
                        date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
                    dates.not(this).datepicker("option", option, date);
                }
            });
            
            var dates = $("#wcs3_ReturnDate").datepicker({
                changeMonth: true,
                minDate: dateToday,
                onSelect: function(selectedDate) {
                    var option = this.id == "from" ? "maxDate" : "minDate",
                        instance = $(this).data("datepicker"),
                        date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
                    dates.not(this).datepicker("option", option, date);
                }
            });
        }
       
        
    }
)

