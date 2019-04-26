/**
 * 
 */



function RentalMap(divContainer, rentalMap, rentalMapItems, opts) {
	
	this.divContainer = $(divContainer);
	this.rentalMap = rentalMap;
	this.rentalMap.width = parseInt(this.rentalMap.width);
	this.rentalMap.height = parseInt(this.rentalMap.height);
	
	this.rentalMapItems = rentalMapItems;			// Note: these are ALL items available for rental
	this.rentalObjects = [ ];
	
	// input fields start/end
	this.inputStartDate = null;
	this.inputEndDate = null;
	
	
	this.opts = opts ? opts : { };
	if (typeof this.opts.editmode == 'undefined') this.opts.editmode = false;
	
	this.canvas = null;
	this.context = null;
	
	this.objectDrag = null;
	
	this.render = function() {
		
		this.context.clearRect(0, 0, this.context.canvas.width, this.context.canvas.height);
		
		for(var i in this.rentalObjects) {
			var ro = this.rentalObjects[i];
			
			this.renderObject( ro );
		}
	};
	
	this.renderObject = function(ro) {
		
		if (!ro.drawObject) {
			
			if (ro.object_type) {
				if (ro.object_type == 'RentalMapSquare1')
					ro.drawObject = new RentalMapSquare1(this, ro);
				if (ro.object_type == 'RentalMapSquare2')
					ro.drawObject = new RentalMapSquare2(this, ro);
				if (ro.object_type == 'RentalMapSquare3')
					ro.drawObject = new RentalMapSquare3(this, ro);
				if (ro.object_type == 'RentalMapSquare4')
					ro.drawObject = new RentalMapSquare4(this, ro);
				if (ro.object_type == 'RentalMapCircle1')
					ro.drawObject = new RentalMapCircle1(this, ro);
				if (ro.object_type == 'RentalMapCircle2')
					ro.drawObject = new RentalMapCircle2(this, ro);
				if (ro.object_type == 'RentalMapCircle3')
					ro.drawObject = new RentalMapCircle3(this, ro);
				if (ro.object_type == 'RentalMapCircle4')
					ro.drawObject = new RentalMapCircle4(this, ro);
				if (ro.object_type == 'RentalMapStairs1')
					ro.drawObject = new RentalMapStairs1(this, ro);
			}
			
			// fallback
			if (!ro.drawObject)
				ro.drawObject = new RentalMapSquare(this, ro);
		}

		return ro.drawObject.draw();
	}
	
	this.findSelectedObject = function(evt) {
		for(var i=this.rentalObjects.length-1; i >= 0; i--) {
			var ro = this.rentalObjects[i];
			
			ro.drawObject.drawPath();
			var bln = this.context.isPointInPath(evt.offsetX, evt.offsetY);
			if (bln) {
				return ro;
			}
		}

		return null;
	}
	
	this.mousedown = function(evt) {
		if (this.opts.editmode == false)
			return;
		
		var obj = this.findSelectedObject(evt);
		
		if (obj) {
			this.objectDrag = {
					object: obj,
					startX: evt.offsetX,
					startY: evt.offsetY,
					objStartX: obj.posX,
					objStartY: obj.posY
			};
		}
	};
	
	this.mousemove = function(evt) {
		var blnPointer = false;
		
		if (this.objectDrag != null) {
			var startX = this.objectDrag.startX;
			var startY = this.objectDrag.startY;
			
			var diffX = evt.offsetX - startX;
			var diffY = evt.offsetY - startY;
			
			var newX = this.objectDrag.objStartX + diffX;
			var newY = this.objectDrag.objStartY + diffY;

			this.objectDrag.object.posX = newX;
			this.objectDrag.object.posY = newY;
			
			for(var i in this.rentalObjects) {
				var ro = this.rentalObjects[i];
				
				// skip ourself
				if (ro.article_id == this.objectDrag.object.article_id)
					continue;
				
				var val = this.objectDrag.object.drawObject.intersectObjects(ro);
				if (val && val.x && val.y) {
					newX = val.x;
					newY = val.y;

					this.objectDrag.object.posX = newX;
					this.objectDrag.object.posY = newY;
				}
				
			}
			
			this.render();
			
			blnPointer = true;
		} else {
			var obj = this.findSelectedObject(evt);
			if (obj != null)
				blnPointer = true;
		}
		
		if (blnPointer)
			$(this.canvas).css('cursor', 'pointer');
		else
			$(this.canvas).css('cursor', 'auto');
	};
	
	
	this.mouseup = function(evt) {
		this.objectDrag = null;
	};
	
	this.mouseclick = function(evt) {
		var ro = this.findSelectedObject(evt);
		
		if (ro && ro.article_id)
			this.selectObject(ro.article_id);
		else
			this.deselectAll();
		
	};
	
	this.deselectAll = function() {
		
		$('ul.floorground-article-container li.article').removeClass('selected');
		
		for(var i in this.rentalObjects) {
			this.rentalObjects[i].selected = false;
		}
		
		this.render();
	}
	
	this.selectObject = function( article_id ) {
		this.deselectAll();
		
		if (article_id) {
			for(var i in this.rentalObjects) {
				if (this.rentalObjects[i].article_id == article_id) {
					this.rentalObjects[i].selected = true;
					$('li.article-'+this.rentalObjects[i].article_id).addClass('selected');
					this.render();
					return;
				}
			}
		}
	}
	
	this.article_idsObjects = function() {
		var ids = [];
		for(var i in this.rentalObjects) {
			ids.push( this.rentalObjects[i].article_id );
		}
		return ids;
	};
	
	
	this.selectedObject = function() {
		for(var i in this.rentalObjects) {
			if (this.rentalObjects[i].selected) {
				return this.rentalObjects[i];
			}
		}
		
		return null;
	}
	
	this.mousedblclick = function(evt) {
		var ro = this.findSelectedObject(evt);
		
		// nothign selected?
		if (typeof ro == 'undefined')
			return;
		
		if (this.opts.editmode) {
			objRentalMap.addRentalObject_Click( ro );
			return;
		}
		
		if (!this.opts.editmode) {
			this.dialogRental(ro);
		}
	};
	
	this.articlePopup = function(articleId) {
		for(var i in this.rentalObjects) {
			var ro = this.rentalObjects[i];
			
			if (ro.article_id == articleId) {
				this.dialogRental( ro );
				break;
			}
		}
	};
	
	this.dialogRental = function(ro) {
		
		$.ajax({
			url: appUrl('/?m=rental&c=rental&a=popup'),
			data: {
				article_id: ro.article_id,
				peildatum: $('.rental-peildatum').val()
			},
			success: function(data, xhr, evt) {
				showDialog({
					title: ro.article_name,
					html: data,
					showCancelSave: false
				});
			}
		});
	};
	
	
	this.initCanvas = function() {
		var width = $(this.divContainer).width();
		console.log(this.divContainer.width());
		var height = parseInt( width / this.rentalMap.width * this.rentalMap.length );
		
		// height larger then window height?
		var maxHeight = $(window).height() - $(this.divContainer).offset().top - 20;
		if (height > maxHeight) {
			height = maxHeight;
			width = height / this.rentalMap.length * this.rentalMap.width;
		}
		
		this.canvas = $('<canvas />');
		this.canvas.attr('width', width);
		this.canvas.attr('height', height);
		this.canvas.css('border', '1px solid #ccc');
		
		$(this.divContainer).append(this.canvas);
		
		
		var me = this;
		$(this.canvas).mousedown(function(evt) { me.mousedown(evt); });
		$(this.canvas).mousemove(function(evt) { me.mousemove(evt); });
		$(this.canvas).mouseup(function(evt) { me.mouseup(evt); });
		$(this.canvas).mouseout(function(evt) { me.mouseup(evt); });
		$(this.canvas).click(function(evt) { me.mouseclick(evt); });
		$(this.canvas).dblclick(function(evt) { me.mousedblclick(evt); });
		
		this.context = this.canvas.get(0).getContext("2d");
	};
	
	
	this.initArticles = function() {
		for(var i in this.rentalMapItems) {
			var rmi = this.rentalMapItems[i];
			
			this.addRentalObject(rmi);
		}
	};
	
	
	/**
	 * renderActionsBar - renders action bar for units
	 */
	this.renderActionsBar = function() {
		var me = this;
		
		var c = $('<div style="margin-left: 165px; font-size: 28px;" />');
		
		c.append('<a class="delete-article" href="javascript:void(0);" title="Verwijder geselecteerd object van plattegrond"><span class="glyphicon glyphicon-trash"></span></a>');
		c.find('.delete-article').click(function() {
			var obj = me.selectedObject();
			
			if (!obj) {
				alert('Geen object geselecteerd');
				return;
			}
			
			me.removeRentalObject(obj);
		});
		
		$(this.divContainer).append( c );
	};
	
	/**
	 * renderItemList - renders list of items in floorground
	 */
	this.renderItemList = function() {
		var me = this;
		
		var c = $('<div style="float: left; width: 165px;" />');
		
		if (this.opts.maps) {
			var mapSelect = $('<select name="id" />');
			mapSelect.change(function() {
				window.location = appUrl('/?m=rental&c=rentalMapEditor&id=' + $(this).val());
			});
			for(var i in this.opts.maps) {
				var opt = $('<option />');
				opt.val( this.opts.maps[i].map_id );
				opt.text( this.opts.maps[i].map_name );
				
				if (this.opts.mapId == this.opts.maps[i].map_id) {
					opt.attr('selected', 'selected');
				}
				
				mapSelect.append( opt );
			}
			c.append( mapSelect );
		}
		
		c.append('<div style="font-weight: bold;">Objecten</div>');
		var ul = $('<ul class="floorground-article-container" />');
		
		for(var i in this.rentalMapItems) {
			this._addItemToList( ul, this.rentalMapItems[i] );
		}
		
		
		c.append( ul );
		
		$(this.divContainer).append( c );
		
		
	};
	
	/**
	 * _addItemToList - add's item to <ul>
	 */
	this._addItemToList = function( ulContainer, rentalItem ) {
		
		// already in list? happends if width/length is edited..
		if ($(ulContainer).find('.article-' + rentalItem.article_id).length > 0)
			return;
		
		var me = this;
		
		var li = $('<li />');
		li.addClass('article-' + rentalItem.article_id);
		li.addClass('article');
		
		var a = $('<a />');
		a.attr('href', 'javascript:void(0);');
		a.data('item', rentalItem);
		a.text( rentalItem.article_name );
		a.click(function() {
			var i = $(this).data('item');
			
			me.selectObject( i.article_id );
		});
		
		li.append(a);
		ulContainer.append( li );
	}
	
	
	
	
	this.addRentalObject_Click = function(rentalItem) {
		var me = this;
		
		var html = '';
		html += '<div class="add-rental-object">';
		html += '<div><label style="width: 6em;">Lengte</label> <input type="text" name="length" /></div>';
		html += '<div><label style="width: 6em;">Breedte</label> <input type="text" name="width" /></div>';
		html += '</div>';
		
		
		
		showConfirmation(rentalItem.article_name, html, function() {
			var length = strtodouble( $('.add-rental-object input[name=length]').val(), 0 );
			var width  = strtodouble( $('.add-rental-object input[name=width]').val(), 0 );
			
			if (length <= 0) {
				alert('Onjuiste lengte opgegeven');
				return false;
			}
			
			if (width <= 0) {
				alert('Onjuiste breedte opgegeven');
				return false;
			}
			
			rentalItem.length = length;
			rentalItem.width = width;
			
			me.addRentalObject(rentalItem);
			me.render();
			
			me._addItemToList( $('ul.floorground-article-container'), rentalItem );
		});
		
		
		if (rentalItem.length)
			$('.add-rental-object [name=length]').val( rentalItem.length );
		if (rentalItem.width)
			$('.add-rental-object [name=width]').val( rentalItem.width );
		
		
	};
	
	this.containsRentalObject = function(article_id) {
		for(var i in this.rentalObjects) {
			if (this.rentalObjects[i].article_id == article_id) {
				return true;
			}
		}
		
		return false;
	};
	
	this.addRentalObject = function(rentalItem) {
		if (this.containsRentalObject(rentalItem.article_id))
			return false;
		
		rentalItem.length = parseInt(rentalItem.length);
		rentalItem.width = parseInt(rentalItem.width);
		
		this.rentalObjects.push( rentalItem );
		this.render();
	};
	
	this.removeRentalObject = function(rentalItem) {
		console.log('removeRentalObject');
		console.log(rentalItem);
		if (!rentalItem)
			return;
		
		var article_id = rentalItem.article_id;
		
		var pos = -1;
		for(var x=0; x < this.rentalObjects.length; x++) {
			if (this.rentalObjects[x].article_id == article_id) {
				pos = x;
				break;
			}
		}
		
		if (pos != -1) {
			this.rentalObjects.splice(x, 1);
			$('.floorground-article-container .article-' + article_id).remove();
		}
		
		this.render();
	};
	
	
	
	this.init = function() {
		
		if (this.opts.editmode) {
			this.renderActionsBar();
			
			this.renderItemList();
		}

		this.initCanvas();
		
		this.initArticles();
		
		
		this.render();
		
	};
	
	
	
	this.init();
}


