
<div class="nav-side-menu">
		<div class="mobile-menu-header d-md-none"><a href="<?= appUrl('/') ?>"><?= esc_html($context->getCompanyName()) ?></a></div>
		
		<div class="mobile-icon-container"></div>
		
		<div class="menu-mobile-spacer"></div>
		
	    <i class="fa fa-bars fa-2x toggle-btn" data-toggle="collapse" data-target="#menu-content"
	    										data-bs-toggle="collapse" data-bs-target="#menu-content"></i>
	
		<div class="menu-list">
			

			<ul id="menu-content" class="menu-content collapse out">
			<?php $activeSet = false ?>
    		<?php if (isset($menuItems)) foreach($menuItems as $mi) : ?>
    			<?php
    			 if ($activeSet == false && $mi->isActive()) {
    			     $active = true;
    			     $activeSet = true;
    			 } else {
        			 $active = false;
    			 }
    			?>
				<li class="menu-item <?= $mi->getField('css_class') ?>">
					<a class="nav-link <?= $active ? 'active' : '' ?> weight-<?= $mi->getWeight() ?>" 
						href="<?= appUrl($mi->getUrl()) ?>"
						title="<?= esc_attr($mi->getLabel()) ?>">
						<i class="fa <?= $mi->getIcon() ?>"></i> 
						<span class="menu-label"><?= esc_html($mi->getLabel()) ?></span>
					</a>
					
					<?php if ($mi->hasChildMenus()) : ?>
					<?php $childItems = $mi->getChildMenus() ?>
					<?php if ($mi->menuAsFirstChild()) $childItems = array_merge(array($mi), $childItems) ?>
					<ul class="child-menu">
    					<?php foreach($childItems as $ci) : ?>	
    					<li>
    						<a class="nav-link weight-<?= $ci->getWeight() ?>" href="<?= appUrl($ci->getUrl()) ?>">
        						<i class="fa <?= $ci->getIcon() ?>"></i> 
        						<span class="menu-label">
        							<?= esc_html($ci->getSubmenuLabel() ? $ci->getSubmenuLabel() : $ci->getLabel()) ?>
        						</span>
        					</a>
    					</li>
    					<?php endforeach; ?>
					</ul>
					<?php endif; ?>
				</li>
    		<?php endforeach; ?>
    		
				<li class="menu-item menu-item-sign-out d-md-none">
					<a class="nav-link" 
						href="<?= appUrl('/?m=base&c=auth&a=logoff') ?>"
						title="<?= t('Log out') ?>">
						<i class="fa fa-sign-out"></i> 
						<span class="menu-label"><?= esc_html(t('Log out')) ?></span>
					</a>
    			</li>
			</ul>
		</div>
	</div>
