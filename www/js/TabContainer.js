


function TabContainer(container) {
	
	this.container = container;
	
	
	this._buildSkeleton = function() {
		var nav = $('<nav class="tab-container-navigation" />');
		nav.append('<div class="nav nav-tabs" id="nav-tab" role="tablist" />');
		
        // content
        var cont = $('<div class="tab-content tab-container-content" />');
        
        $(this.container).append( nav );
        $(this.container).append( cont );
	};
	
	this.addTab = function(title, content) {
		var tci = new TabContainerItem(this, title, content);
		return tci;
	};
	
	
	
	this.init = function() {
		this._buildSkeleton();
	};
	
}


function TabContainerItem(tabContainer, title, content) {
	
	this.tabContainer = tabContainer;
	this.title = title;
	this.content = content;
	
	this.menuItem = null;
	this.contentContainer = null;
	
	
	this.setTitle = function(t) {
		this.title = t;
		$(this.menuItem).text( t );
	};
	
	this.setContent = function(c) {
		$(this.contentContainer).html( c );
	};
	
	this.init = function() {
		var t = new Date().getTime();
		
		var firstItem = $(this.tabContainer.container).find('.nav.nav-tabs a').length == 0 ? true : false;
		
		this.menuItem = $('<a class="nav-item nav-link" id="nav-'+t+'-tab" data-toggle="tab" role="tab" aria-controls="'+t+'" href="#nav-'+t+'" aria-selected="false"></a>');
		this.menuItem.text( this.title );
		if ( firstItem ) {
			this.menuItem.addClass('active show');
			this.menuItem.attr('aria-selected', 'true');
		}
		$(this.tabContainer.container).find('nav.tab-container-navigation > div.nav').append( this.menuItem );
		
		
        this.contentContainer = $('<div class="tab-pane fade" id="nav-'+t+'" role="tabpanel" aria-labelledby="'+t+'-tab">');
        this.contentContainer.html( this.content );
        if (firstItem) {
        	this.contentContainer.addClass('active show');
        }
		$(this.tabContainer.container).find('div.tab-container-content').append( this.contentContainer );
	};
	
	
	this.init();
}

