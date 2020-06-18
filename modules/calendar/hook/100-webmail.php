<?php




use calendar\helper\SabreVEventParser;
use customer\service\CustomerService;

hook_eventbus_subscribe('webmail', 'mailbox-mailactions', function($actionContainer) {
    
    $emailId = $actionContainer->getAttribute('data-email-id');
    
    if (!$emailId) {
        return;
    }
    
    $email = \webmail\solr\SolrMailQuery::readStaticById( $emailId );
    ini_set('error_reporting', E_ALL);
    
    $attachmentCount = count($email->getAttachments());
    for($x=0; $x < $attachmentCount; $x++) {
        
        $att = $email->getAttachmentFile( $x );
        if ($att['contentType'] == 'text/calendar') {
            $svp = new SabreVEventParser( $att['content'] );
            
            if ($svp->getEventCount()) {
                $dtstart = $svp->getEventProperty(0, 'DTSTART');
                $dtend   = $svp->getEventProperty(0, 'DTEND');
                $summary = $svp->getEventProperty(0, 'SUMMARY');
                
                $attrStart = '';
                if ($dtstart) {
                    $dt = new DateTime($dtstart, new DateTimeZone(date_default_timezone_get()));
                    $attrStart = $dt->format('Y-m-d H:i:s');
                }
                
                $attrEnd = '';
                if ($dtend) {
                    $dt = new DateTime($dtend, new DateTimeZone(date_default_timezone_get()));
                    $attrEnd = $dt->format('Y-m-d H:i:s');
                }
                
                hook_htmlscriptloader_enableGroup('calendar');
                
                $company_id = '';
                $person_id = '';
                
                // lookup customer by email
                $email = trim($email->getFromEmail());
                if (validate_email($email)) {
                    $customerService = object_container_get(CustomerService::class);
                    $customer = $customerService->readCustomerByEmail( $email );
                    
                    if ($customer) {
                        if ($customer->getCompany()) {
                            $company_id = $customer->getCompany()->getCompanyId();
                        }
                        if ($customer->getPerson()) {
                            $person_id = $customer->getPerson()->getPersonId();
                        }
                    }
                }
                
                
                $html = '<button value="'.t('Calendar item').'" ';
                $html .= ' title="'.t('Create calendar item').'" ';
                $html .= ' data-start="'.esc_attr($attrStart).'" ';
                $html .= ' data-end="'.esc_attr($attrEnd).'" ';
                $html .= ' data-title="'.esc_attr($summary).'" ';
                $html .= ' data-company-id="'.esc_attr($company_id).'" ';
                $html .= ' data-person-id="'.esc_attr($person_id).'" ';
                $html .= 'onclick="'.esc_attr('hook_addCalendarItem_Click(this);').'"><span class="fa fa-calendar webmail-import-calendar-item"></span></button>';
                
                $actionContainer->addItem('filesync-import-file', $html, 100);
                
                break;
            }
        }
    }
    
});

