


EzTemplateLoader.addTemplate('h1', `
	<h1>{{text}}</h1>
`);

EzTemplateLoader.addTemplate('text', `
	<span class="text">{{text}}</span>
`);



EzTemplateLoader.addTemplate('ez-text', `
	<div class="input">
		<label>{{label}}</label>
		
		<input type="text" name="{{name}}" [value]="value" />
		
		<span class="error"></span>
	</div>
`);

EzTemplateLoader.addTemplate('ez-number', `
	<div class="input input-number">
		<label>{{label}}</label>
		
		<input type="number" name="{{name}}" [value]="value" [min]="min" />
		
		<span class="error"></span>
	</div>
`);

EzTemplateLoader.addTemplate('ez-textarea', `
	<div class="input input-textarea">
		<label>{{label}}</label>
		
		<textarea name="{{name}}" [contentHTML]="value"></textara>
		
		<span class="error"></span>
	</div>
`);


EzTemplateLoader.addTemplate('ez-email', `
	<div class="input input-email">
		<label>{{label}}</label>
		
		<input type="email" name="{{name}}" [value]="value" />
		
		<span class="error"></span>
	</div>
`);


EzTemplateLoader.addTemplate('ez-password', `
	<div class="input input-password">
		<label>{{label}}</label>
		
		<input type="password" name="{{name}}" [value]="value" />
		
		<span class="error"></span>
	</div>
`);

EzTemplateLoader.addTemplate('ez-checkbox', `
	<div class="input input-checkbox">
		<label for="cb_{{name||slug}}">{{label}}</label>
		
		<input type="checkbox" id="cb_{{name||slug}}" name="{{name||slug}}" />
		
		<span class="error"></span>
		
		<script>
			if (typeof checked != 'undefined' && checked) {
				let node = document.currentScript.parentNode;
				node.querySelector('input[type=checkbox]').checked=true;
			}
		</script>
		
		
		<div class="clear"></div>
	</div>
`);

EzTemplateLoader.addTemplate('ez-string', `
	<div class="input input-string">
		<label>{{label}}</label>
		
		<span class="value" [contentHTML]="value"></span>
		<div class="clear"></div>
	</div>
`);


EzTemplateLoader.addTemplate('ez-file', `
	<div class="input">
		<label>{{label}}</label>
		
		<input type="file" name="{{name}}" />
		
		<span class="error"></span>
	</div>
`);


EzTemplateLoader.addTemplate('ez-date', `
	<div class="input">
		<label>{{label}}</label>
		
		<input type="text" class="input-pickadate" name="{{name}}" />
		
		<span class="error"></span>
		
		<script type="text/javascript">
		if (!value)
			return;
		
		if (typeof value == 'number' || value.match(/^\d+$/)) {
			let d = new Date(value);
			value = format_date( d, {dmy: true});
		}
		$(document.currentScript.parentNode).find('input').val( value );
		</script>
		
	</div>
`);


EzTemplateLoader.addTemplate('ez-datetime-text', `
	<div class="input">
		<label>{{label}}</label>
		
		<span class="datetime-text"></span>
		<script>
			let node = document.currentScript.parentNode;
			
			if (typeof hideempty != 'undefined' && hideempty && (!value || value == '')) {
				$(node).hide();
				return;
			}
			
			$(node).show();
			
			if (!value || value == '')
				value = '-';
			
			if (typeof value == 'number') {
				value = new Date(value);
				value = format_datetime( value, { dmy: true } );
			}
			
			node.querySelector('span.datetime-text').innerHTML = value;
		</script>
		
		<span class="error"></span>
	</div>
`);


EzTemplateLoader.addTemplate('ez-radio', `
	<div class="input input-radio">
		<label>{{label}}</label>
		
		<span class="radio-container"></span>
		<div class="clear"></div>
		<script>
			let ezContainer = document.currentScript.parentNode.parentNode;
			let c = $(ezContainer);
			
			let ezTpl = c.get(0).ezTemplate;
			
			let opts = $(ezTpl.templateInnerHTML);
			
			for(let i=0; i < opts.length; i++) {
				let opt = opts.get(i);
				
				if ( opt.nodeName != 'OPTION')
					continue;
				
				let val = opt.value;
				let text = $.trim( $(opt).text() );
				
				let l = $('<label><input type="radio" /> <span class="radio-label"></span></label>');
				l.find('input').val( val );
				l.find('input').attr( 'name', name );
				l.find('span').text( text );
				
				if (!value && i == 0) {
					l.find('input').prop('checked', true);
				}
				else if (value == val) {
					l.find('input').prop('checked', true);
				}
				
				
				$(ezContainer).find('span.radio-container').append( l );
			}
			
			if ( typeof options == 'string' ) {
				console.log(' options',  options );
				
				let lines = options.split(/\\n/);
				
				for(let i=0; i < lines.length; i++) {
					let line = lines[i].trim();
					if (line.length == 0) continue;
					
					let val;
					let text;
					if (line.indexOf(':') != -1) {
						val = line.substr(0, line.indexOf(':'));
						text = line.substr( val.length + 1 );
					}
					else {
						val = line;
						text = line;
					}
					
					let l = $('<label><input type="radio" /> <span class="radio-label"></span></label>');
					l.find('input').val( val );
					l.find('input').attr( 'name', name );
					l.find('span').text( text );
					
					if (typeof value != 'undefined') {
						if (!value && i == 0) {
							l.find('input').prop('checked', true);
						}
						else if (value && value == val) {
							l.find('input').prop('checked', true);
						}
					}
					
					
					$(ezContainer).find('span.radio-container').append( l );
				}
				
			}

		</script>
		
		<span class="error"></span>
	</div>
`);