function RentalMapSquare(rentalMap, ro) {
	this.name = 'RentalMapSquare';
	this.rentalMap = rentalMap;
	this.ro = ro;
}

RentalMapSquare.prototype.getBackgroundColor = function() {
	// draw background
	var backgroundColor = '#fff';
	
	if (this.ro.rented && this.ro.has_termination_date) {
		if (this.ro.has_future_contract) {
			backgroundColor = '#40ff88';
		} else {
			backgroundColor = '#ffb65b';
		}
	} else if (this.ro.rented) {
		backgroundColor = '#40ff88';
	} else if (this.ro.has_future_contract) {
		backgroundColor = '#fff81f';
	}

	return backgroundColor;
};

RentalMapSquare.prototype.drawPath = function() {
	// fetch x/y
	var posX = parseFloat(this.ro.left);
	var posY = parseFloat(this.ro.top);
	if (isNaN(posX)) posX = 0;
	if (isNaN(posY)) posY = 0;
	
	var roLength = parseFloat( this.ro.length );
	var roWidth = parseFloat( this.ro.width );
	
	if (isNaN(roLength) || isNaN(roWidth)) {
		console.log('Invalid length/width ' + this.ro);
		return false;
	}
	
	var w = roWidth * ($(this.rentalMap.canvas).width() / this.rentalMap.rentalMap.width);
	var h = roLength * ( $(this.rentalMap.canvas).height() / this.rentalMap.rentalMap.length);
	
	if (typeof this.ro.posX == 'undefined' || this.ro.posY == 'undefined') {
		var x = posX * ($(this.rentalMap.canvas).width() / this.rentalMap.rentalMap.width);
		var y = posY * ($(this.rentalMap.canvas).height() / this.rentalMap.rentalMap.length);

		this.ro.posX = x;
		this.ro.posY = y;
	}
	
	this.rentalMap.context.beginPath();
	this.rentalMap.context.rect(this.ro.posX, this.ro.posY, w, h);
	
	return {x: this.ro.posX, y: this.ro.posY, w: w, h: h};
};

