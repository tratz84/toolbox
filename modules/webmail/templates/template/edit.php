
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
		<th>[[betreft]]</th>
		<td>Betreft-veld, offerte / factuur</td>
	</tr>
	<tr>
		<th>[[document_no]]</th>
		<td>Factuur-, offerte- of order-nr, offerte / factuur</td>
	</tr>

</table>
