



<div style="width: 500px; margin: 30px auto 0;">

	<?php if (isset($fatal_error)) : ?>
		<h1>Error</h1>
		
		<?= $fatal_error ?>
	<?php endif; ?>

	<?php if (isset($fatal_error) == false) : ?>
	<form method="post">
		
		<h1><?= t('Two factor authentication') ?></h1>
		
		An activation e-mail has been sent to <?= $masked_email ?>
	
		<br/><br/>
		
		<table style="">
			<tr>
				<th style="padding-right: 15px;">
					Code
				</th>
				<td>
					<input type="text" name="c" />
				</td>
			</tr>
			<tr>
				<td colspan="2" align="right">
					<label>
    					<input type="checkbox" name="remember_me" <?= get_var('remember_me', true) ? 'checked=checked':'' ?> />
    					<?= t('Remember me on this device') ?>
					</label>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="right" style="padding-top: 10px;">
            		<input type="submit" name="btnNewCode" value="New code" />
	        		<input type="submit" name="btnNext"    value="Next" />
				</td>
			</tr>
		</table>
	</form>
	
	<script>
	$(document).ready(function() {
		$('[name=c]').focus();
	});

	</script>
	
	
	<?php endif; ?>

</div>


