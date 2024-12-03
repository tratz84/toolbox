

<form method="post" action="" class="frm-auth">

	<div class="auth-table">
	
		<?php if ($logoFile) : ?>
		<div style="text-align: center; margin: 15px 0 30px;">
			<img src="<?= appUrl('/?m=base&c=auth&a=logo') ?>" style="max-width: 300px;" />
		</div>
		<?php endif; ?>

		<?php if ($showWarningDefaultAdminPassword) : ?>
		<div style="padding: 10px; border: 1px solid #f00; background-color: #fcffe5; font-style: italic; color: #f00;">
			<b>WARNING:</b>
			<br/>username/password set to default,
			<br/>Username: admin
			<br/>Password: admin123
		</div>
		<?php endif; ?>
	
    	<div>
    		<?php if (isset($error)) : ?>
    		<div style="padding: 10px; border: 1px solid #f00; background-color: #fcffe5; font-style: italic; color: #f00; margin-bottom: 15px;">
    			<div class="error"><?= $error ?></div>
    		</div>
    		<?php endif; ?>
    		
    		<div class="input-row">
    			<div class="label"><?= t('Username') ?></div>
    			<div><input type="text" name="username" value="<?= esc_attr($username) ?>" autofocus /></div>
    		</div>
    		
    		<div class="input-row">
    			<div class="label"><?= t('Password') ?></div>
    			<div><input type="password" name="p" value="<?= esc_attr($password) ?>" /></div>
    		</div>
    		<div>
    			<div>
    				<input type="submit" value="<?= t('Logon') ?>" />
    			</div>
    		</div>
    		
    		<div class="remember-me">
				<label>
					<?= render_checkbox('rememberme', ['checked' => $remembermeChecked]) ?>
					
					<?= t('Remember me') ?>
				</label>
    		</div>
    	</div>
    	
    	<br/>
    	<br/>
    	
    	<?php if (ctx()->isResetPasswordEnabled()) : ?>
    	<a href="<?= appUrl('/?m=base&c=auth&a=reset_password') ?>"><?= t('Reset password') ?></a>
    	<?php endif; ?>
	</div>

</form>



<script>

pageLoaded(function() {
	jQuery('input[name=username]').focus();
});

</script>

