
<div class="page-header">
	
	<div class="toolbox">
		<a href="<?= appUrl('/?m=codegen&c=listeditgenerator&a=list') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>
	

	<h1>ListEdit generator</h1>
</div>

<?= $form->render() ?>

<div class="clear"></div>
<hr />

<a href="javascript:void(0);" onclick="btnAddWidget_Click();">Add widget</a>

<hr/>

<ul class="widgets-container" id="widgets-container">
	
</ul>

<div id="widget-info"></div>


<script>

$(document).ready(function() {

	$('#widgets-container').sortable({ });


	$('form.form-list-edit-generator-form').submit(function() {
		var data = [];
		$('#widgets-container').find('li').each(function(index, node) {
			var w = $(node).data('widget');
			data.push( w );
		});

		$('[name=data]').val( JSON.stringify(data) );
	});

	var data = $('[name=data]').val();
	if (data != '') {
		var widgets = JSON.parse( data );
		for(var i in widgets) {
			add_widget( widgets[i] );
		}
	}
	
});


function btnAddWidget_Click() {
	show_popup( appUrl('/?m=codegen&c=formgenerator&a=select_widget') );
}

function add_widget(w) {

	var li = $('<li />');
	li.data('widget', w);

	var a = $('<a href="javascript:void(0);" />');
	console.log(w);
	if ($.trim(w['text']) == '') {
		a.text( w['class'] );
	} else {
		a.text( w['text'] );
	}
	li.append(a);

	a.click(function() {
		var li = $(this).closest('li');
		widget_properties( $(li).data('widget') );

		$(this).closest('ul').find('li').removeClass('selected');
		li.addClass('selected');
	});

	$('#widgets-container').append( li );
	
	close_popup();
	
// 	update_form();
}

function widget_properties( data ) {
	
    $.ajax({
    	url: appUrl('/?m=codegen&c=formgenerator&a=widget_properties'),
    	type: 'POST',
    	data: data,
    	success: function( data, xhr, textStatus) {
    		$('#widget-info').html( data );
    
    		$('#widget-info').find('input, select, textarea').change(function() {
				var name = $(this).attr('name');
				var val = $(this).val();

				var selectedLi = $('#widgets-container li.selected');
				var data = selectedLi.data('widget');
				data[name] = val;
				selectedLi.data('widget', data);

				if (name == 'name' || name == 'label') {
					var t = '';
					t += $('#widget-info').find('[name=name]').val();

					var lbl = $('#widget-info').find('[name=label]').val();
					if (lbl != '' && typeof lbl != 'undefined') {
    					if (t != '')
    						t = t + ': ';
    					
    					t = t + lbl;
					}

					data['text'] = t;

					selectedLi.find('a').text( t );
				}
        		
    		});
    	}
    });
}

function delete_selected_widget() {
	$('#widgets-container').find('li.selected').remove();
	
	$('#widget-info').html('');

	update_form();
}

function update_form() {
	
}



</script>
