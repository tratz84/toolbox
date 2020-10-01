
<div class="page-header">
	<div class="toolbox list-toolbox">
		<a href="<?= appUrl('/?m=customer&c=person&a=edit') ?>" class="fa fa-user" title="<?= t('New person') ?>"></a>
		<a href="<?= appUrl('/?m=customer&c=company&a=edit') ?>" class="fa fa-building-o" title="<?= t('New company') ?>"></a>
	</div>
	
    <h1><?= t('Overview customers') ?></h1>
</div>



<div id="person-table-container"></div>




<script>

<?= $cit->render() ?>


</script>