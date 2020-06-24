
/**
 * 
 */


$(document).ready(function() {
	
	// check if icon has to be set (permission thing)
	var js = $(document).find('script[src*="project/js/script.js"]');
	if (js.attr('src').toString().indexOf('phicon=1') != -1) {
		var anchorNewPh = $('<a class="fa fa-clock-o"></a>');
		anchorNewPh.attr('href', appUrl('/?m=project&c=projectHour&a=edit'));
		
		$('.mobile-icon-container').append(anchorNewPh);
	}
	
	
});

