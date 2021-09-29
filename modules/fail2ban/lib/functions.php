<?php




function fail2ban_enabled() {
    return ctx()->getSetting('fail2ban__enabled', true);
}

function fail2ban_max_attempts_ip() {
    return (int)ctx()->getSetting('fail2ban__max_attempts_ip', 10);
}
function fail2ban_max_attempts_ip_timespan() {
    $r = (int)ctx()->getSetting('fail2ban__max_attempts_ip_timespan', 5);
    if ($r <= 0)
        $r = 1;
    return $r;
}


function fail2ban_max_attempts_network() {
    return ctx()->getSetting('fail2ban__max_attempts_network', 60);
}
function fail2ban_max_attempts_network_timespan() {
    $r = ctx()->getSetting('fail2ban__max_attempts_network_timespan', 60);
    if ($r <= 0)
        $r = 1;
    return $r;
}

