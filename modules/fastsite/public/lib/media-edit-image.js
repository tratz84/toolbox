


function MediaEditImage(container, url, opts) {
	
	this.container = container;
	this.url = url;
	this.opts = opts || {};
	
	this.img = null;
	this.canvas = null;
	
	this.rotationEnabled = false;
	
	this.zoom = 100;
	this.degrees = 0;
	
	this.crop = {
		pos1: { x: 0, y: 0 },
		pos2: null
	};
	
	this.mousestate = {
		down: false,
		edge: null
	};
	
	this.fixedSide = '';
	
	
	this.setDegrees = function(d) {
		d = parseInt(d);
		if (isNaN(d)) {
			console.log('setDegrees, invalid value: ' + d);
			return;
		}
		
		if (d < 0) d = 0;
		if (d > 360) d = 360;
		
		this.degrees = d;
		this.drawImage();
		
		$('[name=degrees]').val( d );
	};
	
	this.getCropX1 = function() { return this.crop.pos1.x / this.canvas.width * (this.img.width>this.img.height?this.img.width:this.img.height); };
	this.getCropY1 = function() { return this.crop.pos1.y / this.canvas.height * (this.img.width>this.img.height?this.img.width:this.img.height); };
	this.getCropX2 = function() { return this.crop.pos2.x / this.canvas.width * (this.img.width>this.img.height?this.img.width:this.img.height); };
	this.getCropY2 = function() { return this.crop.pos2.y / this.canvas.height * (this.img.width>this.img.height?this.img.width:this.img.height); };
	this.getDegrees= function() { return this.degrees == 360 ? 0 : this.degrees; };

	
	
	this.imageLoaded = function() {
		this.canvas = document.createElement('canvas');
//		$(this.canvas).css('border', '1px solid #f00');
		
		
		$(this.canvas).mousemove(function(evt) {
			this.canvasMousemove( evt );
		}.bind(this));
		$(this.canvas).mousedown(function(evt) {
			this.canvasMousedown( evt );
		}.bind(this));
		
		$(this.canvas).on('mouseup mouseout', function(evt) {
			this.canvasMouseup( evt );
		}.bind(this));

		$(this.container).append(this.canvas);

		$('[name=img_width]').val( this.img.width );
		$('[name=img_height]').val( this.img.height );
		
		// set default crop
		this.drawImage();

		var sc = this.getScaledWidth();
		var sh = this.getScaledHeight();
		
		this.crop.pos1.x = this.canvas.width/2  - sc/2;
		this.crop.pos1.y = this.canvas.height/2 - sh/2;
		this.crop.pos2.x = this.canvas.width/2  + sc/2;
		this.crop.pos2.y = this.canvas.height/2 + sh/2;
		
		this.drawImage();
	};
	
	this.enableRotate = function() {
		this.rotationEnabled = true;
	};
	
	this.disableRotation = function() {
		this.rotationEnabled = false;
	};

	
	this.getScaledWidth = function() {
		var scaledWidth = this.img.width  / 100 * this.zoom;
		return scaledWidth;
	};
	this.getScaledHeight = function() {
		var scaledHeight = this.img.height / 100 * this.zoom;
		return scaledHeight;
	};
	
	this.drawImage = function() {
		// image not loaded yet?
		if (!this.img) {
			return;
		}
			
		
		var scaledWidth  = this.getScaledWidth();
		var scaledHeight = this.getScaledHeight();
		
		
		var ctx = this.canvas.getContext('2d');
		
		if (this.degrees != 0 && this.degrees != 360) {
		} else {
			this.rotationEnabled = false;
		}
		this.rotationEnabled = true;
		
		
//		if (this.rotationEnabled) {
			var size = scaledWidth > scaledHeight ? scaledWidth : scaledHeight;
			
			this.canvas.width  = size;
			this.canvas.height = size;
			
			var tw = size/2;
			var th = size/2;
			ctx.save();
			ctx.translate(tw, th);
			ctx.rotate(this.degrees * Math.PI / 180);
			
			ctx.drawImage(this.img, 0, 0, this.img.width, this.img.height, -size/2+((size-scaledWidth)/2), -size/2+((size-scaledHeight)/2), scaledWidth, scaledHeight);
			ctx.restore();
//		} else {
//			this.canvas.width  = parseInt(scaledWidth);
//			this.canvas.height = parseInt(scaledHeight);
//			
//			ctx.drawImage(this.img, 0, 0, this.img.width, this.img.height, 0, 0, scaledWidth, scaledHeight);
//		}
		
		this.drawCropContainer();
	};
	
	this.drawCropContainer = function() {
		var ctx = this.canvas.getContext('2d');
		
		var cw = this.canvas.width;
		var ch = this.canvas.height;
		
		if (this.crop.pos2 == null) {
			this.crop.pos2 = { x: cw, y: ch };
		}
		
		var pos1 = this.crop.pos1;
		var pos2 = this.crop.pos2;
		
		ctx.fillStyle = 'rgba(0, 0, 0, 0.5)';
		console.log(pos1);
		
		var img = ctx.getImageData(pos1.x, pos1.y, pos2.x-pos1.x, pos2.y-pos1.y);
		ctx.fillRect(0, 0, cw, ch);
		ctx.putImageData(img, pos1.x, pos1.y);
		
		// rectangle
		ctx.beginPath();
		ctx.fillStyle = 'rgba(0, 0, 0, 0.5)';
		ctx.lineWidth = 3;
		ctx.rect(this.crop.pos1.x, this.crop.pos1.y, this.crop.pos2.x-this.crop.pos1.x, this.crop.pos2.y-this.crop.pos1.y);
		ctx.stroke();
	};
	
	

	this.canvasXY = function(evt) {
		var x = evt.originalEvent.offsetX;
		var y = evt.originalEvent.offsetY;
		
		if (x < 0)
			x = 0;
		else if (x > this.canvas.width)
			x = this.canvas.width;
		
		if (y < 0)
			y=0;
		else if (y > this.canvas.height)
			y = this.canvas.height;

		return {x: x, y: y};
	}
	
	this.canvasMousemove = function(evt) {
		// image not loaded
		if (this.img.complete == false) return;
		
		var mousepos = this.canvasXY(evt);
		
		this.determineCursor( mousepos.x, mousepos.y );
		
		if (this.mousestate.down) {
			
			var diffy = mousepos.y - this.mousestate.y;
			var diffx = mousepos.x - this.mousestate.x;
			
			if (this.mousestate.edge == 't' || this.mousestate.edge == 'tl' || this.mousestate.edge == 'tr') {
				this.crop.pos1.y = this.mousestate.crop.pos1.y + diffy;
			}
			if (this.mousestate.edge == 'tl' || this.mousestate.edge == 'l' || this.mousestate.edge == 'bl') {
				this.crop.pos1.x = this.mousestate.crop.pos1.x + diffx;
			}
			
			if (this.mousestate.edge == 'tr' || this.mousestate.edge == 'r' || this.mousestate.edge == 'br') {
				this.crop.pos2.x = this.mousestate.crop.pos2.x + diffx;
			}
			
			if (this.mousestate.edge == 'bl' || this.mousestate.edge == 'b' || this.mousestate.edge == 'br') {
				this.crop.pos2.y = this.mousestate.crop.pos2.y + diffy;
			}
			
			if (this.crop.pos2.x-10 < this.crop.pos1.x) {
				this.crop.pos2.x = this.crop.pos1.x + 10;
			}
			
			if (this.crop.pos2.y-10 < this.crop.pos1.y) {
				this.crop.pos2.y = this.crop.pos1.y + 10;
			}
			
			this.calculateWidthHeight();
			
			this.drawImage();
		}
		
	};
	
	this.determineCursor = function(x, y) {
		if (this.readonly) return;
		
		var spacing = 8;
		
		// top left
		if (x >= this.crop.pos1.x-spacing && x <= this.crop.pos1.x+spacing && y >= this.crop.pos1.y-spacing && y <= this.crop.pos1.y+spacing) {
			$(this.canvas).css('cursor', 'nwse-resize');
			return 'tl';
		}
		// top right
		else if (x >= this.crop.pos2.x-spacing && x <= this.crop.pos2.x+spacing && y >= this.crop.pos1.y-spacing && y <= this.crop.pos1.y+spacing) {
			$(this.canvas).css('cursor', 'nesw-resize');
			return 'tr';
		}
		// bottom left
		else if (x >= this.crop.pos1.x-spacing && x <= this.crop.pos1.x+spacing && y >= this.crop.pos2.y-spacing && y <= this.crop.pos2.y+spacing) {
			$(this.canvas).css('cursor', 'nesw-resize');
			return 'bl';
		}
		// bottom right
		else if (x >= this.crop.pos2.x-spacing && x <= this.crop.pos2.x+spacing && y >= this.crop.pos2.y-spacing && y <= this.crop.pos2.y+spacing) {
			$(this.canvas).css('cursor', 'nwse-resize');
			return 'br';
		}
		// top
		else if (y >= this.crop.pos1.y-spacing && y <= this.crop.pos1.y+spacing) {
			$(this.canvas).css('cursor', 'ns-resize');
			return 't';
		}
		// right
		else if (x >= this.crop.pos2.x-spacing && x <= this.crop.pos2.x+spacing) {
			$(this.canvas).css('cursor', 'ew-resize');
			return 'r';
		}
		// bottom
		else if (y >= this.crop.pos2.y-spacing && y <= this.crop.pos2.y+spacing) {
			$(this.canvas).css('cursor', 'ns-resize');
			return 'b';
		}
		// left
		else if (x >= this.crop.pos1.x-spacing && x <= this.crop.pos1.x+spacing) {
			$(this.canvas).css('cursor', 'ew-resize');
			return 'l';
		} else {
			$(this.canvas).css('cursor', '');
		}
		
		return null;
	};
	
	this.canvasMousedown = function (evt) {
		if (this.readonly) return;
		
		// only register initial state
		if (this.mousestate.down) {
			return;
		}
		
		var mousepos = this.canvasXY(evt);
		
		var edge = this.determineCursor( mousepos.x, mousepos.y );
		if (edge != null) {
			this.mousestate.down = true;
			this.mousestate.edge = edge;
			this.mousestate.x = mousepos.x;
			this.mousestate.y = mousepos.y;
			
			this.mousestate.crop = {
				pos1: {x: this.crop.pos1.x, y: this.crop.pos1.y},
				pos2: {x: this.crop.pos2.x, y: this.crop.pos2.y},
			}
		}
	};
	
	this.canvasMouseup = function (evt) {
		this.mousestate.down = false;
	};
	
	
	this.setZoom = function(val) {
		val = parseInt(val);
		
		var oldZoom = this.zoom;
		
		if (!isNaN(val)) {
			this.zoom = val;
		}
		
		if (this.zoom != oldZoom) {
			this.crop.pos1.x = this.crop.pos1.x / oldZoom * this.zoom;
			this.crop.pos1.y = this.crop.pos1.y / oldZoom * this.zoom;
			this.crop.pos2.x = this.crop.pos2.x / oldZoom * this.zoom;
			this.crop.pos2.y = this.crop.pos2.y / oldZoom * this.zoom;
		}
		
		this.drawImage();
	};
	
	this.calculateWidthHeight = function() {
		var w = parseInt( $('[name=img_width]').val() );
		var h = parseInt( $('[name=img_height]').val() );
		
		// crop width/height, used for ratio
		var cw = this.crop.pos2.x - this.crop.pos1.x;
		var ch = this.crop.pos2.y - this.crop.pos1.y;

		if (this.fixedSide == 'width') {
			if (isNaN(w))
				return;
			
			h = ch/cw * w;
		} else if (this.fixedSide == 'height') {
			if (isNaN(h))
				return;
			
			w = cw / ch * w;
		} else {
			w = this.img.width * (cw/(this.img.width * this.zoom / 100));
			h = this.img.height * (ch/(this.img.height * this.zoom / 100));
		}
		
		w = parseInt(w);
		h = parseInt(h);
		
		if (isNaN(w) == false && isNaN(h) == false) {
			$('[name=img_width]').val(w);
			$('[name=img_height]').val(h);
		}
		
	};
	
	this.init = function() {
		this.img = document.createElement('img');
		this.img.onload = function() {
			this.imageLoaded();
		}.bind(this);
		this.img.src = this.url;
		
		
		$('.degrees-mutate').click(function(evt) {
			var mutateDegrees = parseInt( $(evt.target).data('degrees') );
			
			this.setDegrees( this.degrees + mutateDegrees );
			
		}.bind(this));
		
		$('[name=degrees]').on('change input', function( evt ){
			this.setDegrees( evt.target.value );
		}.bind(this));
		
		
		$('[name=img_width]').change(function() { this.fixedSide = 'width'; this.calculateWidthHeight(); }.bind(this));
		$('[name=img_height]').change(function() { this.fixedSide = 'height'; this.calculateWidthHeight(); }.bind(this));
	};
	
	
	this.init();
	
}

