
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=webmail&c=template') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

    <?php if ($isNew) : ?>
    <h1>Template toevoegen</h1>
    <?php else : ?>
    <h1>Template bewerken</h1>
    <?php endif; ?>
</div>


<?php print $form->render() ?>


<br/><br/>

<h2 style="float: left;">Variabelen</h2>
	<?= infopopup('Onderstaand een overzicht van mogelijke variabelen welke in de mail-template gebruikt kunnen worden. Daadwerkelijk beschikbare variabelen zijn afhankelijk van de situatie.') ?>

<div style="clear: both; height: 10px;"></div>

<table>

	<tr>
		<th>[[naam]]</th>
		<td>Naam contactpersoon, danwel volledige naam persoon, Voornaam + tussenvoegsel + achternaam</td>
	</tr>
	<tr>
		<th>[[naam2]]</th>
		<td>Naam contactpersoon, danwel volledige naam persoon, Voornaam + tussenvoegsel + achternaam</td>
	</tr>
	<tr>
		<th>[[bedrijfsnaam]]</th>
		<td>Naam bedrijf</td>
	</tr>
	<tr>
		<th>[[adres]]</th>
		<td>1e adres (straat + huisnr)</td>
	</tr>
	<tr>
		<th>[[straat]]</th>
		<td>Straatnaam</td>
	</tr>
	<tr>
		<th>[[huisnummer]]</th>
		<td></td>
	</tr>
	<tr>
		<th>[[postcode]]</th>
		<td></td>
	</tr>
	<tr>
		<th>[[woonplaats]]</th>
		<td></td>
	</tr>
	
	<tr>
		<th>[[telefoonnummer]]</th>
		<td>1e telefoonnummer</td>
	</tr>
	<tr>
		<th>[[email]]</th>
		<td>1e emailadres</td>
	</tr>
	<tr>
		<th>[[kvk_nummer]]</th>
		<td>KVK nummer</td>
	</tr>
	<tr>
		<th>[[btw_nummer]]</th>
		<td>Btw nr</td>
	</tr>

	<tr>
		<th>[[betreft]]</th>
		<td>Betreft-veld, offerte / factuur</td>
	</tr>
	<tr>
		<th>[[document_no]]</th>
		<td>Factuur-, offerte- of order-nr, offerte / factuur</td>
	</tr>

	<?php hook_eventbus_publish( $form, 'webmail', 'template-edit-parameters' ) ?>

</table>
