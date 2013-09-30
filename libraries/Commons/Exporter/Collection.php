<?php

class Commons_Exporter_Collection extends Commons_Exporter
{
    protected $typeKey = 'collections';

    public function buildRecordData()
    {
        $collectionArray = array(
            'orig_id' => $this->record->id,
            'title' => metadata($this->record, array('Dublin Core', 'Title')),
            'description' => metadata($this->record, array('Dublin Core', 'Description')),
            'url' => $this->buildRealUrl(WEB_ROOT . '/collections/show/' . $this->record->id),
        );
        return $collectionArray;
    }

    public function exportItems()
    {
        require_once COMMONS_PLUGIN_DIR . '/libraries/Commons/ItemsExportJob.php';
        Zend_Registry::get('bootstrap')->getResource('jobs')->send('Commons_ItemsExportJob', 
                array('collectionId' => $this->record->id));  
    }
}