RentalMapSquare.prototype.draw = function() {
	
	var pos = this.drawPath();
	
	// draw box
	if (this.ro.selected) {
		this.rentalMap.context.lineWidth = '1px';
		this.rentalMap.context.fillStyle = '#ceeeff';
		this.rentalMap.context.fill();
	} else {
		var backgroundColor = this.getBackgroundColor();
		
		this.rentalMap.context.save();
		this.rentalMap.context.lineWidth = '1px';
		this.rentalMap.context.fillStyle = backgroundColor;
		this.rentalMap.context.fill();
		
		// rentable gap avaiable? => draw stripes
		if (this.ro.has_rentable_gap) {
			var pos = this.drawPath();
			this.rentalMap.context.clip();
			
			var stripeWidth = 20;
			for(var stripe=-stripeWidth; stripe <pos.w+stripeWidth; stripe += stripeWidth) {
				this.rentalMap.context.strokeStyle = '#fff';
				this.rentalMap.context.lineWidth = '5';
				
				this.rentalMap.context.beginPath();       // Start a new path
				this.rentalMap.context.moveTo(pos.x+(stripe+stripeWidth+10), pos.y);    // Move the pen to (30, 50)
				this.rentalMap.context.lineTo(pos.x+(stripe-20), pos.y+(pos.h*2));  // Draw a line to (150, 100)
				this.rentalMap.context.stroke();          // Render the path
			}
		}
		this.rentalMap.context.restore();
	}
	
	var pos = this.drawPath();
	this.rentalMap.context.lineWidth = '1px';
	this.rentalMap.context.strokeStyle = 'black';
	this.rentalMap.context.stroke();
	
	// draw unit name
	var m = this.rentalMap.context.measureText(this.ro.article_name);		// TextMetrics
	
	var center = this.center ? this.center : pos.w/2;
	
	var textX = (pos.x + center) - (m.width / 2);
	var textY = (pos.y + pos.h/2) + 6;
	this.rentalMap.context.fillStyle = 'black';
	this.rentalMap.context.fillText(this.ro.article_name, textX, textY);
	
	return true;
}

