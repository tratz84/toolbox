
<div class="page-header">
	<div class="toolbox list-toolbox">
		<a href="<?= appUrl('/?m=customer&c=company&a=edit') ?>" class="fa fa-plus"></a>
	</div>
	
    <h1><?= t('Overview companies') ?></h1>
</div>



<div id="company-table-container"></div>




<script>

<?= $cit->render() ?>

</script>