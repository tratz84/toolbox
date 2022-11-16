


function create_chartjs( id, config ) {
	var container = $( '#' + id );
	container.addClass( 'chart-js-container' );
	
	var parentContainer = $(container).closest('.grid-stack-item-content');
	
	$(container).empty();
	
	var w = $(container).width();
	var h = parentContainer.height() - (parentContainer.find('.widget-title').outerHeight());
	parentContainer.find('.chart-overhead').each(function(index, node) {
		h -= $(node).outerHeight(true);
	});
	
	
	if (config.heightRatio) {
		h *= config.heightRatio;
	}
	
	config.options.responsive = true;
	config.options.maintainAspectRatio = false;

	var canvas = $('<canvas />');
//	canvas.attr('width', w);
	canvas.attr('height', h);
	container.append( canvas );
	
	var ctx = $(canvas).get(0).getContext('2d');
	
	var bar = new Chart( ctx, config );
	
	container.data( 'chartjs', bar );
}



function report_widget_title( node, title ) {
	if ($(node).hasClass('widget-item-content') == false) {
		node = $(node).closest( '.widget-item-content' );
	}
	
	if ($(node).length == 1) {
		$(node).find('.widget-title').text( title );
	}
}


