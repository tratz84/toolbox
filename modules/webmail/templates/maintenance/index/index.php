
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=base&c=masterdata/index') ?>"
			class="fa fa-chevron-circle-left"></a>
	</div>

	<h1><?= t('E-mail maintenance')?></h1>
</div>





<div class="functions-menu-page">


	<div class="col-xs-12 col-sm-4 col-lg-3 setting-menu-tag-container">
		<h2><?= t('Misc') ?></h2>
		<ul class="">
			<li>
				<a href="<?= appUrl('/?m=webmail&c=maintenance/updateaction') ?>"><?= t('Update action') ?></a>
			</li>
			<li>
				<a href="<?= appUrl('/?m=webmail&c=maintenance/purgejunk') ?>"><?= t('Purge junk') ?></a>
			</li>
		</ul>
	</div>

	<div class="clearfix"></div>
</div>