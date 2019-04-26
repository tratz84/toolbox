<?php


namespace signrequest\model;


class MessageFile extends base\MessageFileBase {

    
    public function getId() { return $this->getMessageFileId(); }
    public function getName() { return $this->getFilename(); }

}

