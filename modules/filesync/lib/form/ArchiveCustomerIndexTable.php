<?php


namespace filesync\form;


use core\forms\lists\IndexTable;
use core\exception\InvalidStateException;

class ArchiveCustomerIndexTable extends IndexTable {
    
    protected $companyId = null;
    protected $personId = null;
    
    public function __construct() {
        parent::__construct();
        
        $this->setColumn('path',              ['fieldDescription' => t('Filename'), 'fieldType' => 'text', 'searchable' => true]);
        $this->setColumn('subject',           ['fieldDescription' => t('Subject'),  'fieldType' => 'text', 'searchable' => true]);
        $this->setColumn('public',            ['fieldDescription' => t('Public'),   'fieldType' => 'boolean']);
        $this->setColumn('filesize_text',     ['fieldDescription' => t('File size')]);
        $this->setColumn('document_date',     ['fieldDescription' => t('Date'),      'fieldType' => 'date']);
        
    }
    
    
    public function setCompanyId( $id ) { $this->companyId = $id; }
    public function setPersonId( $id ) { $this->personId = $id; }
    
    
    public function render() {
        if ($this->companyId == null && $this->personId == null) {
            throw new InvalidStateException('No customer id set');
        }
        
        $connectorUrl = '/?m=filesync&c=archiveOverview&a=search';
        if ($this->companyId) {
            $connectorUrl .= '&companyId='.$this->companyId;
        }
        if ($this->personId) {
            $connectorUrl .= '&personId='.$this->personId;
        }
        $this->setConnectorUrl( $connectorUrl );
        
        
        return parent::render();
    }
    
}

