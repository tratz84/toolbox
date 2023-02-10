




class TableSelectWidget {
	
	container = null;
	tpl = `
		<div>
			<div><input type="text" name="q" /></div>
			<table>
				<thead ez-for="header_fields" ez-item="hf">
					<th>{{hf}}</th>
				</thead>
				<tbody ez-for="results" ez-item="r">
					<tr ez-for="header_fields" ez-item="hf">
						<td>
							{{ results[hf] }}
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	`;
	
	constructor( container ) {
		this.container = container;
		
		this.container.tsw = this;
	}
	
	
	
	
	init() {
		
		$(this.container).find('.cst-selector').on('click', function() {
			console.log(' lets go ');
		}.bind(this));
	}
	
}


$(document).ready(function() {
	
	$('div.table-select-widget').each(function(index, node) {
		
		let tsw = new TableSelectWidget( node );
		tsw.init();
		
	});
	
});




