




class TableSelectWidget {
	
	eztemplate = null;
	container = null;
	
	vars = {};
	
	locked = false;
	
	// updateResults-vars
	xhrRequest = null;
	updateTimeout = null;
	
	tpl = `
		<input type="hidden" class="widget-value" [name]="name" [value]="value" />
		<span class="cst-selector">
			<span class="cst-selector-text widget-default-text" [contentHTML]="defaultText"></span>
			<span class="cst-selector-caret fa fa-caret-down"></span>
		</span>
		<div class="cst-dropdown-container">
			<div><input type="text" name="q" [placeholder]="txt_search" data-prevent-submit="1" autocomplete="off" /></div>
			<div ez-subtemplate="result-container">
				<table ez-if="results.length > 0" class="list-response-table">
					<thead>
						<tr ez-for="header_fields" ez-item="hf">
							<th>{{hf}}</th>
						</tr>
					</thead>
					<tbody ez-for="results" ez-item="r">
						<tr ez-for="header_fields" ez-key="hf" [data-record]="r" onclick="$(this).closest('toolbox-table-selector').get(0).tsw._rowClick( this );">
							<td>
								{{ r[hf] }}
							</td>
						</tr>
					</tbody>
				</table>
				<div ez-if="results.length == 0">
					{{ toolbox_t('No results found') }}
				</div ez-if>
			</div>
			<div>
				<a href="javascript:void(0);" class="btn-reset">{{ toolbox_t('Reset') }}</a>
			</div>
		</div>
	`;
	
	constructor( container ) {
		this.container = container;
		
		this.container.tsw = this;
		
		this.vars['txt_search'] = toolbox_t('Search...');
		
		this.vars['name']        = $(this.container).attr('name');
		this.vars['value']       = $(this.container).attr('value');
		this.vars['defaultText'] = $(this.container).attr('default-text');
		this.vars['url']         = $(this.container).attr('url');
		
		this.setValueText( this.vars['value'], this.vars['defaultText'] );
		
		this.vars['results'] =  [];
	}
	
	setLocked(bln) {
		this.locked = bln;
	}
	
	_rowClick( r ) {
		let record = r.record;
		
		this.setValueText( record.id, record.default_text );
	}
	
	setValueText( id, default_text ) {
		
		if (id == '' && default_text == '')
			default_text = toolbox_t('Make your choice');
		
		this.vars['value'] = id;
		this.vars['defaultText'] = default_text;
		
		$(this.container).find('.widget-value').val( id );
		$(this.container).find('.widget-default-text').text( default_text );
		
		$(this.container).val(id);
		$(this.container).attr( 'value', id );
		$(this.container).attr( 'default-text', default_text );
		
		console.log('trigger..');
		$(this.container).trigger( 'change' );
		
		$('toolbox-table-selector').removeClass('opened');
	}
	
	resetValueText() {
		this.setValueText( '', toolbox_t('Make your choice') );
	}
	
	
	
	updateResults( q ) {
		if (this.xhrRequest)
			this.xhrRequest.abort();
		if (this.updateTimeout)
			clearTimeout( this.updateTimeout );
		
		this.updateTimeout = setTimeout(function() {
			this._updateResults( q );
		}.bind(this), 200);
	}
	
	_updateResults( q ) {
		
		this.xhrRequest = $.ajax({
			type: 'POST',
			url: this.vars['url'],
			data: {
				q: q,
				value: this.vars['value']
			},
			success: function(data, xhr, textStatus) {
				this.vars['header_fields'] = data['header_fields'];
				this.vars['results'] = data['results'];
				
				this.eztemplate.renderSubTemplate( 'result-container' );
			}.bind(this)
		});
	}
	
	
	
	init() {
		this.eztemplate = new EzTemplate( this.container );
		this.eztemplate.setVars( this.vars );
		this.eztemplate.loadHtml( this.tpl );
		
		this.renderWidget();
	}
	
	// resets everything & renders widget
	renderWidget() {
		this.eztemplate.render();
		
		if (!this.locked) {
			this.handleEvents();
		}
		
		if (this.locked) {
			$(this.container).closest('div.widget').find('.fa-plus, .cst-selector-caret').remove();
			$(this.container).closest('div.widget').find('.cst-selector').css('cursor', 'auto');
		}
	}
	
	handleEvents() {
		$(this.eztemplate.container).find('[name=q]').on('keydown', function(evt) {
			// escape
			if ( evt.keyCode == 27 ) {
				//$(evt.target).val('');
				$('toolbox-table-selector').removeClass('opened');
				
				evt.stopPropagation();
				
				return;
			}
			
			
		}.bind(this));
		
		$(this.eztemplate.container).find('[name=q]').on('keyup', function(evt) {
			if ( $(this.container).hasClass( 'opened' ) == false ) {
				return;
			}
			
			this.updateResults( $(evt.target).val() );
		}.bind(this));
		
		$(this.eztemplate.container).find('.cst-selector').on('click', function(evt) {
			let ets = $(evt.target).closest('toolbox-table-selector');
			
			if (ets.hasClass('opened')) {
				ets.removeClass('opened');
			}
			else {
				ets.addClass('opened');
				ets.find('[name=q]').focus();
				
				if (!this.initialSearchExecuted) {
					this.initialSearchExecuted = true;
				
					this._updateResults('');
				}
			}
		}.bind(this));
		
		$(this.eztemplate.container).find('input[name=q]').on('change', function(evt) {
			evt.stopImmediatePropagation();
		});
		
		$(this.eztemplate.container).find('.btn-reset').on('click', function(evt) {
			this.resetValueText();
		}.bind(this));
	}
	
}


$(window).on('applyWidgetFields', function() {
	$('toolbox-table-selector').each(function(index, node) {
		if (node.tsw)
			return;
		
		let locked = $(node).closest('form').find('.object-locked').val() == '1' ? 1 : 0;
		
		let tsw = new TableSelectWidget( node );
		tsw.setLocked( locked );
		tsw.init();
		
		node.tsw = tsw;
	});
	
});
$(window).on('click', function(evt) {
	if ( $(evt.target).closest('toolbox-table-selector').length > 0 )
		return;
	
	$('toolbox-table-selector').removeClass('opened');
});





