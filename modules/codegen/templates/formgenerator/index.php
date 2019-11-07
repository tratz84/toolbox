
<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=codegen&c=formgenerator&a=list') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

	<h1>Form generator</h1>
</div>



<?= $form->render() ?>

<hr/>

<div class="row">
    <div class="col-xs-3">
    	<div>
    		<a href="javascript:void(0);" onclick="btnAddWidget_Click();">Add widget</a>
    	</div>
    	<div id="tree"></div>
    	
    	<div id="widget-info"></div>
    </div>
    
    <div class="col-xs-9">
		<div id="form-preview" class="form-preview"></div>
	</div>
</div>

<script>

var selectedNode = null;

$(document).ready(function() {
	var treedata = [];
	if ($('[name=treedata]').val() != '') {
		treedata = JSON.parse( $('[name=treedata]').val() );
	}
	
	$('#tree').jstree({
		core: {
			animation: false,
			check_callback: true,
			data: treedata,
			multiple: false
		},
		'types': {
			'widget': {
				icon: 'fa fa-sticky-note-o',
				'valid_children': []
			},
			'container': {
				'icon': 'fa fa-folder-o',
				'valid_children': ['container', 'widget']
			}
		},
		plugins: [ 'themes', 'dnd', 'state', 'types', 'html_data' ]
		
	}).bind("loaded.jstree", function (event, data) {
        $(this).jstree("open_all");
        
    	update_form();
    }).on('changed.jstree', function(e, data) {
    	if (data.action && data.action == 'delete_node')
        	return true;

        if (data && data.node && data.node.data) {
        	selectedNode = data.node;
            widget_properties( data.node );
        }
    }).on('move_node.jstree', function(obj, parent) {
        
        update_form();
    });

});

$('.form-form-generator-form').submit(function() {
	var data = treedata('tree');
	
	$('[name=treedata]').val( JSON.stringify( data ) );
});

function treedata(treeid) {
	var jso = $('#' + treeid).jstree(true).get_json();

	return parse_tree( jso );
	
}
function parse_tree(tree_json) {
	var data = [];

	for(var x=0; x < tree_json.length; x++) {
		var i = {};
		console.log(tree_json[x]);
		i.type = tree_json[x].type;
		i.text = tree_json[x].text;
		i.data = {};
		if (tree_json[x].data) for(var y in tree_json[x].data) {
			i.data[y] = tree_json[x].data[y];
		}
		
		if (tree_json[x].children && tree_json[x].children.length) {
			var childdata = parse_tree(tree_json[x].children);
			i.children = childdata;
		}

		data.push(i);
	}

	return data;
}


function btnAddWidget_Click() {
	show_popup( appUrl('/?m=codegen&c=formgenerator&a=select_widget') );
}

function add_widget(w) {
	$('#tree').jstree(true).create_node(null, {
		'text': w.label,
		'type': w.type,
		data: w
	});
	
	close_popup();

	update_form();
}

function widget_properties(node) {
	var data = node.data;

	$.ajax({
		url: appUrl('/?m=codegen&c=formgenerator&a=widget_properties'),
		type: 'POST',
		data: data,
		success: function( data, xhr, textStatus) {
			$('#widget-info').html( data );

			$('#widget-info').find('input, select, textarea').change(function() {
				var name = $(this).attr('name');
				var val = $(this).val();
				selectedNode.data[name] = val;

				if (name == 'name' || name == 'label') {
					var t = '';
					t += $('#widget-info').find('[name=name]').val();

					var lbl = $('#widget-info').find('[name=label]').val();
					if (lbl != '' && typeof lbl != 'undefined') {
    					if (t != '')
    						t = t + ': ';
    					
    					t = t + lbl;
					}

					selectedNode.text = t;
					var n = $('#tree').jstree(true).redraw( true );
				}
				
				update_form();
			});
		}
	});
}

function delete_selected_widget() {
	$('#tree').jstree(true).delete_node( selectedNode );
	
	$('#widget-info').html('');
	selectedNode = null;

	update_form();
}

var ajx_update_form = null;
function update_form() {

	if (ajx_update_form) {
		ajx_update_form.abort();
	}

	var json_treedata = JSON.stringify( treedata('tree') );
	
	ajx_update_form = $.ajax({
		type: 'POST',
		url: appUrl('/?m=codegen&c=formgenerator&a=example_form'),
		data: {
			json_treedata: json_treedata
		},
		success: function(data, xhr, textStatus) {
			$('#form-preview').html( data );
			
		}
	});
}



</script>



