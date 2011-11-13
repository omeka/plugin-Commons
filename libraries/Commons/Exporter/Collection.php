<?php

class Commons_Exporter_Collection extends Commons_Exporter
{
    protected $typeKey = 'collections';
    
    public function buildRecordData()
    {
        $collectionArray = array(
            'orig_id' => $this->_record->id,
            'title' => $this->_record->name,
            'description' => $this->_record->description,
            'url' => record_uri($this->_record, 'show')
        );
        return $collectionArray;
    }
    
    

}