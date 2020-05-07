<?php




use calendar\helper\SabreVEventParser;

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
                
                $html = '<input type="button" value="Calendar" ';
                $html .= ' data-start="'.esc_attr($attrStart).'" ';
                $html .= ' data-end="'.esc_attr($attrEnd).'" ';
                $html .= ' data-title="'.esc_attr($summary).'" ';
                $html .= 'onclick="'.esc_attr('hook_addCalendarItem_Click(this);').'" />';
                
                $actionContainer->addItem('filesync-import-file', $html, 100);
                
                break;
            }
        }
    }
    
});

