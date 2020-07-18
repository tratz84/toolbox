


$(document).ready(function() {
	$('div.usercustomer-select-widget.usercustomer-deleted select.select2-widget').on('change', function() {
		$(this).closest('div.widget').removeClass('usercustomer-deleted');
	});
});

