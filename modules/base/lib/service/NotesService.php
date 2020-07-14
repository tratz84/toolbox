<?php

namespace base\service;


use base\form\NoteForm;
use base\forms\FormChangesHtml;
use base\model\Note;
use base\model\NoteDAO;
use base\util\ActivityUtil;
use core\service\ServiceBase;
use core\exception\ObjectNotFoundException;
use core\exception\InvalidArgumentException;

class NotesService extends ServiceBase {
    
    
    
    public function readNotes($ref_object, $ref_id) {
        $nDao = new NoteDAO();
        
        return $nDao->readByRef($ref_object, $ref_id);
    }
    
    public function readNotesByCustomer( $company_id, $person_id ) {
        $nDao = new NoteDAO();
        
        if ($company_id) {
            return $nDao->readByCompany($company_id);
        }
        else if ($person_id) {
            return $nDao->readByPerson($person_id);
        }
        else {
            throw new InvalidArgumentException('No company/person set');
        }
    }

    
    public function readNotesByRef( $refObject, $refId ) {
        $nDao = new NoteDAO();
        
        return $nDao->readByRef($refObject, $refId);
    }
    
    
    public function readNote($note_id) {
        $nDao = new NoteDAO();
        
        return $nDao->read($note_id);
    }
    
    
    public function saveNote($form) {
        $noteId = $form->getWidgetValue('note_id');
        
        if ($noteId) {
            $note = $this->oc->get(NotesService::class)->readNote($noteId);
        } else {
            $note = new Note();
        }
        
        $isNew = $note->isNew();
        
        if ($isNew) {
            $fch = FormChangesHtml::formNew($form);
        } else {
            $oldForm = NoteForm::createAndBind($note);
            $fch = FormChangesHtml::formChanged($oldForm, $form);
        }
        
        $form->fill($note, array('note_id', 'ref_object', 'ref_id', 'company_id', 'person_id', 'important', 'shortNote', 'longNote'));
        
        if (!$note->getSort()) {
            $nDao = object_container_get( NoteDAO::class );
            
            if ($note->getRefObject()) {
                $sort = $nDao->maxSortByRef( $note->getRefObject(), $note->getRefId() ) + 1;
                $note->setSort( $sort );
            }
            else {
                $sort = $nDao->maxSortByCustomer( $note->getCompanyId(), $note->getPersonId() ) + 1;
                $note->setSort( $sort );
            }
        }
        
        if (!$note->save()) {
            // exception would also be on it's place
            return false;
        }
        
        $form->getWidget('note_id')->setValue($note->getNoteId());
        
        if ($isNew) {
            ActivityUtil::logActivity($note->getCompanyId(), $note->getPersonId(), $note->getRefObject(), $note->getRefId(), 'note-created', 'Notitie aangemaakt: '.$note->getSummary(), $fch->getHtml());
        } else {
            // TODO: check of er wijzigingen zijn
            ActivityUtil::logActivity($note->getCompanyId(), $note->getPersonId(), $note->getRefObject(), $note->getRefId(), 'note-edited', 'Notitie gewijzigd: '.$note->getSummary(), $fch->getHtml());
        }
        
        return $note->getNoteId();
    }
    
    
    public function saveNotesOrder( $noteIds ) {
        $ids = array();
        
        foreach($noteIds as $nid) {
            if ((int)$nid) {
                $ids[] = (int)$nid;
            }
        }
        
        $nDao = object_container_get( NoteDAO::class );
        $nDao->updateSort( $ids );
    }
    
    
    
    public function deleteNote($noteId) {
        $note = $this->oc->get(NotesService::class)->readNote($noteId);
        
        if (!$note) {
            throw new ObjectNotFoundException('Note not found');
        }
        
        $nDao = new NoteDAO();
        $nDao->delete( $noteId );

        ActivityUtil::logActivity($note->getCompanyId(), $note->getPersonId(), $note->getRefObject(), $note->getRefId(), 'note-deleted', 'Notitie verwijderd: '.$note->getSummary());
    }
    
    
}



