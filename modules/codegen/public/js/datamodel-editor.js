/**
 * 
 */



function DatamodelEditor( schemaName, container ) {
	
	this.schemaName = schemaName;
	this.container = container;
	this.data = [];
	
	this.addTable = function() {
		
	};
	
	this.setData = function( data ) {
		this.data = data;
	};
	
	
	this.render = function() {
		
		this.renderToolbar();
		
		this.renderTables();
	};
	
	
	this.renderTables = function() {
		// create table container
		var tableContainer = $(this.container).find('.table-container');
		if (tableContainer.length == 0) {
			tableContainer = $('<div class="table-container" />')
			tableContainer.css('height', '800px');
			tableContainer.css('width', '100%');
			tableContainer.css('position', 'relative');
			
			$(this.container).append( tableContainer );
		}
		
		for(var i in this.data) {
			var tbl = this.data[i];
			
			this.renderTable( tbl );
		}
	};
	
	this.renderTable = function( tbl_data ) {
		var tc = $(this.container).find('.table-container');
		
		var tbl = null;
		var tbls = $(tc).find('.table');
		tbls.each(function(index, node) {
			if ($(node).data('tableName') == tbl_data.tableName) {
				tbl = node;
				return false;
			});
		}
		if (tbl == null) {
			tbl = $('<table />');
			tbl.data('schemaName', tbl_data.schemaName);
			tbl.data('tableName',  tbl_data.tableName);
			
			tc.append( tbl );
		}
		
		tbl.empty();
		
		var trTableName = $('<tr class="tbl-name"><td></td></tr>');
		trTableName.find('.tbl-name td').text( tbl_data.schemaName + '__' + tbl_data.tableName );
		tbl.append( trTableName );
		
		// columns
		for(var columnName in tbl_data['columns']) {
			var col_props = tbl_data['columns'][ columnName ];
			
			var trCol = $('<tr class="tbl-col col-index"><td></td></tr>');
			trCol.find('.tbl-col.col-index td').text( columnName )
			
		}
		
		// TODO: indexes
		
		
		
		
	};
	
	
	this.renderToolbar = function() {
		var c = $('<div class="toolbar-container" />');
		
		var btnCreateTable = $('<input type="button" value="Create table" />');
		btnCreateTable.click(function() { this.createTable_Click(); }.bind(this));
		
		var btnCreateColumn = $('<input type="button" value="Create column" />');
		btnCreateColumn.click(function() { this.createColumn_Click(); }.bind(this));
		
		var btnCreateIndex = $('<input type="button" value="Create index" />');
		btnCreateIndex.click(function() { this.createIndex_Click(); }.bind(this));
		
		var btnDelete = $('<input type="button" value="Delete" />');
		btnDelete.click(function() { this.delete_Click(); }.bind(this));
		
		c.append( btnCreateTable );
		c.append( btnCreateColumn );
		c.append( btnCreateIndex );
		c.append( btnDelete );
		
		$(this.container).append( c );
	};
	
	this.createTable_Click = function() {
		showConfirmation('Create table',
			'Table name: <input type="text" name="new_table_name" />',
			function() {
				var t = $('[name=new_table_name]').val();
				if ($.trim(t) == '') {
					alert('Invalid Table name');
					return false;
				}
				
				this.createTable( t );
			}.bind(this));
	};
	this.createTable = function(tablename) {
		var tbl = {};
		// position on canvas
		tbl['x'] = 0;
		tbl['y'] = 0;
		
		tbl['schemaName']    = this.schemaName;
		tbl['tableName']     = tablename;
		tbl['columns']       = [];
		tbl['uniqueColumns'] = [];
		tbl['indexes']       = [];
		
		this.data.push( tbl );
	};
	
	this.createColumn_Click = function() {
		
	};
	this.createIndex_Click = function() {
		
	};
	this.delete_Click = function() {
		// TODO: determine what is selected
		// TODO: confirmation
		// TODO: delete
		
		
		// update tables
		this.renderTables();
	};
	
	
	this.init = function() {
		
	};
	
	
	this.init();
}


