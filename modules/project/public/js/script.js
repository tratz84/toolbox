
/**
 * 
 */


$(document).ready(function() {
	
	var anchorNewPh = $('<a class="fa fa-clock-o"></a>');
	anchorNewPh.attr('href', appUrl('/?m=project&c=projectHour&a=edit'));
	
	$('.mobile-icon-container').append(anchorNewPh);
	
	
});

