
<div class="notifications-right">
	<span class="current-user"><?= ctx()->getUser() ?></span>
    
    <span class="action-items">
        <?php if (DEBUG) : ?>
        <span class="action-item">
            <a href="javascript:void(0);" onclick="show_debug_info();" class="fa fa-bug" title="Debug info"></a>
        </span>
        <?php endif; ?>
        <span class="action-item">
            <a href="<?= appUrl('/?m=base&c=auth&a=logoff') ?>" onclick="return confirm('Weet je zeker dat je je wilt afmelden?');" class="fa fa-sign-out" title="Afmelden"></a>
        </span>
    </span>
</div>
