
<div class="page-header">
	<div class="toolbox">
		<a href="javascript:void(0);" class="fa fa-times-circle popup-close-link"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>
	
	<h1><?= t('New customer') ?></h1>
</div>

<?php



$ftc = generate_tabs('customer', 'popup-newCustomer', null);

if ($showCompany) {
    $ftc->addTab(t('New company'), '<div class="popup-error-list-container"></div>'.$companyForm->render(), 10, ['name' => 'company']);
}
if ($showPerson) {
    $ftc->addTab(t('New person'), '<div class="popup-error-list-container"></div>'.$personForm->render(), 20, ['name' => 'person']);
}

$ftc->render();

