<?php




function fail2ban_enabled() {
    return ctx()->getSetting('fail2ban__enabled', true);
}

