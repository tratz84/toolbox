

<div>
	<div class="toolbox">
		<a href="<?= appUrl('/?m=base&c=group&a=edit&id='.$group->getUserGroupId()) ?>" class="fa fa-edit"></a>
	</div>
	
	<h2><?= esc_html($title) ?></h2>
</div>

<hr/>

<h3><?= t('Users') ?></h3>
<table class="list-response-table">
	<thead>
		<tr>
			<th>Username</th>
			<th>Name</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach( $lrUsers->getObjects() as $u ) : ?>
		<tr class="clickable" onclick="window.location = appUrl('/?m=base&c=user&a=edit&user_id=<?= $u['user_id'] ?>')">
			<td><?= esc_html($u['username']) ?></td>
			<td>
				<?= esc_html( $u['firstname'] . ' ' . $u['lastname'] ) ?>
			</td>
		</tr>
		<?php endforeach;?>
		<?php if ($lrUsers->getRowCount() == 0) : ?>
		<tr>
			<td class="no-results-found" colspan="2"><?= t('No results found') ?></td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>

<br/>
<br/>
<h3><?= t('Capabilities') ?></h3>

<table class="list-response-table">
	<thead>
		<tr>
			<th><?= t('Capability') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach( $group->getCapabilities() as $c ) : ?>
		<?php $cap = $allCapabilities[$c->getModuleName() . '_' . $c->getCapabilityCode() ]; ?>
		<tr>
			<td>
				<?php print t('modulename.'.$cap['module_name']) . ' - ' . $cap['short_description'] ?>
			</td>
		</tr>
		<?php endforeach; ?>
		<?php if (count($group->getCapabilities()) == 0) : ?>
		<tr>
			<td class="no-results-found" colspan="2"><?= t('No results found') ?></td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>


