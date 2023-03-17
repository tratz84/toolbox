
<style type="text/css">

.user-group-management-tree {
    display: flex;
}
.user-group-management-tree #tree {
    width: 300px;
    max-width: 300px;
}
.user-group-management-tree #group-summary {
    flex-grow: 4;
}

</style>


<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
		
		<a href="<?= appUrl('/?m=base&c=group&a=edit') ?>" class="fa fa-plus"></a>
	</div>

	<h1><?= t('User group management') ?></h1>
</div>




<div class="user-group-management-tree">

	<div id="tree"></div>
	
	<div id="group-summary"></div>
	
</div>


<script>


$(document).ready(function() {
	if (window.history.state && window.history.state.selected_group_id) {
		loadGroupSummary( window.history.state.selected_group_id );
	}
});



let json_tree = <?= json_encode( $groupTree, JSON_PRETTY_PRINT ) ?>;

$('#tree').jstree( {'core': {
	'data': json_tree
}} );
$('#tree').on('select_node.jstree', function(n, data) {
	if ( data && data.node && data.node.id ) {
		console.log( data.node );
		loadGroupSummary( data.node.id );
	}
});
$('#tree').on('dblclick.jstree', function(n, data) {
	console.log('wel hier');
	let l = $('#tree').jstree(true).get_selected(true);
	
	console.log( l );
	
	if (l && l.length > 0) {
		window.location = appUrl('/?m=base&c=group&a=edit&id='+l[0].id);
	}
});

var lgs_xhr = null;
var lgs_id = null;
function loadGroupSummary( id ) {
	if (id == lgs_id)
		return;

	if (lgs_xhr)
		lgs_xhr.abort();

	window.history.replaceState({
		selected_group_id: id
	}, '');
	
	lgs_id = id;
	lgs_xhr = $.ajax({
		type: 'POST',
		url: appUrl('/?m=base&c=group'),
		data: {
			a: 'group_summary',
			group_id: id
		},
		success: function(data, xhr,textStatus) {
			$('#group-summary').html( data );
		}
	});
	
}



</script>



