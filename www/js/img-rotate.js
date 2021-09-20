

pageLoaded(function() {
	
	$('img-rotate').each(function(index, node) {
		var ir = new ImgRotate( node );
		$(node).data( 'img-rotate', ir );
		ir.init();
	});
	
});




function widget_imgRotate_left( obj ) {
	var ir = $(obj).closest('div.widget').find('img-rotate').data('img-rotate');
	
	ir.rotateLeft();
}
function widget_imgRotate_right( obj ) {
	var ir = $(obj).closest('div.widget').find('img-rotate').data('img-rotate');
	
	ir.rotateRight();
}





function ImgRotate( obj ) {
	
	this.obj = obj;
	this.img = null;
	this.loaded = false;
	
	this.rotation = 0;
	
	
	this.rotateRight = function() {
		var r = this.rotation;
		r = r + 90;
		if (r%90 != 0) r = 0;
		if (r >= 360) r = 0;
		
		this.rotation = r;
		
		this.drawImage();
		
		this.saveRotation();
	}
	
	this.rotateLeft = function() {
		var r = this.rotation;
		
		r = r - 90;
		if (r%90 != 0) r = 0;
		if (r < 0) r = 270;
		
		this.rotation = r;
		
		this.drawImage();
		
		this.saveRotation();
	}
	
	
	this.drawImage = function() {
		if ( this.loaded == false ) return;
		
		if ($(this.obj).data('fullsize') && this.rotation == 0) {
			// just insert <img />
			if ($(this.obj).find('img').length == 0) {
				var img = $('<img />');
				img.attr( 'src', $(this.obj).attr('src') );
				$(this.obj).append( img );
			}
			return;
		}
			
		
		$(this.obj).css('display', 'block');
		
		
		var canvas = null;
		if ($(this.obj).find('canvas').length == 0) {
			canvas = document.createElement('canvas');
			$(canvas).css('max-width', '100%');
			$(canvas).css('width', '100%');
			
			$(this.obj).append( canvas );
		}
		canvas = $(this.obj).find('canvas').get(0);
		
		var containerWidth = $(this.obj).outerWidth( true );
		
		var imgW = this.img.naturalWidth;
		var imgH = this.img.naturalHeight;
		
		
		// determine canvasWidth
		var canvasWidth = containerWidth;
		
		// fullsize-flag set?
		if ($(this.obj).data('fullsize')) {
			canvasWidth = imgW;
			$(canvas).css('width', canvasWidth);
			$(canvas).css('max-width', '');
		}
		
		
		// determine canvasHeight
		var canvasHeight;
		if (this.rotation == 90 || this.rotation == 270) {
			canvasHeight = imgW * canvasWidth / imgH;
		}
		else {
			canvasHeight = imgH * canvasWidth / imgW;
		}
		
		
		// handle rotation
//		if (this.rotation == 90 || this.rotation == 270) {
//			var tmp = canvasWidth;
//			canvasWidth = canvasHeight;
//			canvasHeight = tmp;
//		}
		
		canvas.width = canvasWidth;
		canvas.height = canvasHeight;
		
		var ctx = canvas.getContext('2d');
//		ctx.imageSmoothingEnabled = true;
//		ctx.imageSmoothingQuality = 'high';
//   ctx.webkitImageSmoothingEnabled = false;
//   ctx.mozImageSmoothingEnabled = false;


		ctx.save();
		if (this.rotation == 90) {
			ctx.translate( canvasWidth, 0);
			ctx.rotate( 90 * Math.PI/180 );
			ctx.drawImage(this.img, 0, 0, imgW, imgH, 0, 0, canvasHeight, canvasWidth);
		}
		else if (this.rotation == 180) {
			ctx.translate( canvasWidth, canvasHeight );
			ctx.rotate( 180 * Math.PI/180 );
			ctx.drawImage(this.img, 0, 0, imgW, imgH, 0, 0, canvasWidth, canvasHeight);
		}
		else if (this.rotation == 270) {
			ctx.translate( 0, canvasHeight);
			ctx.rotate( 270 * Math.PI/180 );
			ctx.drawImage(this.img, 0, 0, imgW, imgH, 0, 0, canvasHeight, canvasWidth);
		}
		// default
		else {
			ctx.drawImage(this.img, 0, 0, imgW, imgH, 0, 0, canvasWidth, canvasHeight);
		}
		ctx.restore();
		
	};
	
	
	
	this.saveRotation = function() {
		var ref = $(this.obj).data( 'ref' );
		if (!ref)
			return;
		
		$.ajax({
			type: 'POST',
			url: appUrl('/?m=base&c=js/state&a=save_img_rotate'),
			data: {
				ref: ref,
				rotation: this.rotation
			}
		});
	}
	
	
	
	
	this.init = function() {
		var r = parseInt( $(this.obj).data('rotation') );
		if (isNaN(r) == false && r == 90 || r == 180 || r == 270) {
			this.rotation = r;
		}
		
		
		this.img = new Image();
		this.img.onload = function() {
			this.loaded = true;
			this.drawImage();
		}.bind(this);
		this.img.src = $(this.obj).attr('src');
		
		
		// redraw on resize
		$(window).on('resize', function() {
			this.drawImage();
		}.bind(this));
		
	};
	
	
}



