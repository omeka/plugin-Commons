<?php

class Commons_ItemsExportJob extends Omeka_Job_AbstractJob
{

    public $webRoot; //WEB_ROOT constant doesn't work in a process
    public $collectionId;
    public function perform()
    {
        $flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        $db = get_db();
        $iTable = $db->getTable('Item');
        $select = $iTable->getSelect();
        if($collectionId = $this->getCollectionId()) {
            $select->where('collection_id = ?', $collectionId);
        }
        $select->where('public = ?', 1);
        $items = $iTable->fetchObjects($select);
        $commonsRecordTable = $db->getTable('CommonsRecord');
        $successCount = 0;
        $count = count($items);
        $errorItems = array();
        foreach($items as $item) {
            //see if item has a CommonsRecord
            $itemRecord = $commonsRecordTable->findByTypeAndId('Item', $item->id);
            if(!$itemRecord) {
                $itemRecord = new CommonsRecord();
                $itemRecord->initFromRecord($item);
            }
            $itemRecord->export(array('webRoot' => $this->webRoot));
            $itemRecord->save();
            if($itemRecord->status == 'ok') {
                $successCount ++;
            } else {
                $errorItems[] = metadata($itemRecord->Record, array('Dublin Core', 'Title'));
            }
            
            release_object($item);
            release_object($itemRecord);
            sleep(1);
        }
        $flashMessenger->addMessage(__('Successfully updated %s of %s items', $successCount, $count));
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