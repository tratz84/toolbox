<?php

return array (
  'data' => '[{"type":"widget","class":"core\\\\forms\\\\SelectField","editor":"codegen\\\\form\\\\widgetoptions\\\\SelectOptionsForm","label":"Betaalmethode","name":"payment_method_id","text":"payment_method_id: Betaalmethode","optionItems":"<?php return payment_method_map();"},{"type":"widget","class":"core\\\\forms\\\\TextField","label":"Opmerking","name":"description1","text":"description1: Opmerking","defaultValue":""},{"type":"widget","class":"core\\\\forms\\\\EuroField","label":"Bedrag","name":"amount","text":"amount: Bedrag"}]',
  'module_name' => 'payment',
  'name' => 'PaymentLineListEdit',
  'short_description' => 'Payment line editor',
  'objects_getter' => 'PaymentLines',
);

