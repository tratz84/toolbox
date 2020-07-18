


$(document).ready(function() {
	$('div.user-select-widget.user-deleted select.select2-widget').on('change', function() {
		$(this).closest('div.widget').removeClass('user-deleted');
	});
});


