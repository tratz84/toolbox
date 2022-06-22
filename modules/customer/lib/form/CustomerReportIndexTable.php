<?php


namespace customer\form;


use core\forms\lists\IndexTable;

class CustomerReportIndexTable extends IndexTable {
    
    public function __construct() {
        parent::__construct();
        
        $this->setContainerId('object-container-crit');
        $this->setConnectorUrl( appUrl('/?m=customer&c=report/customerReport&a=search') );
        
        $this->setColumn('customer_id', [
            'width' => 40
            , 'fieldDescription' => 'Id'
            , 'fieldType' => 'text'
            , 'render' => "function(record) {
                if (record.person_id) {
                    return record.person_id;
                }
                
                return record.company_id;
            }"
        ]);
        $this->setColumn('type', [
            'width' => 40
            , 'fieldDescription' => 'Type'
            , 'fieldType' => 'text'
            , 'render' => "function(r) {
                if (r.person_id) {
                    return 'Particulier';
                }
                
                return 'Bedrijf';
            }"
        ]);
        
        $this->setColumn('name', [
            'fieldDescription' => 'Klant'
            , 'fieldType' => 'text'
            , 'render' => "function(record) {
                return format_customername(record);
            }"
        ]);
        $this->setColumn('coc_number', [
            'fieldDescription' => 'Kvk nr'
            , 'fieldType' => 'text'
        ]);
        $this->setColumn('vat_number', [
            'fieldDescription' => 'Btw nr'
            , 'fieldType' => 'text'
        ]);
        $this->setColumn('iban', [
            'fieldDescription' => 'IBAN'
            , 'fieldType' => 'text'
        ]);
        $this->setColumn('bic', [
            'fieldDescription' => 'BIC'
            , 'fieldType' => 'text'
        ]);
        
        $this->setColumn('street', [
            'fieldDescription' => 'Straat'
            , 'fieldType' => 'text'
            , 'render' => "function(record) {
                    var t = '';
                    
                    if (record.street)
                        t += record.street;
                    if (record.street_no)
                        t = t + ' ' + record.street_no;
                    
                    return t;
                }"
        ]);
        
        $this->setColumn('zipcode', [
            'fieldDescription' => 'Postcode'
            , 'fieldType' => 'text'
        ]);
        $this->setColumn('city', [
            'fieldDescription' => 'Plaats'
            , 'fieldType' => 'text'
        ]);
        $this->setColumn('email_address', [
            'fieldDescription' => 'E-mail'
            , 'fieldType' => 'text'
        ]);
        $this->setColumn('phonenr', [
            'fieldDescription' => 'Telnr'
            , 'fieldType' => 'text'
        ]);
        
        
        $this->setRowClick("function(row, evt) {
                                var record = $(row).data('record');
                                
                                if (record.person_id) {
                                    window.open(appUrl('/?m=customer&c=person&a=edit&person_id=' + record.person_id), '_blank');
                                }
                                if (record.company_id) {
                                    window.open(appUrl('/?m=customer&c=company&a=edit&company_id=' + record.company_id), '_blank');
                                }
                            }");
    }
    
    
}


