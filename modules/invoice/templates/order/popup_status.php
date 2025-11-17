
<div class="page-header">
	
	<div class="toolbox">
		<a href="javascript:void(0);" class="fa fa-times-circle popup-close-link"></a>
	</div>
	
	<h1>Status bijwerken</h1>
</div>

<div class="action-box">
	<span><a href="javascript:void(0);" onclick="order_bulkUpdateStatusCancel();">Annuleer</a></span>
</div>

<hr/>

Kies de status waarnaar u order wilt zetten,

<br/><br/>

<ul>
	<?php foreach($orderStatus as $os) : ?>
	<li>
		<a href="javascript:void(0);" 
			data-orderstatusid="<?= $os->getOrderStatusId() ?>"
			data-description="<?= esc_attr($os->getDescription()) ?>" 
			onclick="order_bulkUpdateStatus(this.dataset.orderstatusid, this.dataset.description);"><?= $os ?></a>
	</li>
	<?php endforeach; ?>
</ul>