RentalMapSquare.prototype.intersectObjects = function(rentalObject) {
	if (this.name != 'RentalMapSquare' || rentalObject.drawObject.name != 'RentalMapSquare') {
		return false;
	}
	
	var obj = rentalObject.drawObject.ro;
	
	var m = 5;		// margin in px
	
	// dragging object
	var x1 = this.ro.posX;
	var w1 = parseFloat(this.ro.width) * ($(this.rentalMap.canvas).width() / this.rentalMap.rentalMap.width);
	var y1 = this.ro.posY;
	var l1 = parseFloat(this.ro.length) * ($(this.rentalMap.canvas).width() / this.rentalMap.rentalMap.width);
	
	// other
	var x2 = obj.posX;
	var w2 = parseFloat(obj.width) * ($(this.rentalMap.canvas).width() / this.rentalMap.rentalMap.width);
	var y2 = obj.posY;
	var l2 = parseFloat(obj.length) * ($(this.rentalMap.canvas).width() / this.rentalMap.rentalMap.width);
	
	
	var hitX, hitY;
	
	// right side dragging object hits left side object
	if (x1+w1 >= x2-m && x1+w1 <= x2+m) {
		console.log('right hit');
		hitX = x2-w1;
	}
	// left side dragging object hits right side object 
	else if (x1 >= (x2+w2)-m && x1 <= (x2+w2)+m) {
		console.log('left hit');
		hitX = x2+w2;
	}
	// left side dragging object hits left side object
	else if (x1 >= x2-m && x1 <= x2+m) {
		hitX = x2;
	}
	// right side dragging object rights right side object
	else if (x1+w1 >= (x2+w2)-m && x1+w1 <= (x2+w2)+m) {
		hitX = (x2+w2) - w1;
	}
	// edges touch each other
	else if ((x1 <= x2 && x1+w1 >= x2) || (x1 >= x2 && x1 <= x2+w2) || (x1 <= x2 && x1+w1 >= x2+w2)) {
		hitX = x1;
	}
	
	
	// top dragging object hits top object
	if (y1 >= y2-m && y1 <= y2+m) {
		hitY = y2;
	}
	// bottom dragging object hits top object
	else if (y1+l1 >= y2-m && y1+l1 <= y2+m) {
		hitY = y2-l1;
	}
	// top dragging object hits bottom object
	else if (y1 >= (y2+l2)-m && y1 <= (y2+l2)+m) {
		hitY = y2+l2;
	}
	// bottom dragging object hits bottom object
	else if (y1+l1 >= (y2+l2)-m && y1+l1 <= (y2+l2)+m) {
		hitY = (y2+l2)-l1;
	}
	// edges touch each other
	else if ((y1 <= y2 && y1+l1 >= y2) || (y1 >= y2 && y1 <= y2 + l2) || (y1 <= y2 && y1+l1 >= y2+l2)) {
		hitY = y1;
	}
	
	if (hitX && hitY) {
		return {
			x: hitX,
			y: hitY
		};
	}
	
	return;
};

