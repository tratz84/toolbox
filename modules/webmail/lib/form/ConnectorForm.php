<?php


namespace webmail\form;


use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\HiddenField;
use core\forms\InternalField;
use core\forms\NumberField;
use core\forms\SelectField;
use core\forms\TextField;
use core\forms\WidgetContainer;
use core\forms\validator\NotEmptyValidator;
use webmail\model\Connector;
use core\forms\TabWidgetContainer;

class ConnectorForm extends BaseForm {
    
    protected $connector = null;
    
    public function __construct(Connector $connector) {
        parent::__construct();
        
        $this->connector = $connector;
        
        $this->addKeyField('connector_id');
        
        $this->addWidget(new HiddenField('connector_id'));
        $this->addWidget(new InternalField('imapfolders'));
        $this->addWidget(new InternalField('selectedImapfolders'));
        
        $twc = new TabWidgetContainer( 'connector-tabs' );
        $twc->addTab( 'base', t('Base settings') );
        $this->addWidget( $twc );
        
        $twc->addTabWidget( 'base', new CheckboxField('active', '', t('Active')));
        
        $twc->addTabWidget( 'base', new TextField('description', '', t('Description')));
        
        $twc->addTabWidget( 'base', new SelectField('connector_type', '', array(
            'imap'  => 'imap (native)')));
//             'horde' => 'imap (horde)',
//             'pop3'  => 'pop3'), 'Soort'));
        
        
        $twc->addTabWidget( 'base', new TextField('hostname', '', 'Hostname'));
        
        $twc->addTabWidget( 'base', new NumberField('port', '', 'Port'));
        
        $twc->addTabWidget( 'base', new TextField('username', '', 'Username'));
        $twc->addTabWidget( 'base', new TextField('password', '', 'Password'));
        
        $mapFolders = array();
        $mapFolders[] = 'Maak uw keuze';
        foreach($connector->getImapfolders() as $if) {
            $mapFolders[$if->getConnectorImapfolderId()] = $if->getFolderName();
        }
        $twc->addTabWidget( 'base', new SelectField('sent_connector_imapfolder_id', '', $mapFolders, 'Sent'));
        $twc->addTabWidget( 'base', new SelectField('junk_connector_imapfolder_id', '', $mapFolders, 'Junk'));
        $twc->addTabWidget( 'base', new SelectField('trash_connector_imapfolder_id', '', $mapFolders, 'Trash'));
        
        $twc->addTab('advanced', t('Advanced settings'), 20);
        $twc->addTabWidget( 'advanced', new SelectField('reply_move_imapfolder_id',      '', $mapFolders, 'Move on reply'));
        
        $this->addImapFolders();
        
        $this->addValidator('description', new NotEmptyValidator());
        $this->addValidator('hostname', new NotEmptyValidator());
        $this->addValidator('username', new NotEmptyValidator());
        $this->addValidator('port', function($form) {
            $p = (int)$form->getWidgetValue('port');
            
            if ($p < 1 || $p > 65535) {
                return 'Ongeldige poort';
            }
            
            return null;
        });
    }
    
    public function bind($obj) {
        
        if (is_array($obj)) {
            $this->getWidget('imapfolders')->setValue('');
        }
        
        parent::bind( $obj );
        
        if (is_a($obj, Connector::class)) {
            foreach( $obj->getImapfolders() as $if ) {
                if ($if->getActive() == false) continue;
                
                $w = $this->getWidget('selectedImapfolder-'.slugify($if->getFoldername()));
                
                if (!$w) continue;
                
                $w->setValue( 1 );
            }
        }
        
    }
    
    
    public function addImapFolders() {
        $t = $this->getWidget( 'connector-tabs' );
        
        $wc = new WidgetContainer();
        $wc->setName('imap-folders');
        $t->addWidget($wc);
        
        foreach($this->connector->getImapfolders() as $if) {
            $wc->addWidget(new HiddenField('imapfolder-'.slugify($if->getFoldername()), $if->getFoldername(), $if->getFoldername()));
            $wc->addWidget(new CheckboxField('selectedImapfolder-'.slugify($if->getFoldername()), '', $if->getFoldername()));
        }
    }
    
}

