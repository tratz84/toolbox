<?php

namespace codegen\form\install;

use core\forms\HiddenField;
use core\db\mysql\MysqlTableGenerator;

class ConfigCreatorForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
		$this->addWidget(new HiddenField('hmz'));
		
		$this->addValidator('hmz', function($form) {
		    if (is_writable(ROOT.'/config/') == false) {
		        return 'Config dir not writable';
		    }
		});
		
	    $this->addValidator('data_dir', function($form) {
	        $d = trim($form->getWidgetValue('data_dir'));
	        
	        if (!$d) {
	            return 'required';
	        }
	        
	        // data-dir already exists?
	        if (is_dir($d)) {
	            if (is_writable($d) == false) {
	                return 'data-dir not writable';
	            }
	            // writeable? => everything fine..
	            return;
	        }
	        
	        // new data-dir? => check if parent is writable
	        $parent = dirname($d);
	        if (is_writable($parent) == false) {
	            return 'unable to create data-dir (create data-dir manual or make parent writabel)';
	        }
	        
	    });
		
        $this->addValidator('db_host', function($form) {
            $r = $form->asArray();
            $dbh = @mysqli_connect($r['db_host'], $r['db_user'], $r['db_password'], $r['db_name']);
            
            if (!$dbh) {
                return 'Unable to connect to database (username/password right? database exists?)';
            }
            
            mysqli_close($dbh);
        });
	}
	
	
	
	public function doInstall() {
	    
	    $arr = $this->asArray();
	    
	    // create tables
	    $dbh = @mysqli_connect($arr['db_host'], $arr['db_user'], $arr['db_password'], $arr['db_name']);
	    if (!$dbh) {
	        // note, shouldn't happen, already checked in validation..
	        die('Unable to connect to database');
	    }
	    
	    // core stuff
	    $sql = '';
	    $core_tms = load_php_file( module_file('core', 'core/tablemodel.php'));
	    foreach($core_tms as $tm) {
    	    $mtg = new MysqlTableGenerator($tm);
    	    $sql .= $mtg->buildCreateTable() . "\n";
	    }
	    
	    $r = mysqli_multi_query($dbh, $sql);
	    if (!$r) {
	        die('Core-queries failed to execute: '.mysqli_error($dbh));
	    }
	    $this->fetch_mysqli_results($dbh);
	    
	    // base stuff
	    $sql = '';
	    $core_tms = load_php_file( module_file('core', 'base/tablemodel.php'));
	    foreach($core_tms as $tm) {
	        $mtg = new MysqlTableGenerator($tm);
	        $sql .= $mtg->buildCreateTable() . "\n";
	    }
	    
	    $r = mysqli_multi_query($dbh, $sql);
	    if (!$r) {
	        die('Base-queries failed to execute: '.mysqli_error($dbh));
	    }
	    $this->fetch_mysqli_results($dbh);
	    
	    
	    // NOTE: if this password is changed, also update modules/base/controller/authController.php
	    //       this controller contains a check if default password is set & gives a warning!!!
	    $sql_user = "insert into base__user set username='admin', password='admin123', edited=now(), created=now(), user_type='admin'";
	    mysqli_query($dbh, $sql_user);
	    
	    
	    // create data-dir
	    $data_dir = trim($this->getWidgetValue('data_dir'));
	    if (file_exists($data_dir) == false) {
	        mkdir($data_dir, 0755, true);
	    }
	    
	    // write config
	    $tplfile = module_file('codegen', '/templates/_classes/codegen-config-local.php');
	    $config_txt = get_template($tplfile, $arr);
	    file_put_contents(ROOT.'/config/config-local.php', $config_txt);
	    
	    
	    report_user_message('A user is generated with username: admin, password: admin123');
	    
	    header('Location: ' . $this->getWidgetValue('base_href'));
	    exit;
	}
	
	protected function fetch_mysqli_results($link) {
	    do {
	        $r = mysqli_use_result($link);
	        mysqli_more_results($link);
	    } while (mysqli_next_result($link));
	}
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\TextField('db_host', NULL, 'Database host');
		$this->addWidget( $w1 );
		$w2 = new \core\forms\TextField('db_user', NULL, 'Database username');
		$this->addWidget( $w2 );
		$w3 = new \core\forms\TextField('db_password', NULL, 'Database password');
		$this->addWidget( $w3 );
		$w4 = new \core\forms\TextField('db_name', NULL, 'Database name');
		$this->addWidget( $w4 );
		$w5 = new \core\forms\TextField('base_href', NULL, 'Base href');
		$this->addWidget( $w5 );
		$w5->setInfoText( 'Base href where web-app resides' );
		$w6 = new \core\forms\TextField('api_key', NULL, 'API Key');
		$this->addWidget( $w6 );
		$w6->setInfoText( 'SECRET key used for exports' );
		$w7 = new \core\forms\TextField('data_dir', NULL, 'Data dir');
		$this->addWidget( $w7 );
		$w7->setInfoText( 'directory that contains uploaded data' );
		
	}





}