function RentalMapCircle1(rentalMap, ro) {
	this.name = 'RentalMapCircle1';
	
	this.rentalMap = rentalMap;
	this.ro = ro;
}
RentalMapCircle1.prototype = new RentalMapSquare();
RentalMapCircle1.prototype.drawPath = function() {
	var posX = parseFloat(this.ro.left);
	var posY = parseFloat(this.ro.top);
	if (isNaN(posX)) posX = 0;
	if (isNaN(posY)) posY = 0;
	
	var roLength = parseFloat( this.ro.length );
	var roWidth = parseFloat( this.ro.width );
	
	if (isNaN(roLength) || isNaN(roWidth)) {
		console.log('Invalid length/width ' + this.ro);
		return false;
	}
	
	var w = roWidth * ($(this.rentalMap.canvas).width() / this.rentalMap.rentalMap.width);
	var h = roLength * ( $(this.rentalMap.canvas).height() / this.rentalMap.rentalMap.length);
	
	if (typeof this.ro.posX == 'undefined' || this.ro.posY == 'undefined') {
		var x = posX * ($(this.rentalMap.canvas).width() / this.rentalMap.rentalMap.width);
		var y = posY * ($(this.rentalMap.canvas).height() / this.rentalMap.rentalMap.length);

		this.ro.posX = x;
		this.ro.posY = y;
	}
	
	this.rentalMap.context.beginPath();
	this.rentalMap.context.ellipse(this.ro.posX+w, this.ro.posY+h, w, h, 0, -Math.PI/2, Math.PI, true);
	
	this.rentalMap.context.lineTo(this.ro.posX+w, this.ro.posY+h);
	this.rentalMap.context.lineTo(this.ro.posX+w, this.ro.posY);
	
	this.center = w/2 + w/20;
	
	return {x: this.ro.posX, y: this.ro.posY, w: w, h: h};	
}


function RentalMapCircle2(rentalMap, ro) {
	this.name = 'RentalMapCircle2';
	this.rentalMap = rentalMap;
	this.ro = ro;
}
RentalMapCircle2.prototype = new RentalMapSquare();
RentalMapCircle2.prototype.drawPath = function() {
	var posX = parseFloat(this.ro.left);
	var posY = parseFloat(this.ro.top);
	if (isNaN(posX)) posX = 0;
	if (isNaN(posY)) posY = 0;
	
	var roLength = parseFloat( this.ro.length );
	var roWidth = parseFloat( this.ro.width );
	
	if (isNaN(roLength) || isNaN(roWidth)) {
		console.log('Invalid length/width ' + this.ro);
		return false;
	}
	
	var w = roWidth * ($(this.rentalMap.canvas).width() / this.rentalMap.rentalMap.width);
	var h = roLength * ( $(this.rentalMap.canvas).height() / this.rentalMap.rentalMap.length);
	
	if (typeof this.ro.posX == 'undefined' || this.ro.posY == 'undefined') {
		var x = posX * ($(this.rentalMap.canvas).width() / this.rentalMap.rentalMap.width);
		var y = posY * ($(this.rentalMap.canvas).height() / this.rentalMap.rentalMap.length);

		this.ro.posX = x;
		this.ro.posY = y;
	}
	
	this.rentalMap.context.beginPath();
	this.rentalMap.context.ellipse(this.ro.posX+w, this.ro.posY, w, h, 0, -Math.PI, Math.PI/2, true);
	
	this.rentalMap.context.lineTo(this.ro.posX+w, this.ro.posY);
	this.rentalMap.context.lineTo(this.ro.posX, this.ro.posY);
	
	this.center = w/2 + w/20;
	
	return {x: this.ro.posX, y: this.ro.posY, w: w, h: h};	
}


