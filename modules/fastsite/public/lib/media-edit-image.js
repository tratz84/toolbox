


function MediaEditImage(container, url, opts) {
	
	this.container = container;
	this.url = url;
	this.opts = opts || {};
	
	this.img = null;
	this.canvas = null;
	
	this.rotationEnabled = false;
	
	this.zoom = 100;
	this.degrees = 0;
	this.crop1 = { x: 0, y: 0 };		// pos1
	this.crop2 = { x: 0, y: -1 };		// pos2
	
	
	this.imageLoaded = function() {
		this.canvas = document.createElement('canvas');
		
		$(this.container).append(this.canvas);
		
		this.drawImage();
	};
	
	this.enableRotate = function() {
		this.rotationEnabled = true;
	};
	
	this.disableRotation = function() {
		this.rotationEnabled = false;
	};
	
	
	this.drawImage = function() {
		// image not loaded yet?
		if (!this.img) {
			return;
		}
			
		
		var scaledWidth  = this.img.width  / 100 * this.zoom;
		var scaledHeight = this.img.height / 100 * this.zoom;
		
		
//		void ctx.drawImage(image, sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight);
		var ctx = this.canvas.getContext('2d');
		
		
		if (this.rotationEnabled) {
			var size = scaledWidth > scaledHeight ? scaledWidth : scaledHeight;
			
			this.canvas.width  = size;
			this.canvas.height = size;
			
			ctx.drawImage(this.img, 0, 0, this.img.width, this.img.height, size/2-scaledWidth/2, size/2-scaledHeight/2, scaledWidth, scaledHeight);
		} else {
			this.canvas.width  = parseInt(scaledWidth);
			this.canvas.height = parseInt(scaledHeight);
			
			ctx.drawImage(this.img, 0, 0, this.img.width, this.img.height, 0, 0, scaledWidth, scaledHeight);
		}
		
		
	};
	
	
	this.setZoom = function(val) {
		val = parseInt(val);
		
		if (!isNaN(val)) {
			this.zoom = val;
		}
		
		this.drawImage();
	}
	
	
	this.init = function() {
		
		this.img = document.createElement('img');
		this.img.onload = function() {
			this.imageLoaded();
		}.bind(this);
		this.img.src = this.url;
		
	};
	
	
	this.init();
	
}

