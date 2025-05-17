



Status van uw bestelling: <?= $payment->getMollieStatus() ?>

<br/>
<br/>

<?php if ($payment->getReturnUrl()) : ?>

<a id="a-payment" href="<?= $payment->getReturnUrl() ?>">Klik hier om door te gaan...</a>

<script>
setTimeout(function() {
	window.location = $('#a-payment').attr('href');
}, 1000);
</script>

<?php endif; ?>

