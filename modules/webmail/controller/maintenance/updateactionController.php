<?php



use core\controller\BaseController;
use webmail\form\WebmailUpdateActionForm;
use webmail\mail\MailProperties;
use webmail\mail\SolrMailActions;
use webmail\solr\SolrMailQuery;

class updateactionController extends BaseController {
    
    
    public function action_index() {
        
        
        $this->form = new WebmailUpdateActionForm();
        
        
        return $this->render();
    }
    
    
    public function action_update() {
        
        $old_action = get_var('old_action');
        $new_action = get_var('new_action');
        
        
        if ($old_action == $new_action) {
            return $this->json([
                'success' => false,
                'message' => t('Old state same as new')
            ]);
        }
        
        
        // TODO: validate old_action/new_action value? probably don't care 
        //       about old_value, because solr-query don't find anything. Maybe
        //       validate new_action?
        
        // close session so user can continue
        session_write_close();
        
        set_time_limit(0);
        
        
        $updateCount = 0;
        
        try {
            $smq = new SolrMailQuery();
            $smq->addFacetSearch('action', ':', $old_action);
            
            $sma = new SolrMailActions();
            
            // delete all documents in response
            do {
                $r = $smq->search();
                $smq->setStart(0);
                
                // delete documents
                $docs = $r->getDocuments();
                foreach($docs as $doc) {
                    // update .tbproperties-file
                    $mp = new MailProperties( $doc->id );
                    $mp->setAction( $new_action );
                    $mp->save();
                    
                    // update solr
                    $sma->updateAction($doc->id, $new_action);
                    
                    $updateCount++;
                }
                
                // loop till 0 results
            } while ($r->getNumFound() > 0);
        } catch (\Exception|\Error $ex) {
            return $this->json([
                'success' => false,
                'message' => $ex->getMessage()
            ]);
        }
        
        
        return $this->json([
            'success' => true,
            'message' => t('Actions updated, number: #').$updateCount
        ]);
    }
    
}

