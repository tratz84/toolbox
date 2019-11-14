<?php



use core\exception\FileException;
use core\exception\InvalidStateException;

function codegen_map_form_classes() {
    return \codegen\generator\GeneratorHelper::getFormClassesMap();
}

function codegen_map_field_classes() {
    return \codegen\generator\GeneratorHelper::getFieldClassesMap();
}


function codegen_save_config($moduleName, $configfile, $data) {
    $configfile = basename($configfile);
    
    $p = module_file($moduleName, '/');
    if ($p == false) {
        throw new InvalidStateException('Module not found');
    }
    
    $pconfig = module_file($moduleName, '/config');
    if ($pconfig == false) {
        if (mkdir($p . '/config', 0755) == false)
            throw new FileException('Unable to create config-dir');
        
        $pconfig = module_file($moduleName, '/config');
    }
    
    
    return file_put_contents($pconfig.'/'.$configfile, '<?php return '.var_export($data, true).";\n\n");
}


