
<div class="page-header">

	<h1>BTW nummer informatie</h1>
</div>


<form method="get" action="<?= appUrl('/') ?>">
	<input type="hidden" name="m" value="base" />
	<input type="hidden" name="c" value="company" />
	<input type="hidden" name="a" value="view_vat_number" />
	
    <input type="text" name="nr" value="<?= esc_attr($nr) ?>" />
    <input type="submit" value="Opzoeken" />
</form>

<br/>

<?php if (isset($error)) : ?>
Er is een fout opgetreden: <?= esc_html($error) ?>
<?php endif; ?>

<?php if ($validVat) : ?>
	<div class="widget">
		<div style="font-weight: bold;">Btw nr</div>
		<?= esc_html($vatInfo->countryCode) ?><?= esc_html($vatInfo->vatNumber) ?>
	</div>
	<br/>
	<div class="widget">
		<div style="font-weight: bold;">Naam</div>
		<?= esc_html($vatInfo->name) ?>
	</div>
	<br/>
	<div class="widget">
		<div style="font-weight: bold;">Adres</div>
		<?= nl2br(esc_html(trim($vatInfo->address))) ?>
	</div>
<?php else : ?>
	<div class="error">
		Onjuist BTW nummer opgegeven
	</div>
<?php endif; ?>





