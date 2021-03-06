<?php



use base\form\NoteForm;
use base\model\Note;
use base\service\NotesService;
use core\controller\BaseController;
use core\forms\lists\ListResponse;
use core\exception\InvalidStateException;

class notestabController extends BaseController {
    
    
    public function action_index() {
        $notesService = object_container_get(NotesService::class);
        
        $this->companyId = isset($this->company_id) ? $this->company_id : null;
        $this->personId = isset($this->person_id) ? $this->person_id : null;
        
        $this->ref_object = isset($this->ref_object) ? $this->ref_object : null;
        $this->ref_id     = isset($this->ref_id)     ? $this->ref_id : null;
        
        if (isset($this->save_by_ref) == false) $this->save_by_ref = false;
        
        if ($this->save_by_ref) {
            $notes = $notesService->readNotesByRef($this->ref_object, $this->ref_id);
        } else {
            $notes = $notesService->readNotesByCustomer( $this->companyId, $this->personId );
        }
        
        $this->listResponse = ListResponse::fillByDBObjects(0, null, $notes, ['note_id', 'summary', 'important', 'edited', 'created']);
        
        $this->setShowDecorator(false);
        
        return $this->render();
    }
    
    public function action_search() {
        $notesService = object_container_get(NotesService::class);
        
        if (get_var('save_by_ref')) {
            $notes = $notesService->readNotesByRef(get_var('ref_object'), get_var('ref_id'));
        } else {
            $notes = $notesService->readNotesByCustomer( get_var('companyId'), get_var('personId') );
        }
        
        $lr = ListResponse::fillByDBObjects(0, null, $notes, ['note_id', 'summary', 'important', 'edited', 'created']);
        
        return $this->json([
            'success' => true,
            'listResponse' => $lr
        ]);
    }
    
    
    public function action_edit_note() {
        $notesService = object_container_get(NotesService::class);
        
        if (get_var('note_id')) {
            $this->note = $notesService->readNote( get_var('note_id') );
        } else {
            $this->note = new Note();
            
            $this->note->setCompanyId(get_var('company_id'));
            $this->note->setPersonId(get_var('person_id'));
            $this->note->setRefObject(get_var('ref_object'));
            $this->note->setRefId(get_var('ref_id'));
        }
        
        
        $this->form = new NoteForm();
        $this->form->bind( $this->note );
        
        $this->form->getWidget('save_by_ref')->setValue( get_var('save_by_ref') ? 1 : 0);
        
        $this->isNew = $this->note->isNew();
        $this->setShowDecorator(false);
        
        return $this->render();
    }
    
    public function action_save_note() {
        $notesService = object_container_get(NotesService::class);
        
        $this->form = new NoteForm();
        
        $this->form->bind( $_REQUEST );
        if ($this->form->validate()) {
            $notesService->saveNote( $this->form );
            
            return $this->json([
                'success' => true
            ]);
        }
        
        
        $errors = $this->form->getErrors();
        
        return $this->json([
            'success' => false,
            'errors' => $errors
        ]);
    }
    
    
    public function action_save_sort() {
        $notesService = object_container_get(NotesService::class);
        
        $ids = explode(',', get_var('ids'));
        $ids = array_reverse( $ids );
        
        $notesService->saveNotesOrder( $ids );
        
        $this->json([
            'success' => true
        ]);
    }
    
    
    
    public function action_delete() {
        $notesService = object_container_get(NotesService::class);
        
        $notesService->deleteNote( get_var('note_id') );
        
        return $this->json([
            'success' => true
        ]);
    }
    
    
    
}
