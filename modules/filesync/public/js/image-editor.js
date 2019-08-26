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
	
	this.crop = {
		pos1: { x: 0, y: 0 },
		pos2: null
	};
	
	this.mousestate = {
		down: false,
		edge: null
	};
	
	
	this.draw = function() {
		this.drawImage();
		
		this.drawCropContainer();
	};
	
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
		
		ctx.fillStyle = '#fff';
		ctx.rect(0, 0, cw, ch);
		ctx.clearRect(0, 0, 1000, 1000);
		ctx.fill();
		
		var tw = cw/2;
		var th = ch/2;
		ctx.translate(tw, th);
		ctx.rotate(degrees* Math.PI / 180);
		
		ctx.drawImage( this.img, -tw+((cw-w)/2), -th+((ch-h)/2), w, h );
		
		ctx.restore();
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
		// no images drawn yet
		if (this.crop.pos2 == null) return;
		
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
			
			
			this.draw();
		}
		
	};
	
	this.determineCursor = function(x, y) {
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
	
	
	this.init = function() {
		
		var rotationControlContainer = $('<div class="rotation-control" />');
		
		rotationControlContainer.append('<input type="button" value="<<" class="degree-control minus-90" data-value="-90" />');
		rotationControlContainer.append('<input type="button" value="<" class="degree-control minus-1" data-value="-1" />');
		
		this.range = document.createElement('input');
		this.range.type = 'range';
		this.range.min = 0;
		this.range.max = 360;
		this.range.value = 0;
		$(this.range).on('input change', function(evt) {
			this.degrees = evt.target.value
			this.draw();
		}.bind(this));
		$(rotationControlContainer).append( this.range );

		rotationControlContainer.append('<input type="button" value=">" class="degree-control plus-1" data-value="1" />');
		rotationControlContainer.append('<input type="button" value=">>" class="degree-control plus-90" data-value="90" />');
		rotationControlContainer.find('.degree-control').click(function(evt) {
			var v = $(evt.target).data('value');
			var rangeControl = $(evt.target).closest('.rotation-control').find('[type=range]');
			
			var newV = parseInt($(rangeControl).val()) + parseInt(v);
			if (newV < 0) newV = 0;
			if (newV > 360) newV = 360;
			
			$(rangeControl).val( newV );
			$(rangeControl).trigger('change');
			
		}.bind(this));
		$(this.container).append( rotationControlContainer );
		
		
		
		// create canvas
		this.canvas = document.createElement('canvas');
		$(this.canvas).attr('width', '800px');
		$(this.canvas).attr('height', '800px');
		$(this.canvas).css('border', '1px solid #ccc');
		$(this.canvas).css('display', 'block');
		$(this.container).append( this.canvas );
		
		$(this.canvas).mousemove(function(evt) {
			this.canvasMousemove( evt );
		}.bind(this));
		$(this.canvas).mousedown(function(evt) {
			this.canvasMousedown( evt );
		}.bind(this));
		
		$(this.canvas).on('mouseup mouseout', function(evt) {
			this.canvasMouseup( evt );
		}.bind(this));
		
		
		
		// load image
		this.img = document.createElement('img');
		this.img.src = this.opts.image_url;
		this.img.onload = function() {
			var me = this;
			
		    EXIF.getData(this.img, function() {
		        var r = EXIF.getTag(this, "Orientation");
		        
		        console.log( r );
		        me.draw();
		    });
		    
		}.bind(this);
		

	};
	
	
	this.init();
}


