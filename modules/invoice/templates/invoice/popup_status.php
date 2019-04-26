
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

Kies de status waarnaar u <?= strtolower(strOrder(1)) ?> wilt zetten,

<br/><br/>

<ul>
	<?php foreach($invoiceStatus as $is) : ?>
	<li>
		<a href="javascript:void(0);" 
			data-invoicestatusid="<?= $is->getInvoiceStatusId() ?>"
			data-description="<?= esc_attr($is->getDescription()) ?>" 
			onclick="bulkUpdateStatus(this.dataset.invoicestatusid, this.dataset.description);"><?= $is ?></a>
	</li>
	<?php endforeach; ?>
</ul>

