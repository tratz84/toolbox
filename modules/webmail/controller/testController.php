<?php



use core\controller\BaseController;
use webmail\mail\ImapConnection;
use webmail\mail\MailSearch;
use webmail\mail\SolrUpdate;
use webmail\service\ConnectorService;

class testController extends BaseController {
    
    public function __construct() {
        die('inactive');
    }
    
    public function action_index() {
        
        set_time_limit(600);
        
        $cs = $this->oc->get(ConnectorService::class);
        
        $c = $cs->readConnector(1);
        
        $ic = ImapConnection::createByConnector($c);
        
        if (!$ic->connect())
            die('unable to connect');
        
        
        session_write_close();
        $ic->doImport( $c );
        
        
        $ic->disconnect();
        
        exit;
    }
    
    
    public function action_updatesolr() {
        
        $su = new SolrUpdate();
        $su->truncate();
//         $su->commit();exit;
        $su->importFolder($this->ctx->getDataDir().'/webmail/inbox');
        
//         $su->queueFile('C:/projects/insights/data/demo/webmail/inbox/2017/09/29/f29ed66633e944897e194bb2d5e50510');
//         $su->purge(true);
        $su->commit();
        
        print 'Done';
    }
    
    public function action_test() {
        
//         var_export(function_exists('mailparse_msg_parse_file'));
        
        $su = new SolrUpdate();
        $r = $su->parseEml('/home/timvw/projects/insights/data/dev/webmail/inbox/2017/09/29/f29ed66633e944897e194bb2d5e50510');
        
        var_export( $r );
        exit;
        
    }
    
    
    public function action_search() {
        $ms = new MailSearch();
        
//         $ms->setQuery('sqr');
        $lr = $ms->search(1);
        
        var_export($lr);
        
    }
    
}




