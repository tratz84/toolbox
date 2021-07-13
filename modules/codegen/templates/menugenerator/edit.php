
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=codegen&c=menugeneratorController') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

	<h1>Menu generator</h1>
</div>



<?= $form->render() ?>

<hr/>

<div class="row" style="margin: 0;">
    <div class="col-3">
    	<div>
    		<a href="javascript:void(0);" onclick="btnAddMenu_Click();">Add menu</a>
    	</div>
    	<div id="tree"></div>
    </div>
    
    <div class="col-9">
    	<div id="menu-info"></div>
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
			'container': {
				'icon': 'fa fa-folder-o',
				'valid_children': ['menu']
			}
		},
		plugins: [ 'themes', 'dnd', 'state', 'types', 'html_data' ]
		
	}).bind("loaded.jstree", function (event, data) {
        $(this).jstree("open_all");
    }).on('changed.jstree', function(e, data) {
    	if (data.action && data.action == 'delete_node')
        	return true;

        if (data && data.node && data.node.data) {
        	selectedNode = data.node;
            menu_properties( data.node );
        }
    }).on('move_node.jstree', function(obj, parent) {
        
    });

});

$('.form-menu-generator-form').submit(function() {
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


function btnAddMenu_Click() {
	$('#tree').jstree(true).create_node(null, {
		'text': 'New item',
		'type': 'menu',
		data: {}
	});

	set_data_fields();
}

function menu_properties(node) {
	var data = node.data;

	$.ajax({
		url: appUrl('/?m=codegen&c=menugenerator&a=menu_properties'),
		type: 'POST',
		data: data,
		success: function( data, xhr, textStatus) {
			$('#menu-info').html( data );

			$('#menu-info').find('input, select, textarea').change(function() {
				set_data_fields();
			});
			
			$('#menu-info').find('[name=label]').change(function() {
				selectedNode.text = $(this).val();
				var n = $('#tree').jstree(true).redraw( true );
			});
		}
	});
}

function set_data_fields() {
	$('#menu-info').find('input, select, textarea').each(function() {
		var name = $(this).attr('name');
		var val = $(this).val();

		if ($(this).attr('type') == 'checkbox') {
			selectedNode.data[name] = $(this).prop('checked') ? $(this).val() : '';
		} else {
			selectedNode.data[name] = val;
		}
	});
}


function delete_selected_menu() {
	$('#tree').jstree(true).delete_node( selectedNode );
	
	$('#menu-info').html('');
	selectedNode = null;
}




</script>



