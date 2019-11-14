<?php

return array (
  'treedata' => '[{"type":"widget","text":"module_name: Module","data":{"class":"core\\\\forms\\\\SelectField","editor":"codegen\\\\form\\\\widgetoptions\\\\SelectOptionsForm","label":"Module","type":"widget","name":"module_name","optionItems":"<? \\n\\n$map = array();\\n$map[\'\'] = \'Make your choice\';\\nforeach(module_list() as $m => $p) {\\n$map[$m] = $m;\\n}\\n\\nreturn $map;\\n"}},{"type":"widget","text":"codegen\\\\form\\\\UserCapabilityListEdit","data":{"class":"codegen\\\\form\\\\UserCapabilityListEdit","editor":"codegen\\\\form\\\\widgetoptions\\\\ListEditWidgetsOptionsForm","label":"codegen\\\\form\\\\UserCapabilityListEdit","type":"widget"}}]',
  'module_name' => 'codegen',
  'form_name' => 'ModuleUserCapabilityForm',
  'short_description' => 'codegen - edit user capabilities',
);

