

<fieldset class="customer-widget company-widget">
	<legend class="name">
		<span class="toolbox">
			<?php if ($company->getDeleted() == false) : ?>
			<a href="<?= appUrl('/?m=base&c=company&a=edit&company_id='.$company->getCompanyId())?>"><span class="fa fa-pencil"></span></a>
			<?php endif; ?>
		</span>
		<?= esc_html($company->getCompanyName()) ?>
	</legend>
	
	<?php if ($company->getContactPerson()) : ?>
	<div>
		<?= esc_html($company->getContactPerson()) ?>
	</div>
	<?php endif; ?>
	
	<div>
		<?php foreach($company->getAddressList() as $a) : ?>
    		<div>
    			<?= esc_html($a->getStreet()) ?>
    			<?= esc_html($a->getStreetNo()) ?>
    		</div>
    		<div>
    			<?= esc_html($a->getZipcode()) ?>
    			<?= esc_html($a->getCity()) ?>
			</div>
    		<?php break;?>
		<?php endforeach; ?>
	</div>

	<table>
		<?php foreach($company->getEmailList() as $e) : ?>
		<tr>
			<td>E-mail</td>
			<td class="email"><a href="mailto:<?= esc_attr($e->getEmailAddress()) ?>"><?= esc_html($e->getEmailAddress()) ?></a></td>
			<td class="note">
				<?php if (trim($e->getNote())) : ?>
					<?= esc_html($e->getNote()) ?>
				<?php endif ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<table>
		<?php foreach($company->getPhoneList() as $p) : ?>
		<tr>
			<td>Tel.</td>
			<td class="phonenr">
				<a href="tel:<?= esc_attr($p->getPhonenr()) ?>"><?= esc_html($p->getPhonenr()) ?></a>
				<?php if (trim($p->getNote())) : ?>
					(<?= esc_html($p->getNote()) ?>)
				<?php endif ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	
	
	<?php if ($company->getDeleted()) : ?>
	<div class="object-deleted">
		Dit bedrijf is verwijderd
	</div>
	<?php endif; ?>

</fieldset>

