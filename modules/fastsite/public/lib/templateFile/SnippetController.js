


function SnippetController( tabContainerItem ) {

	this.tabContainerItem = tabContainerItem;
	
	this.codeMirror = null;

	this.setTabName = function(name) {
		this.tabContainerItem.setTitle( name );
	};
	
	
	this.setXPath = function(p) {
		$( this.tabContainerItem.contentContainer ).find('.snippet-xpath').val( p );
	};
	
	this.setSnippetName = function(name) {
		$( this.tabContainerItem.contentContainer ).find('.snippet-name').val( name );
		
		this.setTabName( 'Snippet - ' + name );
	};
	
	this.setCode = function(code) {
		this.codeMirror.setValue( code );
	};
	
	
	this.loadSnippet = function(snippetName) {
		if (snippetName == '') {
			this.setCode('');
			return;
		}
		
		$.ajax({
			type: 'POST',
			url: appUrl('/?m=fastsite&c=template/templateFile'),
			data: {
				a: 'load_snippet',
				template: $('#template_name').val(),
				snippet: snippetName
			},
			success: function(data, xhr, textStatus) {
				if (data.success) {
					this.setCode( data.data );
				} else {
					alert('Error: ' + data.message);
				}
			}.bind(this)
		});
	};
	
	this.init = function() {
		var selectSnippetName = $(this.tabContainerItem.contentContainer).find('select.snippet-name');
		var me = this;
		
		selectSnippetName.change(function() {
			var newSnippet = $(this).find('option:selected').data('new') == '1';
			if (newSnippet) {
				var txtSnippetName = $('<input type="text" name="snippet_name" class="snippet-name" />');
				$(this).replaceWith( txtSnippetName );
				$(txtSnippetName).change(function() {
					me.setTabName('Snippet - ' + $(this).val());
				});
				txtSnippetName.focus();
				me.setTabName('Snippet');
				return;
			}
			
			me.loadSnippet( $(this).val() );
			me.setTabName( 'Snippet - ' + $(this).val() );
		});
		
		var ta = this.tabContainerItem.contentContainer.find('textarea');
		this.codeMirror = CodeMirror.fromTextArea( ta.get(0), {
			lineNumbers: true,
			mode: 'php',
		});
		
	};


	this.init();
}
