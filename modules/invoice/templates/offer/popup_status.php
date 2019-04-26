
<div class="page-header">
	
	<div class="toolbox">
		<a href="javascript:void(0);" class="fa fa-times-circle popup-close-link"></a>
	</div>
	
	<h1>Status bijwerken</h1>
</div>

<div class="action-box">
	<span><a href="javascript:void(0);" onclick="bulkUpdateStatusCancel();">Annuleer</a></span>
</div>

<hr/>

Kies de status waarnaar u offerte wilt zetten,

<br/><br/>

<ul>
	<?php foreach($offerStatus as $os) : ?>
	<li>
		<a href="javascript:void(0);" 
			data-offerstatusid="<?= $os->getOfferStatusId() ?>"
			data-description="<?= esc_attr($os->getDescription()) ?>" 
			onclick="bulkUpdateStatus(this.dataset.offerstatusid, this.dataset.description);"><?= $os ?></a>
	</li>
	<?php endforeach; ?>
</ul>

