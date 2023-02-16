




class TableSelectWidget {
	
	eztemplate = null;
	container = null;
	
	vars = {};
	
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
			<div><input type="text" name="q" data-prevent-submit="1" autocomplete="off" /></div>
			<div ez-subtemplate="result-container">
				<table ez-if="results.length > 0" class="list-response-table">
					<thead>
						<tr ez-for="header_fields" ez-item="hf">
							<th>{{hf}}</th>
						</tr>
					</thead>
					<tbody ez-for="results" ez-item="r">
						<tr ez-for="header_fields" ez-key="hf" [data-record]="r" onclick="$(this).closest('ez-table-selector').get(0).tsw._rowClick( this );">
							<td>
								{{ r[hf] }}
							</td>
						</tr>
					</tbody>
				</table>
				<div ez-if="results.length == 0">
					{{ t('No results found') }}
				</div ez-if>
			</div>
		</div>
	`;
	
	constructor( container ) {
		this.container = container;
		
		this.container.tsw = this;
		
		this.vars['name']        = $(this.container).attr('name');
		this.vars['value']       = $(this.container).attr('value');
		this.vars['defaultText'] = $(this.container).attr('default-text');
		this.vars['url']         = $(this.container).attr('url');
		
		this.vars['results'] =  [];
	}
	
	_rowClick( r ) {
		let record = r.record;
		
		this.setValueText( record.id, record.default_text );
	}
	
	setValueText( id, default_text ) {
		this.vars['value'] = id;
		this.vars['defaultText'] = default_text;
		
		$(this.container).find('.widget-value').val( id );
		$(this.container).find('.widget-default-text').text( default_text );
		
		$(this.container).attr( 'value', id );
		$(this.container).attr( 'default-text', default_text );
		
		$(this.container).trigger( 'change' );
		
		$('ez-table-selector').removeClass('opened');
		
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
		
		this.handleEvents();
	}
	
	handleEvents() {
		$(this.eztemplate.container).find('[name=q]').on('keyup', function(evt) {
			// escape
			if ( evt.keyCode == 27 ) {
				//$(evt.target).val('');
				$('ez-table-selector').removeClass('opened');
				
				return;
			}
			
			this.updateResults( $(evt.target).val() );
		}.bind(this));
		
		$(this.eztemplate.container).find('.cst-selector').on('click', function(evt) {
			let ets = $(evt.target).closest('ez-table-selector');
			
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
	}
	
}


$(window).on('applyWidgetFields', function() {
	$('ez-table-selector').each(function(index, node) {
		if (node.tsw)
			return;
		
		let tsw = new TableSelectWidget( node );
		tsw.init();
		
		node.tsw = tsw;
	});
	
});
$(window).on('click', function(evt) {
	if ( $(evt.target).closest('ez-table-selector').length > 0 )
		return;
	
	$('ez-table-selector').removeClass('opened');
});





