<?php
use base\model\Menu;

hook_eventbus_subscribe('masterdata', 'menu', function ($src) {

    $src->addItem( t('Data import'), t('Start import'), '/?m=dataimport&c=formSelect');
});

