

<br/>

<div class="mail-header">

	<div style="float: right; margin-right: 2em;"><?= $dateReceived ?></div>
	<table>
	
		<tr>
			<th>Van</th>
			<td>
				<?php if (count($from)) foreach($from as $e) : ?>
				<div>
					<?= esc_html($e['display']) ?>
					
					&lt;<a href="mailto:<?= esc_attr($e['address'])?>"><?= esc_html($e['address']) ?></a>&gt;
				</div>
				<?php endforeach; ?>
			</td>
		</tr>

		<?php if (count($to)) : ?>
    		<tr>
    			<th>Aan</th>
    			<td>
    			<?php foreach($to as $e) : ?>
    				<div>
    					<?= esc_html($e['display']) ?>
    					&lt;<a href="mailto:<?= esc_attr($e['address'])?>"><?= esc_html($e['address']) ?></a>&gt;
    				</div>
        		<?php endforeach; ?>
    			</td>
    		</tr>
		<?php endif; ?>
		
		<?php if (count($cc)) : ?>
    		<tr>
    			<th>Cc</th>
    			<td>
    			<?php foreach($cc as $e) : ?>
    				<div>
    					<?= esc_html($e['display']) ?>
    					&lt;<a href="mailto:<?= esc_attr($e['address'])?>"><?= esc_html($e['address']) ?></a>&gt;
    				</div>
        		<?php endforeach; ?>
    			</td>
    		</tr>
		<?php endif; ?>
		
		<?php if (count($bcc)) : ?>
    		<tr>
    			<th>Bcc</th>
    			<td>
    			<?php foreach($bcc as $e) : ?>
    				<div>
    					<?= esc_html($e['display']) ?>
    					&lt;<a href="mailto:<?= esc_attr($e['address'])?>"><?= esc_html($e['address']) ?></a>&gt;
    				</div>
        		<?php endforeach; ?>
    			</td>
    		</tr>
		<?php endif; ?>
		
		<tr>
			<th>Onderwerp</th>
			<td><?= esc_html($subject) ?></td>
		</tr>
		
		<?php if (count($attachments)) : ?>
		<tr>
			<th>Bijlages</th>
			<td class="attachments">
				<?php for($attno=0; $attno < count($attachments); $attno++) : ?>
				<a href="<?= appUrl('/?m=webmail&c=inbox&a=attachment&no='.$attno.'&id='.$id) ?>" target="_blank"><?= esc_html($attachments[$attno]->getFilename()) ?></a>
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