




class TableSelectWidget {
	
	eztemplate = null;
	container = null;
	
	vars = {};
	
	// updateResults-vars
	xhrRequest = null;
	updateTimeout = null;
	
	tpl = `
		<input type="hidden" [name]="name" [value]="value" />
		<span class="cst-selector">
			<span class="cst-selector-text" [contentHTML]="defaultText"></span>
			<span class="cst-selector-caret fa fa-caret-down"></span>
		</span>
		<div class="cst-dropdown-container">
			<div><input type="text" name="q" data-prevent-submit="1" /></div>
			<table>
				<thead>
					<tr ez-for="header_fields" ez-item="hf">
						<th>{{hf}}</th>
					</tr>
				</thead>
				<tbody ez-for="results" ez-item="r">
					<tr ez-for="header_fields" ez-key="hf">
						<td>
							{{ r[hf] }}
						</td>
					</tr>
				</tbody>
			</table>
			<div ez-if="typeof results == 'undefined' || results.length == 0">
				{{ t('No results found') }}
			</div ez-if>
		</div>
	`;
	
	constructor( container ) {
		this.container = container;
		
		this.container.tsw = this;
		
		this.vars['name']        = $(this.container).attr('name');
		this.vars['value']       = $(this.container).attr('value');
		this.vars['defaultText'] = $(this.container).attr('default-text');
		this.vars['url']         = $(this.container).attr('url');
		
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
				q: q
			},
			success: function(data, xhr, textStatus) {
				this.vars['header_fields'] = data['header_fields'];
				this.vars['results'] = data['results'];
				this.eztemplate.render();
			}.bind(this)
		});
	}
	
	
	
	init() {
		this.eztemplate = new EzTemplate( this.container );
		
		this.eztemplate.setVars( this.vars );
		
		console.log('wel hier');
//		console.log(this.tpl);
		this.eztemplate.loadHtml( this.tpl );
		
		this.eztemplate.render();
		
		console.log( $(this.eztemplate.container).length );
		$(this.eztemplate.container).find('[name=q]').on('keyup', function(evt) {
			this.updateResults( $(evt.target).val() );
		}.bind(this));
	}
	
}


$(document).ready(function() {
	
	$('ez-table-selector').each(function(index, node) {
		console.log(node);
		
		let tsw = new TableSelectWidget( node );
		tsw.init();
		
	});
	
});




