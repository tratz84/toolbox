/**
 * 
 */




class EzTemplateLoader {
	
	static cacheTemplates = {};
	
	// cache loading handling
	static urlLoading = {};
	static waitingLoading  = [];
	
	// counter number of xhr-calls
	static xhrCounter = 0;
	
	static callbackXhrFinished = [];
	
	constructor() {
		
	}
	
	
	static loadTemplate( url, callback, opts ) {
		
		opts = opts ? opts : {};
		
		let cacheName = url;
		if (opts.cacheName)
			cacheName = opts.cacheName;
		cacheName = cacheName.toLowerCase();
		
		if (this.cacheTemplates[cacheName]) {
			callback( this.cacheTemplates[cacheName], opts );
			return;
		}
		
		
		// queue callback
		if (!this.waitingLoading[cacheName]) {
			this.waitingLoading[cacheName] = [];
		}
		this.waitingLoading[cacheName].push(
			{
				callback: callback,
				opts: opts
			}
		);
		
		// already xhr request? => skip
		if (this.urlLoading[cacheName] == true)
			return;
		
		// mark as loading
		this.urlLoading[cacheName] = true;
		
		// fetch template
		EzTemplateLoader.xhrCounter++;
		let req = new XMLHttpRequest();
		req.addEventListener("load", function( evt ) {
			EzTemplateLoader.xhrCounter--;
			
			var xhr = evt.currentTarget;
			
			if (xhr.status != 200) {
				console.error( 'Error: EzTemplateLoader: "' + cacheName + '", ' + xhr.statusText + ' (' + xhr.status + ')' );
			}
			
			
			EzTemplateLoader.addCache(cacheName, xhr.responseText);
			
			// exec callbacks
			let c;
			while ( (c = this.waitingLoading[cacheName].pop()) ) {
				if (c.callback)
					c.callback( this.cacheTemplates[cacheName], c.opts );
			}
			
			// callbacks xhr finished
			for(let i in this.callbackXhrFinished) {
				this.callbackXhrFinished[i]();
			}
			
		}.bind(this));
		req.open( "GET", url );
		req.send();
	}
	
	static clearCallbackXhrFinished( callback ) {
		this.callbackXhrFinished = [];
	}
	
	static addCallbackXhrFinished( callback ) {
		this.callbackXhrFinished.push( callback );
	}
	
	static addCache( cacheName, template ) {
		EzTemplateLoader.cacheTemplates[cacheName] = template;
	}
	
}



