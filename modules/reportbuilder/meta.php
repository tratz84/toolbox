<?php


if (ctx()->isExperimental() || is_standalone_installation()) {
    return new core\module\ModuleMeta('reportbuilder', 'Report builder', '');
}
