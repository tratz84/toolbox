<?php

namespace mollie\list;

use function base\model\User\hasCapability;
use core\forms\lists\IndexTable;
use function t;


class MolliePaymentIndexTable extends IndexTable {
    
    
    public function __construct() {
        parent::__construct();
        
        
        $this->setContainerId('#mollie-payment-container');
        $this->setConnectorUrl('/?m=mollie&c=paymentlist&a=search');
        
        $this->setRowClick("function(row, evt) {
            var type = $(row).data('record').type;
            window.location = appUrl('/?m=mollie&c=paymentlist&a=view&id=' + $(row).data('record').mollie_payment_id);
        }");
        
        
        $this->setColumn('description', [
            'fieldDescription' => t('Description'),
            'fieldType' => 'text'
        ]);
        $this->setColumn('amount', [
            'fieldDescription' => t('Amount'),
            'fieldType' => 'currency'
        ]);
        
        $this->setColumn('mollie_status', [
            'fieldDescription' => t('Status'),
            'fieldType' => 'text'
        ]);
        
        $this->setColumn('created', [
            'fieldDescription' => t('Created'),
            'fieldType' => 'datetime'
        ]);
        
    }
}





