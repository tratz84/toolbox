
<fieldset class="customer-widget company-widget">
    <?php if ($person) : ?>
    	<legend class="name">
    		<span class="toolbox">
    			<?php if ($person->getDeleted() == false) : ?>
    			<a href="<?= appUrl('/?m=customer&c=person&a=edit&person_id='.$person->getPersonId())?>"><span class="fa fa-pencil"></span></a>
    			<?php endif; ?>
    		</span>
    		<?= esc_html($person->getFullname()) ?>
    	</legend>
    	
    	<div>
    		<?php foreach($person->getAddressList() as $a) : ?>
    		<div class="street">
    			<?= esc_html($a->getStreet()) ?>
    			<?= esc_html($a->getStreetNo()) ?>
    		</div>
    		<div class="zipcode-city">
    			<?= esc_html($a->getZipcode()) ?>
    			<?= esc_html($a->getCity()) ?>
    		</div>
    		<?php break; ?>
    		<?php endforeach; ?>
    	</div>
    
    	<table>
    		<?php foreach($person->getEmailList() as $e) : ?>
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
    		<?php foreach($person->getPhoneList() as $p) : ?>
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
    <?php endif; ?>
	
	<?php if ($person == null || $person->getDeleted()) : ?>
	<div class="object-deleted">
		<?= t('This person is deleted') ?>
		<?php if (isset($person_id) && $person_id) : ?>
		(person-<?= $person_id ?>)
		<?php endif; ?>
	</div>
	<?php endif; ?>
	

</fieldset>

