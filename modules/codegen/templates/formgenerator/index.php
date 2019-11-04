
<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=codegen&c=menu') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

	<h1>Form generator</h1>
</div>



<?= $form->render() ?>

<div class="">
	<div>
		<a href="javascript:void(0);" onclick="btnAddWidget_Click();">Add widget</a>
	</div>
	<div id="tree"></div>
	
	<div id="widget-info"></div>
</div>
<div id="form-preview" class="form-preview"></div>
<input type="button" value="test" onclick="test()" />

<script>
var tree;
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
    }).on('changed.jstree', function(e, data) {
        console.log(data.node);
        if (data && data.node && data.node.data) {
        	console.log(data.node.data);
        }
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
		if (tree_json[x].data) for(var y in tree_json[x].data) {
			i[y] = tree_json[x].data[y];
		}
		
		
		if (tree_json[x].children && tree_json[x].children.length) {
			var childdata = parse_tree(tree_json[x].children);
			i.children = childdata;
		}

		data.push(i);
	}

	return data;
}


function test() {
	var td = treedata('tree');
	
	console.log(td);
}


function btnAddWidget_Click() {
	show_popup( appUrl('/?m=codegen&c=formgenerator&a=select_widget') );
}

function add_widget(w) {
	$('#tree').jstree(true).settings.core.data.push({
		'text': w.label,
		'type': w.type,
		data: w
	});
	$('#tree').jstree(true).refresh();
	
	close_popup();
}




</script>
