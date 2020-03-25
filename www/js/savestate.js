


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


