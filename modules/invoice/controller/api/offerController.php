<?php



use core\controller\BaseController;
use invoice\service\OfferService;

class offerController extends BaseController {
    
    
    public function action_lastChanged() {
        $offerService = $this->oc->get(OfferService::class);
        
        $opts = array();
        $opts['order'] = 'edited desc';
        $offers = $offerService->searchOffer(0, 10, $opts);
        
        print json_encode($offers);
    }
    
    
}