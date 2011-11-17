<?php

class Commons_ItemsController extends Omeka_Controller_Action
{
    /**
     *
     * Browse, filter, and select individual items to send to the Commons
     */
    public function addAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $item = get_item_by_id($_POST['itemId']);
		
		//if it's part of a collection, add the data about the collection to the export
		if($item->Collection) {
		    $collectionExporter = new Commons_Exporter_Collection($item->Collection);
		    $collectionExporter->buildRecordData();
		    $collectionExporter->addDataToExport();
		    $exporter = new Commons_Exporter_Item($item, $collectionExporter->exportData);
		} else {
		    $exporter = new Commons_Exporter_Item($item);
		}
		$exporter->buildRecordData();
		$exporter->addDataToExport();
		$status = $exporter->sendToCommons();
		$this->updateCommonsRecord($item, $status);
		if($item->Collection) {
		    $this->updateCommonsRecord($item->Collection, $status);
		}
		echo $status;
    }
    
    public function updateCommonsRecord($record, $status)
    {
        $commonsRecord = $this->getCommonsRecord($record);
        $commonsRecord->status = $status;
        $commonsRecord->last_import = Zend_Date::now()->toString('yyyy-MM-dd HH:mm:ss');
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