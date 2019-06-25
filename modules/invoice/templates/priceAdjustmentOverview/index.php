
<div>
    <table class="list-widget">
    	<thead>
    		<tr>
    			<th>Ingangsdatum</th>
    			<th>Prijs</th>
    			<th>Aangemaakt op</th>
    			<th></th>
    		</tr>
    	</thead>
    	
    	<tbody>
    		<?php for($x=0; $x < count($priceAdjustments); $x++) : ?>
    		<?php $pa = $priceAdjustments[$x] ?>
    		
    		<tr class="<?= $pa->getField('active-period') ? 'active-blue' : '' ?>">
    			<td><?= $pa->getStartDateFormat('j') ?> <?= t_lc('month.'.$pa->getStartDateFormat('m')) ?> <?= $pa->getStartDateFormat('Y') ?></td>
    			<td><?= format_price($pa->getNewPrice(), true, ['thousands' => '.']) ?></td>
    			<td><?= $pa->getCreatedFormat('d-m-Y H:i:s') ?></td>
    			<td>
    				<a onclick="return confirmationClickHandler(this, 'Verwijderen', 'Weet u zeker dat u deze prijswijziging wilt verwijderen?');" href="<?= appUrl('/?m=invoice&c=priceAdjustment&a=delete&id='.$pa->getPriceAdjustmentId()).'&back_url='.urlencode($back_url) ?>" class="fa fa-close"></a>
    			</td>
    		</tr>
    		<?php endfor; ?>
    	</tbody>
    	<?php if (count($priceAdjustments) == 0) : ?>
    	<tr>
    		<td colspan="4" style="text-align: center; font-style: italic;">Geen wijzigingen gepland</td>
    	</tr>
    	<?php endif; ?>
    
    </table>
</div>
