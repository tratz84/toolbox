<?php

return array (
  'treedata' => '[{"type":"widget","text":"module_name: Module","data":{"class":"core\\\\forms\\\\SelectField","editor":"codegen\\\\form\\\\widgetoptions\\\\SelectOptionsForm","label":"Module","type":"widget","name":"module_name","optionItems":"<? \\nreturn codegen_map_modules();\\n"}},{"type":"widget","text":"controller_name: Controller name","data":{"class":"core\\\\forms\\\\TextField","label":"Controller name","type":"widget","name":"controller_name","info_text":"name may contain slashes"}},{"type":"widget","text":"default_actions: Default actions","data":{"type":"widget","class":"core\\\\forms\\\\TextareaField","editor":"codegen\\\\form\\\\widgetoptions\\\\TextareaOptionsForm","label":"Default actions","name":"default_actions","defaultValue":"index\\nsearch\\nedit\\ndelete","info_text":"default action_-functions + templates created for controller"}}]',
  'module_name' => 'codegen',
  'form_name' => 'ControllerGeneratorForm',
  'short_description' => 'codegen - Generate controller',
);

