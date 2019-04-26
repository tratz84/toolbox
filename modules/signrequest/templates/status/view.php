
<div class="page-header">
	
	<div class="toolbox">
		<?php if (isset($back_url) && $back_url) : ?>
		<a href="<?= esc_attr(appUrl($back_url)) ?>"><span class="fa fa-chevron-circle-left"></span></a>
		<?php endif; ?>
	</div>
	
	<h1>SignRequest status</h1>
</div>

<?php 
$documentsResponse = $message->getDocumentsResponse();
$jsonDocumentsResponse = @json_decode($documentsResponse);

$signrequestsResponse = $message->getSignrequestsResponse();
$jsonSignrequestsResponse = @json_decode($signrequestsResponse);
?>


<?php if (isset($jsonDocumentsResponse->url) && isset($jsonSignrequestsResponse->url)) : ?>
	SignRequest verstuurd
<?php else : ?>
    <?php if (@$jsonSignrequestsResponse->signers[0]->non_field_errors) : ?>
    	Error: <?= esc_html(implode(', ', $jsonSignrequestsResponse->signers[0]->non_field_errors)) ?>
    <?php else : ?>
    	Error:
    	<div><?= esc_html($documentsResponse) ?></div>
    	<hr/>
    	<div><?= esc_html($signrequestsResponse) ?></div>
    <?php endif; ?>
<?php endif; ?>



