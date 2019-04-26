




<form method="post" action="">

	<div class="auth-table">
	
    	<table>
    		<tr>
    			<th>Gebruikersnaam</th>
    			<td><input type="text" name="username" value="<?= esc_attr($username) ?>" autofocus /></td>
    		</tr>
    		
    		<tr>
    			<th>Wachtwoord</th>
    			<td><input type="password" name="p" value="<?= esc_attr($password) ?>" /></td>
    		</tr>
    		<?php if (is_post()) : ?>
    		<tr>
    			<td colspan="2" class="error">Onjuiste login/wachtwoord</td>
    		</tr>
    		<?php endif; ?>
    		<tr>
    			<td colspan="2" align=right>
    				<input type="submit" value="Aanmelden" />
    			</td>
    		</tr>
    	</table>
	</div>

</form>



<script>

$(document).ready(function() {
	jQuery('input[name=username]').focus();
});

</script>

