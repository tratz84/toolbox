<?php

return array (
  'treedata' => '[{"type":"widget","text":"data","data":{"class":"core\\\\forms\\\\HiddenField","label":"","type":"widget","name":"data"}},{"type":"widget","text":"module_name: Module","data":{"class":"core\\\\forms\\\\SelectField","editor":"codegen\\\\form\\\\widgetoptions\\\\SelectOptionsForm","label":"Module","type":"widget","name":"module_name","optionItems":"<? \\n\\nreturn codegen_map_modules();\\n"}},{"type":"widget","text":"name: Name","data":{"class":"core\\\\forms\\\\TextField","label":"Name","type":"widget","name":"name"}},{"type":"widget","text":"daoObject: DAO Object","data":{"type":"widget","class":"core\\\\forms\\\\SelectField","editor":"codegen\\\\form\\\\widgetoptions\\\\SelectOptionsForm","label":"DAO Object","name":"daoObject","optionItems":"<?php return codegen_map_dao_classes();"}},{"type":"widget","text":"short_description: Short description","data":{"class":"core\\\\forms\\\\TextField","label":"Short description","type":"widget","name":"short_description"}},{"type":"widget","text":"objects_getter: Objects getter-name","data":{"class":"core\\\\forms\\\\TextField","label":"Objects getter-name","type":"widget","name":"objects_getter"}},{"type":"widget","text":"no_results_message: No-results message","data":{"type":"widget","class":"core\\\\forms\\\\CheckboxField","editor":"codegen\\\\form\\\\widgetoptions\\\\CheckboxOptionsForm","label":"No-results message","name":"no_results_message","info_text":"Show \'no results found\'-message when table is empty"}}]',
  'module_name' => 'codegen',
  'form_name' => 'ListEditGeneratorForm',
  'daoClass' => '',
  'key_fields' => '',
  'short_description' => 'codegen - used @ ListEditWidget generator',
);

