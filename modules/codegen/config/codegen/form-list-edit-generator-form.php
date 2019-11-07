<?php

return array (
  'treedata' => '[{"type":"widget","text":"data","data":{"class":"core\\\\forms\\\\HiddenField","label":"","type":"widget","name":"data"}},{"type":"widget","text":"module_name: Module","data":{"class":"core\\\\forms\\\\SelectField","editor":"codegen\\\\form\\\\widgetoptions\\\\SelectOptionsForm","label":"Module","type":"widget","name":"module_name","optionItems":"<? \\n\\n$map = array();\\n$map[\'\'] = \'Make your choice\';\\nforeach(module_list() as $m => $p) {\\n$map[$m] = $m;\\n}\\n\\nreturn $map;\\n"}},{"type":"widget","text":"name: Name","data":{"class":"core\\\\forms\\\\TextField","label":"Name","type":"widget","name":"name"}},{"type":"widget","text":"short_description: Short description","data":{"class":"core\\\\forms\\\\TextField","label":"Short description","type":"widget","name":"short_description"}}]',
  'module_name' => 'codegen',
  'form_name' => 'ListEditGeneratorForm',
  'short_description' => 'used @ ListEditWidget generator',
);

