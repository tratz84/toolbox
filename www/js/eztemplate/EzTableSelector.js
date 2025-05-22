


class EzTableSelector  {
	
	eztemplate = null;
	container = null;
	
	vars = {};
	
	locked = false;
	
	// updateResults-vars
	xhrRequest = null;
	updateTimeout = null;
	
	recordTemplate = null;
	
	tplTable = `
		<input type="hidden" class="widget-value" [name]="name" [value]="value" />
		<span class="cst-selector">
			<span class="cst-selector-text widget-default-text" [contentHTML]="defaultText"></span>
			<span class="cst-selector-caret fa fa-caret-down"></span>
		</span>
		<div class="cst-dropdown-container">
			<div><input type="text" name="q" placeholder="Zoek..." data-prevent-submit="1" autocomplete="off" /></div>
			<div ez-subtemplate="result-container">
				<table ez-if="results.length > 0" class="table">
					<thead>
						<tr ez-for="header_fields" ez-item="hf">
							<th>{{ hf }}</th>
						</tr>
					</thead>
					<tbody ez-for="results" ez-item="r">
						<tr ez-for="header_keys" ez-item="hk" [data-record]="r" onclick="$(this).closest('.ez-table-container').get(0).tsw._rowClick( this );">
							<td>
								{{ r[hk] === null ? '' : r[hk] }}
							</td>
						</tr>
					</tbody>
				</table>
				<div ez-if="results.length == 0">
					Geen resultaten gevonden
				</div ez-if>
			</div>
			<div>
				<a href="javascript:void(0);" class="btn-reset">Reset</a>
			</div>
		</div>
	`;
	
	tplRecordTemplate = `
		<input type="hidden" class="widget-value" [name]="name" [value]="value" />
		<span class="cst-selector">
			<span class="cst-selector-text widget-default-text" [contentHTML]="defaultText"></span>
			<span class="cst-selector-caret fa fa-caret-down"></span>
		</span>
		<div class="cst-dropdown-container">
			<div><input type="text" name="q" placeholder="Zoek..." data-prevent-submit="1" autocomplete="off" /></div>
			<div ez-subtemplate="result-container">
				<div ez-if="results.length > 0" class="cst-records">
					<div class="cst-records" ez-for="results" ez-item="record">
						<div class="cst-record" [data-record]="record" onclick="$(this).closest('toolbox-table-selector').get(0).tsw._rowClick( this );">
						[[recordTemplate]]
						</div>
					</div>
				</div>
				<div ez-if="results.length == 0">
					Geen resultaten gevonden
				</div ez-if>
			</div>
			<div>
				<a href="javascript:void(0);" class="btn-reset">Reset</a>
			</div>
		</div>
	`;
	
	constructor( container ) {
		this.container = container;
		
		this.container.tsw = this;
		
		this.vars['results'] =  [];
	}
	
	setName(n)      { this.vars['name'] = n; }
	setValue(v)     { this.vars['value'] = v; }
//	setValueText(t) { this.vars['valueText'] = t; }
	setUrl(u)       { this.vars['url'] = u; }
	
	
	setLocked(bln) {
		this.locked = bln;
	}
	
	_rowClick( r ) {
		let record = r.record;
		
		this.setValueText( record.id, record.defaultText );
	}
	
	setRecordTemplate( html ) {
		this.recordTemplate = html;
	}
	
	setValueText( id, defaultText ) {
		
		if (id == 'null')
			id = '';
		
		if (!id && !defaultText)
			defaultText = 'Maak uw keuze';
		
		console.log('EzTableSelector.setValueText', id, defaultText);
		
		this.vars['value'] = id;
		this.vars['defaultText'] = defaultText;
		
		$(this.container).find('.widget-value').val( id );
		$(this.container).find('.widget-default-text').text( defaultText );
		
		$(this.container).val(id);
		$(this.container).attr( 'value', id );
		$(this.container).attr( 'default-text', defaultText );
		
		console.log('trigger..', id, defaultText);
		$(this.container).trigger( 'change' );
		
		$('.ez-table-container').removeClass('opened');
	}
	
	resetValueText() {
		this.setValueText( '', 'Maak uw keuze' );
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
				this.vars['header_keys'] = [];
				this.vars['header_fields'] = [];
				for(let i in data.headers) {
					this.vars['header_keys'].push( i );
					this.vars['header_fields'].push( data.headers[i] );
				}
				
				this.vars['results'] = data.records;
				this.eztemplate.setVars( this.vars );
//				console.log('this.vars', this.vars);
				
				this.eztemplate.renderSubTemplate( 'result-container' );
			}.bind(this)
		});
	}
	
	
	
	init() {
		this.eztemplate = new EzTemplate( this.container );
		this.eztemplate.setVars( this.vars );
		
		if (this.recordTemplate) {
			let t = this.tplRecordTemplate.replace('[[recordTemplate]]', this.recordTemplate);
			this.eztemplate.loadHtml( t );
		}
		else {
			this.eztemplate.loadHtml( this.tplTable );
		}
		
		this.renderWidget();
	}
	
	// resets everything & renders widget
	renderWidget() {
		this.eztemplate.render();
		
		if (!this.locked) {
			this.handleEvents();
		}
		
		if (this.locked) {
//			$(this.container).closest('div.widget').find('.fa-plus, .cst-selector-caret').remove();
			$(this.container).closest('div.widget').find('.cst-selector').css('cursor', 'auto');
		}
	}
	
	handleEvents() {
		$(this.eztemplate.container).find('[name=q]').on('keydown', function(evt) {
			// escape
			if ( evt.keyCode == 27 ) {
				//$(evt.target).val('');
				$('.ez-table-container').removeClass('opened');
				
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
			let ets = $(evt.target).closest('.ez-table-container');
			
			if ($(ets).closest('ez-table-selector').attr('readonly') == 'readonly') {
				return;
			}
			
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


$(window).on('click', function(evt) {
	if ( $(evt.target).closest('.ez-table-container').length > 0 )
		return;
	
	$('.ez-table-container').removeClass('opened');
});



