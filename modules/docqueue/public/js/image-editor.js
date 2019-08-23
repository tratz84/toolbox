/**
 * 
 */




function DocumentImageEditor(container, opts) {
	
	this.container = container;
	this.opts = opts;
	
	this.range = null;
	
	this.img = null;
	this.resizedImage = null;
	this.canvas = null;
	
	this.degrees = 0;
	
	
	this.drawImage = function() {
		var ctx = this.canvas.getContext('2d');
		
		var cw = this.canvas.width;
		var ch = this.canvas.height;
		
		var iw = this.img.width;
		var ih = this.img.height;
		
		var w = cw;
		var h = ih / iw * w;

		ctx.save();
		
		var degrees=this.degrees;
		
		ctx.clearRect(0, 0, cw, ch);
		
		var tw = cw/2;
		var th = ch/2;
		ctx.translate(tw, th);
		ctx.rotate(degrees* Math.PI / 180);
		
		ctx.drawImage( this.img, -tw+((cw-w)/2), -th+((ch-h)/2), w, h );
		
		ctx.restore();
	};
	
	
	this.init = function() {
		
		this.range = document.createElement('input');
		this.range.type = 'range';
		this.range.min = 0;
		this.range.max = 360;
		this.range.value = 0;
		$(this.range).on('input change', function(evt) {
			this.degrees = evt.target.value
			this.drawImage();
		}.bind(this));
		$(this.container).append( this.range );
		
		
		// create canvas
		this.canvas = document.createElement('canvas');
		$(this.canvas).attr('width', '800px');
		$(this.canvas).attr('height', '800px');
		$(this.canvas).css('border', '1px solid #ccc');
		$(this.canvas).css('display', 'block');
		$(this.container).append( this.canvas );
		
		
		// load image
		this.img = document.createElement('img');
		this.img.src = this.opts.image_url;
		this.img.onload = function() {
			this.drawImage();
		}.bind(this);
		

	};
	
	
	this.init();
}


