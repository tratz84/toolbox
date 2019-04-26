

// save tab state
$(document).ready(function() {
	$('.nav.nav-tabs a[role=tab]').click(function() {
		current_pageState.saveValue('selected-tab', $(this).attr('id'));
	});
	
	if (current_pageState.getValue('selected-tab') != null) {
		$('.nav.nav-tabs').find( '#' + current_pageState.getValue('selected-tab') ).tab('show');
		
		// current_pageState.saveValue('selected-tab', null);
	}
});



function PageState() {
	this.state = history.state || {};
	
	this.saveValue = function(key, val) {
		this.state[key] = val;
		
		history.replaceState( this.state, 'somestate' );
	};
	
	this.getValue = function(key) {
		if (history.state && history.state[key]) {
			return history.state[key];
		}
		
		return null;
	};
}


var current_pageState = new PageState();


