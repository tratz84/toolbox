


<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=securemail&c=secureMessage&a=edit') ?>" class="fa fa-plus"></a>
	</div>

	<h1><?= t('Secure messages') ?></h1>
</div>


<?= $form->render() ?>

<div class="clear"></div>

<hr/>


<div id="object-container-crit"></div>


<script>

<?= $it->render() ?>


</script>
