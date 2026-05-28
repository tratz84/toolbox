
<div style="padding: 10px;">

    <div class="action-box">
    	<span><a href="javascript:void(0);" onclick="share_wopi_click();"><?= _('Share') ?></a></span>
    </div>
    <hr style="margin-top: 10px;" />
</div>

<?php if (count($was) == 0) : ?>
	<div class="no-results-found">
		<?= _('No results found') ?>
	</div>
<?php else : ?>

<table class="list-response-table">
	<thead>
		<tr>
			<th><?= _('Guest name') ?></th>
			<th><?= _('Writable') ?></th>
			<th><?= _('Created') ?></th>
			<th><?= _('Expires') ?></th>
			<th><?= _('Last accessed') ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($was as $wa) : ?>
		<tr class="wopi-access-token-<?= $wa->getWopiAccessId() ?>">
			<td class="guest-name"><?= esc_html($wa->getGuestName()) ?></td>
			<td><?= $wa->getWritable() ? _('Yes') : _('No') ?></td>
			<td><?= format_datetime($wa->getCreated(), 'd-m-Y H:i:s') ?></td>
			<td><?= date('d-m-Y H:i', intval($wa->getAccessTokenTtl()/1000)) ?></td>
			<td>
				<?php if (valid_datetime($wa->getLastAccessed())) : ?>
					<?= format_datetime($wa->getLastAccessed(), 'd-m-Y H:i:s') ?>
				<?php else : ?>
					-
				<?php endif; ?>
			</td>
			<td class="actions">
				<?php if ($wa->getField('lool_url')) : ?>
				<a href="javascript:void(0);" onclick="copy_lool_url(<?= esc_json_attr($wa->getField('lool_url')) ?>);" class="fa fa-copy"></a>
				<?php endif; ?>
				<a href="javascript:void(0);" onclick="delete_wopi_access(<?= $wa->getWopiAccessId() ?>);" class="fa fa-trash"></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php endif; ?>


<script>

function share_wopi_click() {
	showConfirmation('Add share', `
			<div class="new-wopi-share">
    			<div class="input-writable">
        			<div><b>Write access</b></div>
        			<div>
        				<input type="checkbox" name="write_access" checked="checked" />
        			</div>
        		</div>
				<div class="input-guest-name">
    				<div><b>Guest name</b></div>
    				<div>
    					<input type="text" name="guest_name" value="" />
    					<span class="error hidden">verplicht</span>
    				</div>
    			</div>

    			<div class="input-hours-ttl">
    				<div><b>Hours available</b></div>
    				<div>
    					<input type="number" name="hours_ttl" value="744" min="1" />
    				</div>
    			</div>
			</div>
	`, function() {
		let write_access = $('.new-wopi-share input[name=write_access]').prop('checked');
		let guest_name = $('.new-wopi-share input[name=guest_name]').val().trim();
		let hours_ttl = $('.new-wopi-share input[name=hours_ttl]').val();
		
		if (guest_name == '') {
			$('.input-guest-name .error').removeClass('hidden');
			return false;
		}
		
		$.ajax({
			type: 'POST',
			url: appUrl('/?m=filesync&c=storefile'),
			data: {
				a: 				'add_wopi_access',
				store_file_id: 	$('input[name=store_file_id]').val(),
				write_access: 	write_access ? 1 : 0,
				guest_name: 	guest_name,
				hours_ttl: 		hours_ttl
			},
			success: function() {
				reload_wopi_tbl();
			}
		});
		
	});
}

function reload_wopi_tbl() {
	// TODO: reload..
	$.ajax({
		type: 'POST',
		url: appUrl('/?m=filesync&c=storefile'),
		data: {
			a: 'wopi_share',
			store_file_id: $('input[name=store_file_id]').val()
		},
		success: function(data, xhr, textStatus) {
			$('.tab-name-share').html( data );
		}
	});
}


function delete_wopi_access(id) {
	let guest_name = $('.wopi-access-token-'+id + ' .guest-name').text().trim();
	showConfirmation('Delete', 'Are you sure to remove the selected access token? ' + guest_name + '?', function() {
		$.ajax({
			type: 'POST',
			url: appUrl('/?m=filesync&c=storefile'),
			data: {
				a: 				'delete_wopi_access',
				wopi_access_id:	id
			},
			success: function() {
				reload_wopi_tbl();
			}
		});
		
	});
}


function copy_lool_url( url ) {

	textToClipboard( url );
}



</script>

