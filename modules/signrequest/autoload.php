<?php



use core\ObjectContainer;
use core\event\CallbackPeopleEventListener;
use signrequest\service\SignRequestService;
use core\Context;

Context::getInstance()->enableModule('signrequest');

$oc = ObjectContainer::getInstance();

$eb = $oc->get(\core\event\EventBus::class);

module_update_handler('signrequest', '20200419');


$eb->subscribe('masterdata', 'menu', new CallbackPeopleEventListener(function($evt) {
    $src = $evt->getSource();
    $src->addItem('SignRequest', 'Instellingen',     '/?m=signrequest&c=setting');
}));

$eb->subscribe('invoice', 'offer-edit', new CallbackPeopleEventListener(function($evt) {
    /**
     * @var \core\container\ActionContainer $actionContainer
     */
    $actionContainer = $evt->getSource();
    
    // hmmz, shouldn't happen
    if ($actionContainer->getObjectType() != 'offer')
        return;
    // might happen on new offer
    if (!$actionContainer->getObjectId())
        return;
    
    
    $oc = ObjectContainer::getInstance();
    $srs = $oc->get(SignRequestService::class);
    $cnt = $srs->getSentCountByRef('offer', $actionContainer->getObjectId());
   
    $onclick = '';
    if ($cnt > 0) {
        $onclick = ' onclick="'.esc_attr('showConfirmation(\'SignRequest versturen\', \'Weet je zeker dat je nog een SignRequest wilt sturen?<br>Deze is reeds '.$cnt.'x verstuurd.\', function() { window.location = \''.appUrl('/?m=signrequest&c=offer&a=create&offer_id='.$actionContainer->getObjectId()).'\' } )').'; return false;" ';
    }
    
    $actionContainer->addItem('signrequest', '<a '.$onclick.' href="'.appUrl('/?m=signrequest&c=offer&a=create&offer_id='.$actionContainer->getObjectId()).'">Verstuur SignRequest</a>');
}));
    