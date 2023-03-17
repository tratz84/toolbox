

<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
		
		<a href="<?= appUrl('/?m=base&c=group&a=edit') ?>" class="fa fa-plus"></a>
	</div>

	<h1><?= t('User group management') ?></h1>
</div>


<div id="tree"></div>


<script>

let json_tree = <?= json_encode( $groupTree, JSON_PRETTY_PRINT ) ?>;

$('#tree').jstree( {'core': {
	'data': json_tree
}} );


</script>



