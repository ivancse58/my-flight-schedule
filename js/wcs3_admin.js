/**
 * Javascript for WCS3 admin.
 */
function checkSearch(){
    var postform=true;
    if(jQuery("#fromDate").val()!='From'){
        if(jQuery("#toDate").val()=='To'){
            alert('Please Select Both Date');
            postform=false;
        }
    }
    if(jQuery("#toDate").val()!='To'){
        if(jQuery("#fromDate").val()=='From'){
            alert('Please Select Both Date');
            postform=false;
        }
    }                
    if(postform==true){
	jQuery( "#movies-filter" ).submit();
    }
}
(function($) {
	
	
	// WCS3_AJAX_OBJECT available
	
	$(document).ready(function() {
                list_table_search_filter();
		wcs3_bind_schedule_submit_handler();
		wcs3_bind_schedule_delete_handler();
		wcs3_bind_schedule_edit_handler();
		wcs3_bind_colorpickers();
		wcs3_bind_import_update();
                wcs3_bind_schedule_submit_datepicker_handler();//ivan
		var cc=read_query_string();
                if(cc['row_id'] != undefined){
//                    alert(cc['row_id']);
                    edit_fucntion_enable_from_url(cc['row_id']);
                }
                
		//b.sajal
		jQuery( '#wcs3_Depart_Airport_list' ).change(function() {
				entry = {
				action: 'departure_list_change',
				security: WCS3_AJAX_OBJECT.ajax_nonce,
                                depart_airport_id: $('#wcs3_Depart_Airport_list option:selected').val()
			    };
				jQuery.post(WCS3_AJAX_OBJECT.ajax_url, entry, function(data) {
                                jQuery('#wcs3_Arrive_Airport_list').parent().html(data['response']);
			}).fail(function(err) {
				// Failed
				console.error(err);
				schedule_item_message(WCS3_AJAX_OBJECT.ajax_error, 'error');
				
			});
		
                });
                
	});
        //ivan
	var wcs3_bind_schedule_submit_datepicker_handler = function() {
            var dateToday = new Date();
                jQuery('#wcs3_flight_date').datepicker({
                    dateFormat : 'yy-mm-dd',
                    changeMonth: true,
                    minDate: dateToday
                });
        }
	/**
	 * Handles the Add Item button click event.
	 */
	var wcs3_bind_schedule_submit_handler = function() {
		$('#wcs3-submit-item').click(function(e) {
			var day = $('#wcs3_weekday option:selected').val(),
				entry;
			
			e.preventDefault();
			
			entry = {
				action: 'add_or_update_schedule_entry',
				security: WCS3_AJAX_OBJECT.ajax_nonce,
//ivan js code start
                                flight_date: $('#wcs3_flight_date').val(),
                                depart_airport_id: $('#wcs3_Depart_Airport_list option:selected').val(),
                                arrive_airport_id: $('#wcs3_Arrive_Airport_list option:selected').val(),
                                flight_no_id: $('#wcs3_Flight_NO option:selected').val(),
                                adult_fare: $('#wcs3_adult_fare').val(),
                                child_fare: $('#wcs3_child_fare').val(),
                                tax_per_person: $('#wcs3_tax_per_person').val(),
//ivan js code end
				start_hour: $('#wcs3_start_time_hours option:selected').val(),
				start_minute: $('#wcs3_start_time_minutes option:selected').val(),
				end_hour: $('#wcs3_end_time_hours option:selected').val(),
				end_minute: $('#wcs3_end_time_minutes option:selected').val()
			};
			
			if ($('#wcs3-row-id').length > 0) {
				// We've got a hidden row field, that means this is an update
				// request and not a regular insert request.
				entry.row_id = $('#wcs3-row-id').val();
			}
						
			$('#wcs3-schedule-management-form-wrapper .wcs3-ajax-loader').show();
			
			// We can also pass the url value separately from ajaxurl for 
			// front end AJAX implementations
			jQuery.post(WCS3_AJAX_OBJECT.ajax_url, entry, function(data) {
				schedule_item_message(data.response, data.result);
				
				if (data.result == 'updated') {
				update_day_schedule('add');
				}
				
			}).fail(function(err) {
				// Failed
				console.error(err);
				schedule_item_message(WCS3_AJAX_OBJECT.ajax_error, 'error');
				
			}).always(function() {
				exit_editing_mode();
				$('#wcs3-schedule-management-form-wrapper .wcs3-ajax-loader').hide();
			});
		});
	}
	
	/**
	 * Handles the delete button click event.
	 */
	var wcs3_bind_schedule_delete_handler = function() {
		$('.wcs3-delete-button').each(function() {
			// Check if element is already bound.
			if (is_elem_unbound($(this))) {
				// Bound, continue.
				return true;
			}
			
			// Re-bind new elements
			$(this).click(function(e) {
				var row_id,
					src,
					entry,
					confirm = true;
				
				if (typeof(e.target) != 'undefined') {
					src = e.target;
				}
				else {
					src = e.srcElement;
				}
				row_id = src.id.replace('delete-entry-', '')
				
				// Confirm delete operation.
				confirm = window.confirm(WCS3_AJAX_OBJECT.delete_warning);
				if (!confirm) {
					return;
				}
				
				entry = {
					action: 'delete_schedule_entry',
					security: WCS3_AJAX_OBJECT.ajax_nonce,
					row_id: row_id
				};
				
				$('#wcs3-schedule-management-form-wrapper .wcs3-ajax-loader').show();
				
				jQuery.post(WCS3_AJAX_OBJECT.ajax_url, entry, function(data) {
                                    schedule_item_message(data.response, data.result);
                                    update_day_schedule('remove');
				}).fail(function(err) {
					// Failed
					console.error(err);
					schedule_item_message(WCS3_AJAX_OBJECT.ajax_error, 'error');
					
				}).always(function() {
					$('#wcs3-schedule-management-form-wrapper .wcs3-ajax-loader').hide();
				});
			});
		});
	}
	
	/**
	 * Handles the edit button click event.
	 */
	var wcs3_bind_schedule_edit_handler = function() {
		$('.wcs3-edit-button').each(function() {
			if (is_elem_unbound($(this))) {
				// Bound, continue.
				return true;
			}
			
			// Re-bind new elements
			$(this).click(function(e) {
				var src_elem,
					row_id,
					entry;
				
				if (typeof(e.target) != 'undefined') {
					src_elem = e.target;
				}
				else {
					src_elem = e.srcElement;
				}
				
				row_id = src_elem.id.replace('edit-entry-', '');
				
				edit_fucntion_enable_from_url(row_id);
			});
		});
	
	}
	var edit_fucntion_enable_from_url = function(row_id){
                entry = {
			action: 'edit_schedule_entry',
			security: WCS3_AJAX_OBJECT.ajax_nonce,
			row_id: row_id
		};
				
		$('#wcs3-schedule-management-form-wrapper .wcs3-ajax-loader').show();
		jQuery.post(WCS3_AJAX_OBJECT.ajax_url, entry, function(data) {
			// Get row data
			var entry = data.response,
			start_array,
			end_array,
			start_hour,
			start_min,
			end_hour,
			end_min;
			if (entry.hasOwnProperty('flight_no_id')) {
			// We got an entry.
                        //ivan
                            $('#wcs3_Depart_Airport_list').val(entry.depart_airport_id);
                            $('#wcs3_Arrive_Airport_list').val(entry.arrive_airport_id);
                            $('#wcs3_Flight_NO').val(entry.flight_no_id);
                            $('#wcs3_location').val(entry.arrive_airport_id);
                            $('#wcs3_weekday').val(entry.weekday);
                            $('#wcs3_flight_date').val(entry.flight_date);
                            $('#wcs3_adult_fare').val(entry.adult_fare);
                            $('#wcs3_child_fare').val(entry.child_fare);
                            $('#wcs3_tax_per_person').val(entry.tax_per_person);
			// Update time fields.
                            start_array = entry.start_time.split(':');
                            start_hour = start_array[0].replace(/^[0]/g,"");
                            start_min = start_array[1].replace(/^[0]/g,"");
			
                            end_array = entry.end_time.split(':');
                            end_hour = end_array[0].replace(/^[0]/g,"");
                            end_min = end_array[1].replace(/^[0]/g,"");
			
                            $('#wcs3_start_time_hours').val(start_hour);
                            $('#wcs3_start_time_minutes').val(start_min);
						
                            $('#wcs3_end_time_hours').val(end_hour);
                            $('#wcs3_end_time_minutes').val(end_min);
                         // Let's add the row id and the save button.
                            $('#wcs3-submit-item').attr('value', WCS3_AJAX_OBJECT.save_item);
                        /* ------------ Change to editing mode --------- */
                            enter_edit_mode(row_id);
                        /* ----------------------------------------------- */					
                        }
                    }).fail(function(err) {
			// Failed
			console.error(err);
			schedule_item_message(WCS3_AJAX_OBJECT.ajax_error, 'error');
                    }).always(function() {
			$('#wcs3-schedule-management-form-wrapper .wcs3-ajax-loader').hide();
                    });
        }
	/**
	 * Updates dynamically a specific day schedule.
	 */
	var update_day_schedule = function(action) {
		entry = {action: 'reload_schedule_entry',security: WCS3_AJAX_OBJECT.ajax_nonce};
			
		jQuery.post(WCS3_AJAX_OBJECT.ajax_url, entry, function(data) {
			// Rebuild table
			 var html=data.html;
			 parent = $('#wcs3-schedule-events-list-wrapper');
			 to_remove = $('#wcs3-admin-table-day-', parent);
			 to_remove.remove();
			
			if (html.length > 0) 
			{				
				if (action == 'add') {
					parent.append(html).hide().fadeIn('slow');
				}
				else if (action == 'remove') 
				{
					parent.append(html);
				}
			}
			
			
		}).fail(function(err) {
			// Failed
			console.error(err);
			schedule_item_message(WCS3_AJAX_OBJECT.ajax_error, 'error');
			
		}).always(function() {	
			// Re-bind handlers
			wcs3_bind_schedule_delete_handler();
			wcs3_bind_schedule_edit_handler();
			
			$('#wcs3-schedule-management-form-wrapper .wcs3-ajax-loader').hide();
		});
	}
	
	/**
	 * Enter edit mode.
	 */
	var enter_edit_mode = function(row_id) {
		var row_hidden_field,
			cancel_button,
			msg;
		
		// Add editing mode message
		if ($('#wcs3-editing-mode-message').length == 0) {
			msg = '<div id="wcs3-editing-mode-message" class="wcs3-form-message">' + WCS3_AJAX_OBJECT.edit_mode + '</div>';
			$('#wcs3-schedule-management-form-wrapper').prepend(msg)
		}
		
		// Add hidden row field
		if ($('#wcs3-row-id').length > 0) {
			// Field already exists, let's update.
			$('#wcs3-row-id').attr('value', row_id);
		}
		else {
			// Field does not exist.
			row_hidden_field = '<input type="hidden" id="wcs3-row-id" name="wcs3-row-id" value="' + row_id + '">';
			$('#wcs3-schedule-management-form-wrapper').append(row_hidden_field);
		}
		
		// Add cancel editing button
		if ($('#wcs3-cancel-editing').length == 0) {
			cancel_button = '<div id="wcs3-cancel-editing-wrapper"><a href="#" id="wcs3-cancel-editing">' + WCS3_AJAX_OBJECT.cancel_editing + '</a></div>';
			$('#wcs3-schedule-management-form-wrapper').append(cancel_button);
			$('#wcs3-cancel-editing').click(function() {
				exit_editing_mode();
			})
		}
	}
	
	/**
	 * Exit edit mode.
	 */
	var exit_editing_mode = function() {
		$('#wcs3-editing-mode-message').remove();
		$('#wcs3-row-id').remove();
		$('#wcs3-cancel-editing-wrapper').remove();
		$('#wcs3-submit-item').val(WCS3_AJAX_OBJECT.add_item);
	}
	
	/**
	 * Handles the Ajax UI messaging.
	 */
	var schedule_item_message = function(message, status) {
		$('.wcs3-ajax-text').html('').show();
		$('.wcs3-ajax-text').removeClass('updated').removeClass('error')
		if (status == 'updated') {
			$('.wcs3-ajax-text').addClass('updated');
		}
		else if (status == 'error') {
			$('.wcs3-ajax-text').addClass('error');
		}
		$('.wcs3-ajax-text').html(message);
		setTimeout(function() {
			$('.wcs3-ajax-text').fadeOut('slow');
		}, 2000);
	}
	
	/**
	 * Extracts the day ID from an element (delete or edit).
	 */
	var get_day_from_element = function(elem) {
		var cls = elem.className,
			m,
			day;
		
		m = cls.match(/wcs3-action-button-day-(\d)+/g);
		if (m.length > 0) {
			m = m[0];
			day = m.replace('wcs3-action-button-day-', '');
			return parseInt(day);
		}
		else {
			return false;
		}
	}
	
	/**
	 * Checks if a jQuery element is already bound to the 'click' event.
	 * 
	 * @return bool: true if bound, false if not.
	 */
	var is_elem_unbound = function(elem) {
		// Check if element is already bound.
		var t = elem.data('events');
		
		if (typeof(t) != 'undefined') {
			if (t.hasOwnProperty('click')) {
				if (t['click'].length > 0) {
					// Element is already bound.
					// Continue to next iternation, no need to re-bind.
					return true;
				}
			}
		}
	}
	
	/**
	 * Binds the colorpicker plugin to the selectors
	 */
	var wcs3_bind_colorpickers = function() {
		$('.wcs_colorpicker').each(function(index) {
			var elementName = $(this).attr('id');
			$(this).ColorPicker({
				onChange: function (hsb, hex, rgb) {
					$('#' + elementName).val(hex);
					$('.' + elementName).css('background', '#' + hex);
				},
				onBeforeShow: function (hsb, hex, rgb) {
					$(this).ColorPickerSetColor(this.value);
				}
			});
		});
	}
		

	var wcs3_bind_import_update = function() {
		$('#wcs3_import_wcs2_data').click(function(e) {
			var confirm;
			
			e.preventDefault();
			
			entry = {
					action: 'import_update_data',
					security: WCS3_AJAX_OBJECT.ajax_nonce,
				};
			
			// Confirm delete operation.
			confirm = window.confirm(WCS3_AJAX_OBJECT.import_warning);
			if (!confirm) {
				return;
			}
			
			$('#wcs3-import-update-wrapper .wcs3-ajax-loader').show();
			
			jQuery.post(WCS3_AJAX_OBJECT.ajax_url, entry, function(data) {
				schedule_item_message(data.response, data.result);
				
			}).fail(function(err) {
				// Failed
				console.error(err);				
			}).always(function() {	
				// Re-bind handlers
				$('#wcs3-import-update-wrapper .wcs3-ajax-loader').hide();
			});
		});
		
	}
        function read_query_string(){
            var a=window.location.search.split(/\?/);
            var b=a[1].split("&");
            var c={};
            for(var i=0;i<b.length;i++){
                var d=b[i].split("=");
                c[d[0]]=d[1];
            }
            return c;
        }
        //ivan
        function list_table_search_filter(){
            wcs3_bind_search_filter_datepicker_handler();

            $('#DAID').prepend($('<option>', {
                selected:'selected',
                value: 0,
                text: 'Depart Airport'
            }));
            $('#AAID').prepend($('<option>', {
                selected:'selected',
                value: 0,
                html: 'Arrive Airport'
            }));
        }
//ivan
	var wcs3_bind_search_filter_datepicker_handler = function() {
                var dateToday = new Date();
            var dates = $("#fromDate").datepicker({
                dateFormat : 'yy-mm-dd',
                changeMonth: true,
                minDate: null,
                onSelect: function(selectedDate) {
                    var option = this.id == "from" ? "maxDate" : "minDate",
                        instance = $(this).data("datepicker"),
                        date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
                    dates.not(this).datepicker("option", option, date);
                }
            });
            
            var dates = $("#toDate").datepicker({
                dateFormat : 'yy-mm-dd',
                changeMonth: true,
                minDate: null,
                onSelect: function(selectedDate) {
                    var option = this.id == "from" ? "maxDate" : "minDate",
                        instance = $(this).data("datepicker"),
                        date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
                    dates.not(this).datepicker("option", option, date);
                }
            });
        }			
        
	
})(jQuery);