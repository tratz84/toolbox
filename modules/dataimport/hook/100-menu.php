<?php
use base\model\Menu;

hook_eventbus_subscribe('masterdata', 'menu', function ($src) {

    $src->addItem( t('Tools'), t('Data importer'), '/?m=dataimport&c=formSelect');
});

