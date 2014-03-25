<?php
//TODO upload von anderen dateien bsp: excel whateva
abstract class Application_Model_Forms_UploadPictureForm extends Zend_Form {

    public function init() {
        $this->setMethod('post');
        //TODO pfade in ner configdatei und andere sachen
        $file_picture = new Zend_Form_Element_File('image_form');
        $file_picture->setLabel('Ein Bild hochladen:')->setDestination(APPLICATION_PATH . '/upload');
        $file_picture->setRequired($required);

        // Nur 1 Datei sicherstellen
        $file_picture->addValidator('Count', false, 1);
        // Maximal 100k
        $file_picture->addValidator('Size', false, 2102400);
        // Nur JPEG, PNG, und GIFs
        $file_picture->addValidator('Extension', false, 'jpg,png,jpeg');

        return $file_picture;
    }

}
