<?php

use core\controller\BaseController;
use core\db\DatabaseHandler;
use core\db\connection\MysqlConnection;

class modelController extends BaseController {

	public function action_index() {
	
	    $this->tbl = get_var('tbl');
	    
	    if (is_post()) {
    	    $con = DatabaseHandler::getConnection('default');
    	    
    	    if (is_a($con, MysqlConnection::class)) {
    	        
    	        $arr = $con->queryListAsArray('describe '.$this->tbl);
    	        
    	        $slug = $this->tbl;
    	        if (strpos($slug, '.') !== false) {
    	            $slug = substr($slug, strrpos($slug, '.')+1);
    	        }
    	        $slug = str_replace('-', '_', slugify($slug));
    	        
    	        $tblname = $this->tbl;
    	        if (strpos($tblname, '.') !== false) {
    	            $tblname = substr($tblname, strpos($tblname, '.')+1);
    	        }
    	        
    	        $schema = substr($tblname, 0, strpos($tblname, '__'));
    	        $tbl    = substr($tblname, strpos($tblname, '__')+2);
    	        
    	        
    	        $var = '$tb_'.$slug;
    	        $txt = $var . ' = new TableModel(\'' . $schema . '\', \'' . $tbl . '\');' . PHP_EOL;
    	        
    	        $lenLongestName = null;
    	        foreach($arr as $f) {
    	            if ($lenLongestName == null || strlen($f['Field']) > $lenLongestName)
    	                $lenLongestName = strlen($f['Field']);
    	        }
    	        
    	        foreach($arr as $f) {
    	           $txt .= $var .'->addColumn(';
    	           
    	           // field name
    	           $txt .= '\'' . $f['Field'] . '\', ';
    	           
    	           // alignment :)
    	           $noSpaces = $lenLongestName - strlen($f['Field']);
    	           if ($noSpaces)
    	               $txt .= str_repeat(' ', $noSpaces);
    	           
    	           // type
    	           $type = $f['Type'];
    	           if (strpos($type, 'int(') === 0) $type = 'int';
    	           if (strpos($type, 'bigint(') === 0) $type = 'bigint';
    	           if (strpos($type, 'tinyint') === 0) $type = 'boolean';
    	           $txt .= '\'' . $type . '\'';
    	           
    	           if ($f['Key'] == 'PRI') {
    	               $txt .= ', [\'key\' => \'PRIMARY KEY\', \'auto_increment\' => true]';
    	           }
    	           
    	           $txt .= ');' . PHP_EOL;
    	        }
    	        
    	        $txt .= PHP_EOL;
    	        $txt .= "\$tbs[] = " . $var . ';' . PHP_EOL;

    	        $this->model_code = $txt;
    	        
    	        
    	        $x = $con->queryOne('show create table '.$this->tbl);
    	        $this->create_table = $x['Create Table'];
    	    }
    	    
	    }
	    
	    

		$this->render();

	}


}

