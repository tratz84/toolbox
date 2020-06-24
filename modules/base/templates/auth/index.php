




<form method="post" action="">

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
	
    	<table>
    		<tr>
    			<th><?= t('Username') ?></th>
    			<td><input type="text" name="username" value="<?= esc_attr($username) ?>" autofocus /></td>
    		</tr>
    		
    		<tr>
    			<th><?= t('Password') ?></th>
    			<td><input type="password" name="p" value="<?= esc_attr($password) ?>" /></td>
    		</tr>
    		<tr>
    			<th></th>
    			<td>
    				<label>
    					<?= t('Remember me') ?>
    					<?= render_checkbox('rememberme', ['checked' => $remembermeChecked]) ?>
					</label>
    			</td>
    		</tr>
    		<?php if (isset($error)) : ?>
    		<tr>
    			<td colspan="2" class="error"><?= $error ?></td>
    		</tr>
    		<?php endif; ?>
    		<tr>
    			<td colspan="2" align=right>
    				<input type="submit" value="<?= t('Logon') ?>" />
    			</td>
    		</tr>
    	</table>
    	
    	<br/>
    	<br/>
    	
    	<?php if (ctx()->isResetPasswordEnabled()) : ?>
    	<a href="<?= appUrl('/?m=base&c=auth&a=reset_password') ?>"><?= t('Reset password') ?></a>
    	<?php endif; ?>
	</div>

</form>



<script>

$(document).ready(function() {
	jQuery('input[name=username]').focus();
});

</script>

