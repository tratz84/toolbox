


<form method="post" action="">

	<div class="auth-table">
		
		<h1><?= t('Reset password') ?></h1>
		
		<br/>
		
		<?php if (is_get()) : ?>
    		<div style="font-weight: bold;">
    			<?= t('Username or e-mail') ?>
    		</div>
    		<div>
    			<input type="text" name="id" style="width: 100%" value="" autofocus />
    		</div>
    		
    		<div style="margin-top: 5px;">
    			<input type="submit" value="<?= t('Reset') ?>" />
    		</div>
		<?php endif; ?>
		
		<?php if (is_post()) : ?>
			<?php if (isset($error)) : ?>
				<div class="error">Error: <?= esc_html($error) ?></div>
			<?php else : ?>
			<?= t('If the given username or e-mail is valid, you should receive an e-mail within seconds') ?>
			<?php endif; ?>
			<br/>
			<br/>
			<a href="<?= appUrl('/') ?>"><?= t('Back to login page') ?></a>
		<?php endif; ?>
		
	
	</div>
	
</form>

