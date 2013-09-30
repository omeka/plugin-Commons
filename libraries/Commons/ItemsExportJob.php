<?php

class Commons_ItemsExportJob extends Omeka_Job_AbstractJob
{

    public $webRoot; //WEB_ROOT constant doesn't work in a process
    public $collectionId;
    public function perform()
    {
        $db = get_db();
        $iTable = $db->getTable('Item');
        $select = $iTable->getSelect();
        if($collectionId = $this->getCollectionId()) {
            $select->where('collection_id = ?', $collectionId);
        }
        $select->where('public = ?', 1);
        $items = $iTable->fetchObjects($select);
        $commonsRecordTable = $db->getTable('CommonsRecord');
        foreach($items as $item) {
            //see if item has a CommonsRecord
            $itemRecord = $commonsRecordTable->findByTypeAndId('Item', $item->id);
            if(!$itemRecord) {
                $itemRecord = new CommonsRecord();
                $itemRecord->initFromRecord($item);
            }
            $itemRecord->export(array('webRoot' => $this->webRoot));
            $itemRecord->save();
            release_object($item);
            release_object($itemRecord);
            sleep(1);
        }
    }
    
    public function setCollectionId($id)
    {
        $this->collectionId = $id;
    }
    
    public function getCollectionId()
    {
        return $this->collectionId;
    }
}