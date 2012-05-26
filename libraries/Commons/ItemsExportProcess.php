<?php

class Commons_ItemsExportProcess extends ProcessAbstract
{

    public $webRoot; //WEB_ROOT constant doesn't work in a process

    public function run($args)
    {
        $db = get_db();
        $iTable = $db->getTable('Item');
        $select = $iTable->getSelect();
        define('COMMONS_WEB_ROOT', $args['webRoot']);
        $this->webRoot = $args['webRoot'];
        if(isset($args['collectionId'])) {
            $collectionId = $args['collectionId'];
            //$params = array('collection'=>$collectionId);
            //$items = get_items($params);
            $select->where('collection_id = ?', $collectionId);
            $select->where('public = ?', 1);
        } else {
            //$items = get_items(array('public'=>true));
            $select->where('public = ?', 1);

        }
        $items = $iTable->fetchObjects($select);
_log('item count: ' . count($items));
        $commonsRecordTable = get_db()->getTable('CommonsRecord');
        foreach($items as $item) {
            //see if item has a CommonsRecord
            $itemRecord = $commonsRecordTable->findByTypeAndId('Item', $item->id);
            if(!$itemRecord) {
                $itemRecord = new CommonsRecord();
                $itemRecord->initFromRecord($item);
            }
            $itemRecord->export(array('webRoot' => $this->webRoot));
            $itemRecord->process_id = $this->_process->id;
            $itemRecord->save();
            release_object($item);
            release_object($itemRecord);
            sleep(1);
        }
    }
}