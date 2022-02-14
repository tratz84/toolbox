<?php



use core\controller\BaseController;
use webmail\form\WebmailUpdateActionForm;
use webmail\mail\MailProperties;
use webmail\mail\SolrMailActions;
use webmail\solr\SolrMailQuery;
use webmail\search\MailSearchBase;
use webmail\mail\render\MailRenderBase;
use webmail\mail\action\MailActionsBase;
use core\forms\lists\ListResponse;

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
            
            $mab = MailActionsBase::getInstance();
            
            $ms = MailSearchBase::getInstance();
            $ms->addAction( $old_action );
            
            $start = 0;
            /** @var ListResponse $lr */
            $lr = null;
            
            // delete all documents in response
            do {
                $ms->setStart( $start );
                $ms->setRows( 100 );
                
                $lr = $ms->searchListResponse();
                
                $count = 0;
                
                /** @var $mail MailRenderBase */
                foreach($lr->getObjects() as $mail) {
                    // update Action
                    $mab->updateAction($mail['email_id'], $new_action);
                    
                    $count++;
                }
                
                // next start
                $start += $ms->getRows();
            } while ( $lr->hasMore() );
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