function RentalMapCircle3(rentalMap, ro) {
	this.name = 'RentalMapCircle3';
	this.rentalMap = rentalMap;
	this.ro = ro;
}
RentalMapCircle3.prototype = new RentalMapSquare();
RentalMapCircle3.prototype.drawPath = function() {
	var posX = parseFloat(this.ro.left);
	var posY = parseFloat(this.ro.top);
	if (isNaN(posX)) posX = 0;
	if (isNaN(posY)) posY = 0;
	
	var roLength = parseFloat( this.ro.length );
	var roWidth = parseFloat( this.ro.width );
	
	if (isNaN(roLength) || isNaN(roWidth)) {
		console.log('Invalid length/width ' + this.ro);
		return false;
	}
	
	var w = roWidth * ($(this.rentalMap.canvas).width() / this.rentalMap.rentalMap.width);
	var h = roLength * ( $(this.rentalMap.canvas).height() / this.rentalMap.rentalMap.length);
	
	if (typeof this.ro.posX == 'undefined' || this.ro.posY == 'undefined') {
		var x = posX * ($(this.rentalMap.canvas).width() / this.rentalMap.rentalMap.width);
		var y = posY * ($(this.rentalMap.canvas).height() / this.rentalMap.rentalMap.length);

		this.ro.posX = x;
		this.ro.posY = y;
	}
	
	this.rentalMap.context.beginPath();
	this.rentalMap.context.ellipse(this.ro.posX, this.ro.posY, w, h, 0, Math.PI/2, 0, true);
	
	this.rentalMap.context.lineTo(this.ro.posX, this.ro.posY);
	this.rentalMap.context.lineTo(this.ro.posX, this.ro.posY+h);
	
	this.center = w/2 - w/20;
	
	return {x: this.ro.posX, y: this.ro.posY, w: w, h: h};	
}


function RentalMapCircle4(rentalMap, ro) {
	this.name = 'RentalMapCircle4';
	this.rentalMap = rentalMap;
	this.ro = ro;
}
RentalMapCircle4.prototype = new RentalMapSquare();
RentalMapCircle4.prototype.drawPath = function() {
	var posX = parseFloat(this.ro.left);
	var posY = parseFloat(this.ro.top);
	if (isNaN(posX)) posX = 0;
	if (isNaN(posY)) posY = 0;
	
	var roLength = parseFloat( this.ro.length );
	var roWidth = parseFloat( this.ro.width );
	
	if (isNaN(roLength) || isNaN(roWidth)) {
		console.log('Invalid length/width ' + this.ro);
		return false;
	}
	
	var w = roWidth * ($(this.rentalMap.canvas).width() / this.rentalMap.rentalMap.width);
	var h = roLength * ( $(this.rentalMap.canvas).height() / this.rentalMap.rentalMap.length);
	
	if (typeof this.ro.posX == 'undefined' || this.ro.posY == 'undefined') {
		var x = posX * ($(this.rentalMap.canvas).width() / this.rentalMap.rentalMap.width);
		var y = posY * ($(this.rentalMap.canvas).height() / this.rentalMap.rentalMap.length);

		this.ro.posX = x;
		this.ro.posY = y;
	}
	
	this.rentalMap.context.beginPath();
	this.rentalMap.context.ellipse(this.ro.posX, this.ro.posY+h, w, h, 0, 0, -Math.PI/2, true);
	
	this.rentalMap.context.lineTo(this.ro.posX, this.ro.posY+h);
	this.rentalMap.context.lineTo(this.ro.posX+w, this.ro.posY+h);
	
	this.center = w/2 - w/20;
	
	return {x: this.ro.posX, y: this.ro.posY, w: w, h: h};	
}




function RentalMapSquare1(rentalMap, ro) {
	this.name = 'RentalMapSquare1';
	this.rentalMap = rentalMap;
	this.ro = ro;
}
RentalMapSquare1.prototype = new RentalMapSquare();
RentalMapSquare1.prototype.drawPath = function() {
	var posX = parseFloat(this.ro.left);
	var posY = parseFloat(this.ro.top);
	if (isNaN(posX)) posX = 0;
	if (isNaN(posY)) posY = 0;
	
	var roLength = parseFloat( this.ro.length );
	var roWidth = parseFloat( this.ro.width );
	
	if (isNaN(roLength) || isNaN(roWidth)) {
		console.log('Invalid length/width ' + this.ro);
		return false;
	}
	
	var w = roWidth * ($(this.rentalMap.canvas).width() / this.rentalMap.rentalMap.width);
	var h = roLength * ( $(this.rentalMap.canvas).height() / this.rentalMap.rentalMap.length);
	
	if (typeof this.ro.posX == 'undefined' || this.ro.posY == 'undefined') {
		var x = posX * ($(this.rentalMap.canvas).width() / this.rentalMap.rentalMap.width);
		var y = posY * ($(this.rentalMap.canvas).height() / this.rentalMap.rentalMap.length);

		this.ro.posX = x;
		this.ro.posY = y;
	}
	
	
	this.rentalMap.context.beginPath();
//	this.rentalMap.context.rect(this.ro.posX, this.ro.posY, w, h);
	
//	var skew = 20;
	var skew = Math.sin(Math.PI/180*5)*h;
	
	// top line
	this.rentalMap.context.moveTo(this.ro.posX, this.ro.posY);
	this.rentalMap.context.lineTo(this.ro.posX+w, this.ro.posY);
	
	// right side
	this.rentalMap.context.lineTo(this.ro.posX+w+skew, this.ro.posY+h);
	
	// bottom
//	this.rentalMap.context.lineTo(this.ro.posX+w, this.ro.posY+h);
	this.rentalMap.context.lineTo(this.ro.posX+skew, this.ro.posY+h);
	this.rentalMap.context.lineTo(this.ro.posX, this.ro.posY);
	
	this.center = w/2 + skew/2;
	
	return {x: this.ro.posX, y: this.ro.posY, w: w, h: h};	
}