EzTemplateLoader.addTemplate('ez-select', `
	<div class="input">
		<label>{{label}}</label>
		
		<select name="{{name}}"></select>
		<script>
			let ezContainer = document.currentScript.parentNode.parentNode;
			
			if (typeof slug != 'undefined') {
				$(ezContainer).addClass('input-'+slug);
			}
			
			let slct = ezContainer.querySelector( 'select' );
			
			// set options
			if ( ezContainer.ezTemplate.templateInnerHTML ) {
				slct.innerHTML = ezContainer.ezTemplate.templateInnerHTML;
			}
			
			// list items
			if (typeof items != 'undefined') {
				for(let i in items) {
					let opt = document.createElement('option');
					opt.value     = items[i].value;
					opt.innerHTML = items[i].description;
					opt.item      = items[i];
					slct.appendChild( opt );
				}
			}

			if (typeof value != 'undefined' && selectOptionExists(slct, value)) {
				slct.value = value;
			}
			

			let oc = ezContainer.getAttribute('[onchange]');
			if (oc) {
				let val = ezContainer.eztemplate.getVarValue(oc)
				
				if (val)
					slct.addEventListener('change', val);
			}


			
		</script>
		
		<span class="error"></span>
	</div>
`);


EzTemplateLoader.addTemplate('ez-table-selector', `
	<div class="input">
		<label>{{label}}</label>
		
		<div class="ez-table-container"></div>
		
		<div class="clear"></div>
		
		<script>
			let ezContainer = document.currentScript.parentNode;
			
			let eztbl = new EzTableSelector( ezContainer.querySelector('.ez-table-container') );
			eztbl.setName( name );
			eztbl.setValueText( value, valuetext );
			eztbl.setUrl( url );
			eztbl.init();
		</script>
	</div>
`);

EzTemplateLoader.addTemplate('ez-pager', `
	<div class="pager">
	</div>
	<script>
		console.log('ez-pager...');
		
		if (typeof response == 'undefined')
			return;
		
		let container = document.currentScript.parentNode;
		
		let w = container.eztemplate.vars['widget'];
		
		
		let pageCount = Math.ceil( response.rowCount / response.pageSize );
		let pageNo = Math.floor( response.start / response.pageSize );
		
		if (pageCount == Infinity || isNaN(pageCount))
			return;
		if (pageNo == Infinity || isNaN(pageNo))
			return;
		
		if (pageNo > 0) {
			let a = $('<a class="pager-link" />');
			a.attr('href', 'javascript:void(0)');
			a.text( '<' );
			a.click(function() {
				w.selectPage( pageNo-1 );
			});
			$(container).find('.pager').append( a );
			$(container).find('.pager').append( ' ' );
		}
		
		if (pageCount > 10) {
			let s = $('<select />');
			
			for(let i=0; i < pageCount; i++) {
				let opt = $('<option />');
				opt.val( i );
				opt.text( i+1 );
				
				if (i == pageNo)
					opt.prop('selected', true );
				$(s).append(opt);
			}
			
			s.change(function() {
				w.selectPage( this.value );
			});
			
			$(container).find('.pager').append( s );
		}
		else {
			for(let i=0; i < pageCount; i++) {
				let a = $('<a class="pager-link" />');
				a.attr('href', 'javascript:void(0)');
				if (i == pageNo)
					a.text( '[' + (i+1) + ']' );
				else
					a.text( (i+1) );
				
				a.click(function() {
					w.selectPage( i );
				});
				
				$(container).find('.pager').append( a );
				$(container).find('.pager').append( ' ' );
			}
		}
		
		
		if (pageNo+1 < pageCount) {
			let a = $('<a class="pager-link" />');
			a.attr('href', 'javascript:void(0)');
			a.text( '>' );
			a.click(function() {
				w.selectPage( pageNo+1 );
			});
			$(container).find('.pager').append( a );
		}
		
	</script>
`);



EzTemplateLoader.addTemplate('tinymce-text', `
	<div class="input" [contentHTML]="text">
	</div>
`);


EzTemplateLoader.addTemplate('linechartv1', `
	<div class="input">
		<label></label>
		
		<canvas class="chart-widget"></canvas>
		
		<div class="clear"></div>
		
		<script>
			let ezContainer = document.currentScript.parentNode;
			
			loadDataAdapter( dataAdapterId ).then(async function(e) {
				let json = await e.json();
				let c = ezContainer.querySelector('.chart-widget');
				
				
				renderLinechartv1( c, json, {
					label: label,
					value: value,
					title: title
				});
			});
		</script>
	</div>
`);







