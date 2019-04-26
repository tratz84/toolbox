




function Dashboard( containerId, config ) {
	
	this.containerId = containerId;
	this.config = config;
	var me = this;
	
	
	this.init = function() {
		this.bindEvents();
		
	    $( this.containerId ).gridstack({
	    	
	    });
	    
	    this.renderDashboard();
	};
	
	this.bindEvents = function() {
		var me = this;
		
		$('#dashboard-settings-click').click(function() { me.dashboardSettings_Click(); });
		
		$( this.containerId ).on('change', this.dashboardChanged);
	};
	
	this.getGrid = function() {
		var grid = $( this.containerId ).data('gridstack');
		
		return grid;
	};
	
	
	this.dashboardSettings_Click = function() {
		var me = this;
		
		me.renderDashboardSettings( );
	};
	
	
	// render
	this.renderDashboard = function() {
		var me = this;
		
		// remove old
		$('.dashboard-widgets .widget-item').each(function(index, node) {
			var widgetCode = $(node).data('widgetCode');
			console.log(widgetCode);
			
			if (typeof me.config.userWidgets[widgetCode] == 'undefined') {
				$(node).remove();
			}
		});
		
		// load/render widgets
		for(var i in this.config.userWidgets) {
			var widgetCode = i;
			
			this.loadWidget( widgetCode );
		}
		
		// no widgets? => show message
		if (jQuery.isEmptyObject( this.config.userWidgets )) {
			var c = $('<div class="widgets-empty-note" style="font-style: italic;"><br/>Je hebt nog geen widgets op je dashboard. Klik rechtsboven op het wieltje om widgets toe te voegen.</div>');
			$('.dashboard-widgets').prepend( c );
		} else {
			$('.dashboard-widgets .widgets-empty-note').remove();
		}

	};
	
	this.dashboardChanged = function(evt, items) {
		me.save();
	};
	
	
	this.findWidget = function(widgetCode) {
		for(var i in this.config.widgets) {
			if (this.config.widgets[i].code == widgetCode) {
				return this.config.widgets[i];
			}
		}
		
		return null;
	};
	
	this.loadWidget = function(widgetCode) {
		var me = this;
		console.log('loadWidget(' + widgetCode + ')');
		
		var widget = this.findWidget( widgetCode );
		
		if (widget == null) {
			console.log('Error, widget not found: ' + widgetCode);
			return;
		}
		

		// prevent dashboard-changed event
		$( me.containerId ).unbind('change', me.dashboardChanged);
		me.renderWidget('<div class="widget-loading">Loading...</div>', {
			widgetCode: widgetCode
		});
		// prevent dashboard-changed event
		$( me.containerId ).on('change', me.dashboardChanged);
		
		
		$.ajax({
			url: appUrl( widget.ajaxUrl ),
			type: 'POST',
			success: function(data, textStatus, xhr) {
				// prevent dashboard-changed event
				$( me.containerId ).unbind('change', me.dashboardChanged);

				me.renderWidget(data, {
					widgetCode: widgetCode
				});
				
				// prevent dashboard-changed event
				$( me.containerId ).on('change', me.dashboardChanged);
			}
		});
	};
	
	this.renderWidget = function(html, opts) {
		opts = opts ? opts : {};
		
		var c;
		var existing = false;
		if ($('.dashboard-widgets .widget-item-' + opts.widgetCode).length > 0) {
			c = $('.dashboard-widgets .widget-item-' + opts.widgetCode)
			existing  = true;
		} else {
			c = $('<div class="widget-item"><div class="grid-stack-item-content widget-item-content" /></div>');
			c.addClass('widget-item-' + opts.widgetCode);
			c.data('widgetCode', opts.widgetCode);
			
		}

		c.find('.widget-item-content').html( html );
		
		
		if (existing == false) {
			if (this.config['userWidgets'] && this.config['userWidgets'][opts.widgetCode]) {
				var s = this.config['userWidgets'] && this.config['userWidgets'][opts.widgetCode];
				this.getGrid().addWidget(c, s.x, s.y, s.width, s.height);
			} else {
				this.getGrid().addWidget(c);
			}
		}
	};
	
	
	
	this.save = function() {
		// save enabled widgets
		var data = { };
		
		data.enabledWidgets = '';
		
		for(var x=0; x < me.getGrid().grid.nodes.length; x++) {
			var item = me.getGrid().grid.nodes[x];
			
			var widgetCode = $(item.el).data('widgetCode');
			
			data['enabledWidgets'] += widgetCode + ',';
			
			data[widgetCode] = {};
			data[widgetCode]['x']      = item.x;
			data[widgetCode]['y']      = item.y;
			data[widgetCode]['width']  = item.width;
			data[widgetCode]['height'] = item.height;
		}
		
		console.log(data);
		
		$.ajax({
			url: appUrl('/?m=base&c=dashboard&a=save'),
			type: 'POST',
			data: data,
			success: function(data, textStatus, xhr) {
				console.log( data );
			}
		});
		
	};
	
	
	
	// settings
	this.renderDashboardSettings = function(data) {
		var me = this;
		
		// render widget list
		var container = $('<div class="dashboard-widget-settings" />');
		for(var x=0; x < this.config.widgets.length; x++) {
			var w = this.config.widgets[x];
			
			var item = $('<div class="widget-item" />');
			item.addClass('widget-item-' + w.code);
			item.data('widget', w);
			
			var spanName = $('<span class="name" />');
			spanName.text(w.name);
			
			var spanDescription = $('<span class="description" />');
			spanDescription.text(w.description);
			
			var anchAdd = $('<input type="checkbox" class="widget-toggle checkbox-ui" />');
			anchAdd.attr('id', 'widget-' + w.code);
			anchAdd.prop('checked', this.config.userWidgets[w.code] ? true : false);
			
			
			console.log('binding click event');
			anchAdd.click(function() {
				me.toggleWidget( $(this).closest('.widget-item') );
			});
			
			var checkboxContainer = $('<div style="float: right;" />');
			checkboxContainer.append(anchAdd);
			checkboxContainer.append('<label class="checkbox-ui-placeholder" for="widget-'+w.code+'"></label>');
			item.append(checkboxContainer);
			
			item.append(spanName);
			item.append(spanDescription);
			
			container.append( item );
		}
		
		
		showDialog({
			title: 'Widgets kiezen',
			html: container,
			callback_ok: function(objDialog) {
				$('.dashboard-widget-settings .widget-item').each(function(index, node) {
					var widget = $(node).data('widget');
					if (!widget) return;
					
					var enabled = $(node).find('.widget-toggle').prop('checked') ? true : false;
					
					if (enabled == false) {
						delete me.config.userWidgets[widget.code];
					}
				});
				
				me.renderDashboard();
				
				me.save();
				
				closeDialog();
			}
		});
	};
	

	this.toggleWidget = function(widget) {
		var anchor = $(widget).find('.widget-toggle');
		
		var widgetCode = widget.data('widget').code;
		console.log( widget.data('widget'));
		
		if (anchor.prop('checked')) {
			this.config.userWidgets[widgetCode] = {};
		} else {
			delete this.config.userWidgets[widgetCode];
		}
	};
	
	
	
	
	this.init();
}



