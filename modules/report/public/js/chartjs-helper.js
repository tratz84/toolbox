


function create_chartjs( id, config ) {
	var container = $( '#' + id );
	
	var parentContainer = $(container).closest('.grid-stack-item-content');
	
	$(container).empty();
	
	var w = $(container).width();
	var h = parentContainer.height() - (parentContainer.find('.widget-title').outerHeight());
	
	if (config.heightRatio) {
		h *= config.heightRatio;
	}
	
	var canvas = $('<canvas />');
	canvas.attr('width', w);
	canvas.attr('height', h);
	container.append( canvas );
	
	var ctx = $(canvas).get(0).getContext('2d');
	
	var bar = new Chart( ctx, config );
	
}



function report_widget_title( node, title ) {
	if ($(node).hasClass('grid-stack-item') == false) {
		node = $(node).closest( '.grid-stack-item' );
	}
	
	if ($(node).length == 1) {
		$(node).find('.widget-title').text( title );
	}
}


