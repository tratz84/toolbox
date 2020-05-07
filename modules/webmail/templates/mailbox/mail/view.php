
<?php if (get_var('skip_mailactions') == false) : ?>
	<?= include_component('webmail', 'mailbox/mail', 'mailactions', ['emailId' => $id]) ?>
<?php endif; ?>

<div style="font-family: -apple-system,BlinkMacSystemFont, 'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol','Noto Color Emoji' ">

    <br/>
    
    <div class="mail-header">
    
    	<div style="float: right; margin-right: 2em;"><?= $date ?></div>
    	<table>
    	
    		<tr>
    			<th align="left"><?= t('From') ?></th>
    			<td>
    				<div>
    					<?= esc_html($fromName) ?>
    					
    					&lt;<a href="mailto:<?= esc_attr($fromEmail)?>"><?= esc_html($fromEmail) ?></a>&gt;
    				</div>
    			</td>
    		</tr>
    
    		<?php if (count($to)) : ?>
        		<tr>
        			<th align="left"><?= t('To1') ?></th>
        			<td>
        			<?php foreach($to as $e) : ?>
        				<div>
        					<?= esc_html($e['name']) ?>
        					&lt;<a href="mailto:<?= esc_attr($e['email'])?>"><?= esc_html($e['email']) ?></a>&gt;
        				</div>
            		<?php endforeach; ?>
        			</td>
        		</tr>
    		<?php endif; ?>
    		
    		<?php if (count($cc)) : ?>
        		<tr>
        			<th align="left">Cc</th>
        			<td>
        			<?php foreach($cc as $e) : ?>
        				<div>
        					<?= esc_html($e['name']) ?>
        					&lt;<a href="mailto:<?= esc_attr($e['email'])?>"><?= esc_html($e['email']) ?></a>&gt;
        				</div>
            		<?php endforeach; ?>
        			</td>
        		</tr>
    		<?php endif; ?>
    		
    		<?php if (count($bcc)) : ?>
        		<tr>
        			<th align="left">Bcc</th>
        			<td>
        			<?php foreach($bcc as $e) : ?>
        				<div>
        					<?= esc_html($e['name']) ?>
        					&lt;<a href="mailto:<?= esc_attr($e['email'])?>"><?= esc_html($e['email']) ?></a>&gt;
        				</div>
            		<?php endforeach; ?>
        			</td>
        		</tr>
    		<?php endif; ?>
    		
    		<tr>
    			<th align="left"><?= t('Subject') ?></th>
    			<td><?= esc_html($subject) ?></td>
    		</tr>
    		
    		<?php if (count($attachments)) : ?>
    		<tr>
    			<th align="left"><?= t('Attachments') ?></th>
    			<td class="attachments">
    				<?php for($attno=0; $attno < count($attachments); $attno++) : ?>
    				<a href="<?= appUrl('/?m=webmail&c=mailbox%2Fmail&a=attachment&no='.urlencode($attno).'&id='.urlencode($id)) ?>" target="_blank"><?= esc_html($attachments[$attno]['filename']) ?></a>
    				<?php endfor; ?>
    			</td>
    		</tr>
    		<?php endif; ?>
    		
    	</table>
    	
    	<hr />
    </div>
    
    <?php if (isset($html)) : ?>
    	<?php print $html ?>
    <?php else : ?>
    	<pre><?= esc_html($text) ?></pre>
    <?php endif; ?>

	<?php foreach ($attachmentsRendered as $ar) : ?>
		<hr/>
		<?= $ar ?>
	<?php endforeach; ?>

</div>
