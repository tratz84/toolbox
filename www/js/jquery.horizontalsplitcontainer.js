

(function($) {
	$.fn.horizontalSplitContainer = function(opts) {
		var me = this;
		
		opts = opts || { };
		
		$(this).data('split-container', new ContainerSlider(this, opts));
		
		return this;
	};


	function ContainerSlider(container, opts) {

		this.opts = opts ? opts : {};
		
		this.container = container;
		this.subcontainers = null;
		
		this.sliderBars = [];
		this.resizeSliderStart = null;
		this.mouseSliderStart = null;
		this.selectedSlider = null;

		this.init = function() {
			var me = this;
			
			// outer container
			$(window).resize(this.resizeContainer.bind(this));
			this.resizeContainer();

			$(this.container).css('position', 'relative');
			
			// inner containers
			this.subcontainers = $(this.container).find('> *');
			var containerHeight = $(this.container).height();

			var currentHeightOffset = 0;
			this.subcontainers.each(function(index, node) {
				var perc = parseFloat($(node).data('height-in-percentage'));
				
				if (isNaN(perc)) {
					perc = (containerHeight / me.subcontainers.length) / containerHeight;
					$(node).data('height-in-percentage', perc);
				}
				$(node).height( containerHeight * perc );
				
				if ($(node).data('dont-overflow')) {
					
				} else {
					$(node).css('overflow-y', 'auto');
				}

				currentHeightOffset += $(node).height();

				if (index < me.subcontainers.length-1) {
					me.addSlider( index, currentHeightOffset );
				}
			});
		};

		this.resizeContainer = function() {
			var me = this;
			
			// container container
			var o = $(this.container).offset();
			var h = $(window).height();
			var containerHeight = h - o.top;
			$(this.container).css('height', containerHeight);


			if (this.subcontainers) {
    			var offsetHeight = 0;
    			this.subcontainers.each(function(index, node) {
        			var p = $(node).data('height-in-percentage');
        			var height = containerHeight * p;
        			$(node).height( height );
    
        			offsetHeight += height;
    
    				if (index < me.sliderBars.length) {
    					me.sliderBars[index].css('top', offsetHeight);
    				}
    			});
			}
		}

		

		this.addSlider = function(sliderNo, offset) {
			var panels = $(this.container).find('> *:not(.slider)');

    		var s = $('<div class="slider"></div>');
    		s.css('position', 'absolute');
    		
    		var bg = $('header .notifications-bar').css('background-color');
    		if (!bg) bg = '#f00';
    		
    		s.css('background-color', bg);
    		s.css('cursor', 'ns-resize');
    		s.height(5);
    		s.css('width', '100%');
    		s.css('top', offset);
    		s.css('z-index', '9999');
    		s.data('slider-no', sliderNo);

    		$(this.container).append(s);
    		
    		$(window).mouseup(this.mouseup.bind(this));
    		s.mousedown(this.mousedown.bind(this));
    		$(window).mousemove(this.mousemove.bind(this));
    

    		this.sliderBars.push( s );
		};
		
		this.mouseup = function(evt) {
			if (this.resizeSliderStart == null) {
				return;
			}
			
			this.resizePanels( $(this.selectedSlider).data('slider-no'), this.selectedSlider );

			
			this.resizeSliderStart = null;
			this.selectedSlider = null;
			this.selectedSlider = null;

			if (this.opts.onresize) {
				this.opts.onresize(this);
			}
		};
		this.mousedown = function(evt) {
			var t = parseInt( $(evt.target).css('top') );
			this.resizeSliderStart = t;
			this.mouseSliderStart = evt.clientY;

			this.selectedSlider = evt.target;
			
		};
		this.mousemove = function(evt) {
			if (this.resizeSliderStart == null || this.selectedSlider == null) return;

			var o = evt.clientY - this.mouseSliderStart;
			
			var pos = this.resizeSliderStart + o;

			if (pos < 0) pos = 0;
			if (pos > $(this.container).height()) pos = $(this.container).height();
			
			$(this.selectedSlider).css('top', pos);

			// todo: resize panels
			this.resizePanels( $(evt.target).data('slider-no'), evt.target );
		};

		this.resizePanels = function(sliderNo, objSlider) {
			if (typeof sliderNo == 'undefined') return;
			
			var containerHeight = $(this.container).height();

			
			var panelTop = $(this.subcontainers).get(sliderNo);
			var panelBottom = $(this.subcontainers).get(sliderNo+1);

			var startTop = 0;
			var heightOtherPanels = 0;
			this.subcontainers.each(function(index, node) {
				if (index < sliderNo)
					startTop += $(node).height();
				
				if (index == sliderNo || index == sliderNo+1) return;
				heightOtherPanels += $(node).height();
			});
			var sharedHeight = containerHeight - heightOtherPanels;

			var sliderTop = parseInt( $(objSlider).css('top') );

			
			var sizeFirst = (sliderTop - startTop);
			var sizeSecond = sharedHeight - sizeFirst; 
			
			$(panelTop).height(sizeFirst);
			$(panelBottom).height(sizeSecond);

			this.subcontainers.each(function(index, node) {
				var p = $(node).height() / containerHeight;
				$(node).data('height-in-percentage', p);
			});
			
		};
		
    	this.getPanelPercentages = function() {
    		var p = [];
    		this.subcontainers.each(function(index, node) {
    			p.push( $(node).data('height-in-percentage') );
    		});
    		return p;
    	};

		this.init();
	};

		

}(jQuery));

