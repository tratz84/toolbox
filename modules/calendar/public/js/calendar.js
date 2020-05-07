




function hook_addCalendarItem_Click( obj ) {
	
	var startDate = '';
	var startTime = '';
	var endDate = '';
	var endTime = '';
	var title = '';
	
	var start = $(obj).data('start');
	if (valid_datetime(start)) {
		var dtStart = str2datetime( start );
		var dateTime = start.split(' ');
		
		startDate = format_date( dtStart );
		startTime = dateTime[1];
	}
	else if (valid_date(start)) {
		dtStart = str2datetime( start );
		startDate = format_date( dtStart );
	}
	
	
	var end = $(obj).data('end');
	if (valid_datetime(end)) {
		var dtEnd = str2datetime( end );
		var dateTime = end.split(' ');
		
		endDate = format_date( dtEnd );
		endTime = dateTime[1];
	}
	else if (valid_date(end)) {
		dtEnd = str2datetime( end );
		endDate = format_date( dtEnd );
	}
	
	title = $(obj).data('title');
	
	if (endDate == startDate) {
		endDate = '';
	}

	var url = appUrl('/?m=calendar&c=view&a=edit&edit_derived_item=0&calendarId=first&startDate='+startDate+'&start_time='+startTime+'&end_date='+endDate+'&end_time='+endTime+'&title='+encodeURIComponent(title));

	var opts = {};
	opts.renderCallback = function() {
		$('.form-calendar-item-form').submit(function() {
			hook_addCalendarItem_Save();
			
			return false;
		});
		
		$('.form-calendar-item-form').find('.submit-container').hide();
		
		
		$('.calendar-item-container .submit-calendar-item').click(function() { hook_addCalendarItem_Save(); });
		
		focusFirstField('.calendar-item-container');
	};

	
	show_popup( url, opts );
}


function hook_addCalendarItem_Save() {
	var me = this;

	var data = $('form.form-calendar-item-form').serialize();
	
	$.ajax({
		type: 'POST',
		url: appUrl( '/?m=calendar&c=view&a=save' ),
		data: data,
		success: function(data, textStatus, xhr) {
			if (data.success) {
				// close & reload
				show_user_message(toolbox_t('Calendar item saved'));
				close_popup();
			} else {
				// show errors
				$('.calendar-item-container .error-container').empty();
				$('.calendar-item-container .error-container').addClass('errors');
				
				for(var i in data.errors) {
					var err = $('<div />');
					
					err.text( data.errors[i].label + ' - ' + data.errors[i].message );
					
					$('.calendar-item-container .error-container').append(err);
				}
				
			}
		},
		error: function(xhr, textStatus, errorThrown) {
			console.log(xhr);
			alert(xhr.responseText);
		}
	});
	
};






