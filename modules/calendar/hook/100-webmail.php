<?php




use calendar\helper\SabreVEventParser;
use customer\service\CustomerService;
use webmail\search\MailSearchBase;

hook_eventbus_subscribe('webmail', 'include-component-index', function($controller) {
    // load's customer-table javascript
    if (ctx()->isModuleEnabled('customer'))
        new \customer\forms\CustomerTableSelectWidget();
});


hook_eventbus_subscribe('webmail', 'mailbox-mailactions', function($actionContainer) {
    
    $emailId = $actionContainer->getAttribute('data-email-id');
    
    if (!$emailId) {
        return;
    }
    
    $ms = MailSearchBase::getInstance();
    
    
    $email = $ms->readById( $emailId );
    ini_set('error_reporting', E_ALL);
    
    // mail not found?
    if (!$email)
        return;
    
    $attachmentCount = 0;
    
    $emlViewer = $email->getEmlViewer();
    $pas = $emlViewer->getParserAttachments();
    
    
    if (count($pas))
        $attachmentCount = count( $pas );
    for($x=0; $x < $attachmentCount; $x++) {
        
        $att = $pas[$x];
        
        if ($att->getContentType() == 'text/calendar') {
            $svp = new SabreVEventParser( $att->getContent() );
            
            if ($svp->getEventCount()) {
                $dtstart = $svp->getEventProperty(0, 'DTSTART');
                $dtend   = $svp->getEventProperty(0, 'DTEND');
                $summary = $svp->getEventProperty(0, 'SUMMARY');
                
                $attrStart = '';
                if ($dtstart) {
                    $dt = new DateTime( $dtstart );
                    // set timezone to current timezone AFTER parsing
                    $dt->setTimezone( new DateTimeZone(date_default_timezone_get()) );
                    $attrStart = $dt->format('Y-m-d H:i:s');
                }
                
                $attrEnd = '';
                if ($dtend) {
                    $dt = new DateTime( $dtend );
                    // set timezone to current timezone AFTER parsing
                    $dt->setTimezone( new DateTimeZone(date_default_timezone_get()) );
                    $attrEnd = $dt->format('Y-m-d H:i:s');
                }
                
                hook_htmlscriptloader_enableGroup('calendar');
                
                // load's customer-table javascript
                if (ctx()->isModuleEnabled('customer'))
                    new \customer\forms\CustomerTableSelectWidget();
                
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

