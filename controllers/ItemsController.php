<?php

class Commons_ItemsController extends Omeka_Controller_Action
{
   
    public function ajaxAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $item = get_item_by_id($_POST['itemId']);
        $itemRecord = $this->getCommonsRecord($item);
        $status = $itemRecord->export();
		echo $status;
    }
    
    public function updateCommonsRecord($record, $status)
    {
        $statusArray = json_decode($status, true);
        $commonsRecord = $this->getCommonsRecord($record);
        $commonsRecord->status = $status;
        $commonsRecord->last_import = Zend_Date::now()->toString('yyyy-MM-dd HH:mm:ss');

        if(isset($statusArray['item_id'])) {
            $commonsRecord->commons_id = $statusArray['item_id'];
        }
        $commonsRecord->save();
    }
    
    public function getCommonsRecord($record)
    {
        $params = array('record_id' => $record->id, 'record_type' => get_class($record) );
        $commonsRecords = $this->getDb()->getTable('CommonsRecord')->findBy($params);
        if(empty($commonsRecord)) {
            $commonsRecord = new CommonsRecord();
            $commonsRecord->record_id = $record->id;
            $commonsRecord->record_type = get_class($record);
            $commonsRecord->save();
            return $commonsRecord;
        } else {
            return $commonsRecords[0];
        }
        
    }

    
    
}