/**
 * 
 */


$(document).ready(function() {
	
	var anchorPagequeue = $('<a class="fa fa-picture-o"></a>');
	anchorPagequeue.attr('href', appUrl('/?m=filesync&c=pagequeue&a=upload'));
	
	$('.mobile-icon-container').append(anchorPagequeue);
	
	
});