function RentalMapSquare2(rentalMap, ro) {
	this.name = 'RentalMapSquare2';
	this.rentalMap = rentalMap;
	this.ro = ro;
}
RentalMapSquare2.prototype = new RentalMapSquare();
RentalMapSquare2.prototype.drawPath = function() {
	var posX = parseFloat(this.ro.left);
	var posY = parseFloat(this.ro.top);
	if (isNaN(posX)) posX = 0;
	if (isNaN(posY)) posY = 0;
	
	var roLength = parseFloat( this.ro.length );
	var roWidth = parseFloat( this.ro.width );
	
	if (isNaN(roLength) || isNaN(roWidth)) {
		console.log('Invalid length/width ' + this.ro);
		return false;
	}
	
	var w = roWidth * ($(this.rentalMap.canvas).width() / this.rentalMap.rentalMap.width);
	var h = roLength * ( $(this.rentalMap.canvas).height() / this.rentalMap.rentalMap.length);
	
	if (typeof this.ro.posX == 'undefined' || this.ro.posY == 'undefined') {
		var x = posX * ($(this.rentalMap.canvas).width() / this.rentalMap.rentalMap.width);
		var y = posY * ($(this.rentalMap.canvas).height() / this.rentalMap.rentalMap.length);

		this.ro.posX = x;
		this.ro.posY = y;
	}
	
	
	this.rentalMap.context.beginPath();
//	this.rentalMap.context.rect(this.ro.posX, this.ro.posY, w, h);
	
//	var skew = 20;
	var skew = Math.sin(Math.PI/180*35)*h;
	
	// top line
	this.rentalMap.context.moveTo(this.ro.posX, this.ro.posY);
	this.rentalMap.context.lineTo(this.ro.posX+w, this.ro.posY);
	
	// right side
	this.rentalMap.context.lineTo(this.ro.posX+w+skew, this.ro.posY+h);
	
	// bottom
//	this.rentalMap.context.lineTo(this.ro.posX+w, this.ro.posY+h);
	this.rentalMap.context.lineTo(this.ro.posX, this.ro.posY+h);
	this.rentalMap.context.lineTo(this.ro.posX, this.ro.posY);
	
	this.center = w/2 + skew/6;
	
	return {x: this.ro.posX, y: this.ro.posY, w: w, h: h};	
}



function RentalMapSquare3(rentalMap, ro) {
	this.name = 'RentalMapSquare3';
	this.rentalMap = rentalMap;
	this.ro = ro;
}
RentalMapSquare3.prototype = new RentalMapSquare();
RentalMapSquare3.prototype.drawPath = function() {
	var posX = parseFloat(this.ro.left);
	var posY = parseFloat(this.ro.top);
	if (isNaN(posX)) posX = 0;
	if (isNaN(posY)) posY = 0;
	
	var roLength = parseFloat( this.ro.length );
	var roWidth = parseFloat( this.ro.width );
	
	if (isNaN(roLength) || isNaN(roWidth)) {
		console.log('Invalid length/width ' + this.ro);
		return false;
	}
	
	var w = roWidth * ($(this.rentalMap.canvas).width() / this.rentalMap.rentalMap.width);
	var h = roLength * ( $(this.rentalMap.canvas).height() / this.rentalMap.rentalMap.length);
	
	if (typeof this.ro.posX == 'undefined' || this.ro.posY == 'undefined') {
		var x = posX * ($(this.rentalMap.canvas).width() / this.rentalMap.rentalMap.width);
		var y = posY * ($(this.rentalMap.canvas).height() / this.rentalMap.rentalMap.length);

		this.ro.posX = x;
		this.ro.posY = y;
	}
	
	
	this.rentalMap.context.beginPath();
//	this.rentalMap.context.rect(this.ro.posX, this.ro.posY, w, h);
	
//	var skew = 20;
	var skew = Math.sin(Math.PI/180*35)*h;
	
	// top line
	this.rentalMap.context.moveTo(this.ro.posX, this.ro.posY);
	this.rentalMap.context.lineTo(this.ro.posX+w, this.ro.posY);
	
	// right side
	this.rentalMap.context.lineTo(this.ro.posX+w, this.ro.posY+h);
	
	// bottom
//	this.rentalMap.context.lineTo(this.ro.posX+w, this.ro.posY+h);
	this.rentalMap.context.lineTo(this.ro.posX-skew, this.ro.posY+h);
	this.rentalMap.context.lineTo(this.ro.posX, this.ro.posY);
	
	this.center = w/2 - skew/6;
	
	return {x: this.ro.posX, y: this.ro.posY, w: w, h: h};	
}



