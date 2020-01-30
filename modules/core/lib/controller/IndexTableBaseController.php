<?php

namespace core\controller;

use core\controller\BaseController;
use core\parser\SqlQueryParser;
use core\forms\lists\ListResponse;


class IndexTableBaseController extends BaseController {
    
    protected $daoClass = null;
    protected $query = null;
    protected $indexTableColumns = array();
    protected $exportColumns = array();
    protected $htmlColumns = array();
    
    
    protected function search() {
        $p = new SqlQueryParser();
        $p->parseQuery( $this->query );
        
        // TODO: add filters
//         $p->addWhere($str);

        $sql = $p->toString();
        
        $dc = new $this->daoClass();
        /** @var \core\db\query\MysqlCursor */
        $cursor = $dc->queryCursor( $sql );
        
        $pageNo = (int)get_var('pageNo');           // if not set, casts to 0
        $limit = \core\Context::getInstance()->getPageSize();
        
        $numRows = $cursor->numRows();
        $cursor->moveTo($pageNo * $limit);
        
        $objs = array();
        for($x=0; $x < $limit && $cursor->hasNext(); $x++) {
            $obj = $cursor->next();
            
            $obj = $this->renderRow($obj);
            
            $row = array();
            foreach($this->indexTableColumns as $itc) {
                $fieldname = $itc['fieldname'];
                $row[$fieldname] = $obj->{$fieldname};
            }
            $objs[] = $row;
        }
        
        $lr = new ListResponse($pageNo*$limit, $limit, $numRows, $objs);
        
        return $lr;
    }
    
    public function renderRow($row) {
        foreach($this->htmlColumns as $hc) {
            ob_start();
            eval('?>'. $hc['column_html'] );
            $html = ob_get_clean();
            
            $row->{$hc['column_name']} = $html;
        }
        
        
        return $row;
    }
    
    protected function isHtmlColumn($colname) {
        if (is_array($this->htmlColumns)) foreach($this->htmlColumns as $hc) {
            if ($hc['column_name'] == $colname)
                return true;
        }
        
        return false;
    }
    
    public function jsIndexTable() {
        $js = '';
        
        $module_name = $this->getModuleName();
        $controller_path = $this->getControllerPath();
        
        $js .= 'var t = new IndexTable(\'#object-container\');' . PHP_EOL;
        $js .= PHP_EOL;
        $js .= PHP_EOL;
        $js .= 't.setConnectorUrl( ' . json_encode("/?m={$module_name}&c={$controller_path}&a=search") . ' );' . PHP_EOL;
        $js .= PHP_EOL;
        $js .= PHP_EOL;
        
        foreach($this->indexTableColumns as $itc) {
            $arr = array();
            $arr['fieldName'] = $itc['fieldname'];
            $arr['fieldDescription'] = $itc['label'];
            
            
            if ($this->isHtmlColumn($itc['fieldname'])) {
                $arr['fieldType'] = 'html';
            }
            
            $js .= 't.addColumn(' . json_encode($arr) . ');' . PHP_EOL;
            $js .= PHP_EOL;
        }
        
//         t.setRowClick(function(row, evt) {
//             window.location = appUrl('/?m=base&c=company&a=edit&company_id=' + $(row).data('record').company_id);
//         });
            
        return $js;
    }
    
}

