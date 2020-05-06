

<div class="filesync-mailbox-import-container">
    <div class="page-header">
    	<div class="toolbox">
    		<a href="javascript:void(0);" class="fa fa-times-circle popup-close-link"></a>
    	</div>
    	
    	<h1>Create archive file</h1>
    </div>


    <table>
    
    	<tr>
    		<th><?= t('Date') ?></th>
    		<td><?= esc_html($mail->getDate()) ?></td>
    	</tr>
    	<tr>
    		<th><?= t('Subject') ?></th>
    		<td><?= esc_html($mail->getSubject()) ?></td>
    	</tr>
    	<tr>
    		<th><?= t('From') ?></th>
    		<td><?= esc_html($mail->getFromName()) ?> &lt;<?= esc_html($mail->getFromEmail()) ?>&gt;</td>
    	</tr>
    </table>
    
    <br/>
    
    <?php if ($importAvailable) : ?>
    	<div class="form-generator">
    	    <?= $selectArchiveStore->render() ?>
    	</div>
    	
        <br/>
        
        <table>
        	<?php for($x=0; $x < count($attachments); $x++) : ?>
        	<?php 
        	   $ac =  new \core\container\ArrayContainer();
        	   $ac->setAttribute('mail', $mail);
        	   $ac->setAttribute('attachment', $attachments[$x]);
        	   $ac->setAttribute('attachment-no', $x);
        	   
        	   hook_eventbus_publish($ac, 'filesync', 'mailbox-attachment');
        	?>
        	<tr>
        		<th>Attachment #<?= $x+1 ?></th>
        		<td>
    				<?= $attachments[$x]['filename'] ? $attachments[$x]['filename'] : 'Not named' ?>
        		</td>
        		<td style="padding-left: 20px;">
        			<a href="javascript:void(0);" data-email-id="<?= esc_attr($mail->getId()) ?>" data-attachment-no="<?= $x ?>" onclick="filesync_mailbox_Click(this);">
        				<span class="fa fa-download"></span>
        				Filesync
        			</a>
        		</td>
        		<?php foreach($ac->getItems() as $i) : ?>
        		<td style="padding-left: 20px;">
        			<?= $i ?>
        		</td>
        		<?php endforeach; ?>
        	</tr>
        	<?php endfor; ?>
        	
        	<?php if ($htmlToPdfAvailable) : ?>
        	<?php 
        	   $ac =  new \core\container\ArrayContainer();
        	   $ac->setAttribute('mail', $mail);
        	   hook_eventbus_publish($ac, 'filesync', 'mailbox-mail');
        	?>
        	
        	<tr>
        		<th>E-mail</th>
        		<td>
        			E-mail as PDF
            	</td>
            	<td style="padding-left: 20px;">
            		<a href="javascript:void(0);" data-email-id="<?= esc_attr($mail->getId()) ?>" data-attachment-no="-1" onclick="filesync_mailbox_Click(this);">
	            		<span class="fa fa-download"></span>
            			Filesync
            		</a>
            	</td>
            	
        		<?php foreach($ac->getItems() as $i) : ?>
        		<td style="padding-left: 20px;">
        			<?= $i ?>
        		</td>
        		<?php endforeach; ?>
        		
        	</tr>
        	<?php endif; ?>
        </table>
    <?php endif; ?>
    
    <?php if ($importAvailable == false) : ?>
    	Nothing to import
    <?php endif; ?>
</div>


<script>

function filesync_mailbox_Click(anch, redir) {
	var email_id = $(anch).data('email-id');
	var attachment_no = $(anch).data('attachment-no');
	var storeid = $('.filesync-mailbox-import-container [name=store_id]').val();

	var url = appUrl('/?m=filesync&c=hooks/mailbox&a=import&email_id=' + email_id + '&attachmentNo=' + attachment_no + '&store_id=' + storeid);

	if (redir)
		url = url + '&redir=' + redir;
	
	window.open(url, '_blank');

	close_popup();
}


</script>