function RentalMapSquare4(rentalMap, ro) {
	this.name = 'RentalMapSquare4';
	this.rentalMap = rentalMap;
	this.ro = ro;
}
RentalMapSquare4.prototype = new RentalMapSquare();
RentalMapSquare4.prototype.drawPath = function() {
	var posX = parseFloat(this.ro.left);
	var posY = parseFloat(this.ro.top);
	if (isNaN(posX)) posX = 0;
	if (isNaN(posY)) posY = 0;
	
	var roLength = parseFloat( this.ro.length );
	var roWidth = parseFloat( this.ro.width );
	
	if (isNaN(roLength) || isNaN(roWidth)) {
		console.log('Invalid length/width ' + this.ro);
		return false;
	}
	
	var w = roWidth * ($(this.rentalMap.canvas).width() / this.rentalMap.rentalMap.width);
	var h = roLength * ( $(this.rentalMap.canvas).height() / this.rentalMap.rentalMap.length);
	
	if (typeof this.ro.posX == 'undefined' || this.ro.posY == 'undefined') {
		var x = posX * ($(this.rentalMap.canvas).width() / this.rentalMap.rentalMap.width);
		var y = posY * ($(this.rentalMap.canvas).height() / this.rentalMap.rentalMap.length);

		this.ro.posX = x;
		this.ro.posY = y;
	}
	
	
	this.rentalMap.context.beginPath();
//	this.rentalMap.context.rect(this.ro.posX, this.ro.posY, w, h);
	
//	var skew = 20;
	var skew = Math.sin(Math.PI/180*35)*h;
	
	// top line
	this.rentalMap.context.moveTo(this.ro.posX, this.ro.posY);
	this.rentalMap.context.lineTo(this.ro.posX+w, this.ro.posY);
	
	// right side
	this.rentalMap.context.lineTo(this.ro.posX+w, this.ro.posY+h);
	
	// bottom
//	this.rentalMap.context.lineTo(this.ro.posX+w, this.ro.posY+h);
	this.rentalMap.context.lineTo(this.ro.posX+skew, this.ro.posY+h);
	this.rentalMap.context.lineTo(this.ro.posX, this.ro.posY);
	
	this.center = w/2 + skew/6;
	
	return {x: this.ro.posX, y: this.ro.posY, w: w, h: h};	
}



function RentalMapStairs1(rentalMap, ro) {
	this.name = 'RentalMapStairs1';
	this.rentalMap = rentalMap;
	this.ro = ro;
}
RentalMapStairs1.prototype = new RentalMapSquare();
RentalMapStairs1.prototype.drawPath = function() {
	var posX = parseFloat(this.ro.left);
	var posY = parseFloat(this.ro.top);
	if (isNaN(posX)) posX = 0;
	if (isNaN(posY)) posY = 0;
	
	var roLength = parseFloat( this.ro.length );
	var roWidth = parseFloat( this.ro.width );
	
	if (isNaN(roLength) || isNaN(roWidth)) {
		console.log('Invalid length/width ' + this.ro);
		return false;
	}
	
	var w = roWidth * ($(this.rentalMap.canvas).width() / this.rentalMap.rentalMap.width);
	var h = roLength * ( $(this.rentalMap.canvas).height() / this.rentalMap.rentalMap.length);
	
	if (typeof this.ro.posX == 'undefined' || this.ro.posY == 'undefined') {
		var x = posX * ($(this.rentalMap.canvas).width() / this.rentalMap.rentalMap.width);
		var y = posY * ($(this.rentalMap.canvas).height() / this.rentalMap.rentalMap.length);

		this.ro.posX = x;
		this.ro.posY = y;
	}
	
	
	this.rentalMap.context.beginPath();
//	this.rentalMap.context.rect(this.ro.posX, this.ro.posY, w, h);
	
//	var skew = 20;
	var skew = Math.sin(Math.PI/180*35)*h;
	
	// top line
	this.rentalMap.context.moveTo(this.ro.posX, this.ro.posY+h/2);
	this.rentalMap.context.lineTo(this.ro.posX+w/4, this.ro.posY+h/2);
	this.rentalMap.context.arc(this.ro.posX+w/2, this.ro.posY+h/2, w/6, Math.PI, Math.PI*1.5, true);
	
	this.rentalMap.context.lineTo(this.ro.posX+w/2, this.ro.posY);
	this.rentalMap.context.lineTo(this.ro.posX+w, this.ro.posY);
	
	// right side
	this.rentalMap.context.lineTo(this.ro.posX+w+skew, this.ro.posY+h);
	
	// bottom
	this.rentalMap.context.lineTo(this.ro.posX, this.ro.posY+h);
	this.rentalMap.context.lineTo(this.ro.posX, this.ro.posY+h/2);
	
	this.center = w;
	
	return {x: this.ro.posX, y: this.ro.posY, w: w, h: h};	
}


