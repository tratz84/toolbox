


<form method="post" action="">

	<div class="auth-table">
		
		<h1><?= t('Reset password') ?></h1>
		
		<br/>
		
		<?php if (isset($error)) : ?>
			<div class="error">Error: <?= esc_html($error) ?></div>
			<br/>
			<br/>
			<a href="<?= appUrl('/') ?>"><?= t('Back to home') ?></a>
		<?php elseif (isset($success)) : ?>
			<div><?= t('Success: password set') ?></div>
			<br/>
			<br/>
			<a href="<?= appUrl('/') ?>"><?= t('Back to home') ?></a>
		<?php else : ?>
		
			<?php if (isset($message)) : ?>
			<div class="error">
				<?= esc_html($message) ?>
			</div>
			<?php endif; ?>
		
			<table>
				<tr>
					<th><?= t('Username') ?></th>
					<td>
						<?= esc_html($username) ?>
					</td>
				</tr>
				<tr>
					<th><?= t('New password') ?></th>
					<td><input type="password" name="p1" /></td>
				</tr>
				<tr>
					<th><?= t('Password confirmation') ?></th>
					<td><input type="password" name="p2" /></td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" value="<?= t('Reset password') ?>" />
					</td>
				</tr>
			</table>
		
		<?php endif; ?>
		
	</div>
	
</form>


