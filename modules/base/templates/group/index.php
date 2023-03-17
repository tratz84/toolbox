

<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
		
		<a href="<?= appUrl('/?m=base&c=group&a=edit') ?>" class="fa fa-plus"></a>
	</div>

	<h1><?= t('User group management') ?></h1>
</div>


<div>
	<div id="tree"></div>
	
	<div id="group-summary"></div>
	
</div>


<script>

let json_tree = <?= json_encode( $groupTree, JSON_PRETTY_PRINT ) ?>;

$('#tree').jstree( {'core': {
	'data': json_tree
}} );
$('#tree').on('select_node.jstree', function(n, data) {
	if ( data.node ) {
		
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

</script>



