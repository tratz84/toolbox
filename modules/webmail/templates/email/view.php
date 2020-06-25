

<?php if ($access_granted == false) : ?>
	<?= t('E-mail is marked as confidential. No access to view e-mail.') ?>
<?php endif; ?>

<?php if ($access_granted) : ?>
	<?php print $form->renderReadonly() ?>
<?php endif; ?>
