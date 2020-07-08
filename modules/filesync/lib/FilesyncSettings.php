<?php


namespace filesync;


class FilesyncSettings {
    
    
    public function getLibreOfficePreviews() {
        return ctx()->getSetting('filesync__libreoffice_previews', true);
    }
    
    public function getPagequeueDefaultRotation() {
        return ctx()->getSetting('filesync__pagequeue_default_rotation', 0);
    }
    
    public function getPagequeueArchiveStore() {
        return ctx()->getSetting('filesync__pagequeue_archive_store', 0);
    }
}

