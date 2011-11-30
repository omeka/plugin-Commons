<?php

class CommonsRecord extends Omeka_Record
{
    public $id;
    public $commons_id;
    public $record_id;
    public $record_type;
    public $license;
    public $last_export;
    public $status;

    public function export()
    {
        $recordToExport = $this->getTable()->findOmekaRecord($this->record_type, $this->record_id);
        $method = "export" . $this->record_type;
        $status = $this->$method($recordToExport);
        $statusArray = json_decode($status, true);
        $this->status = serialize($statusArray);
        $this->last_export = Zend_Date::now()->toString('yyyy-MM-dd HH:mm:ss');

        if(isset($statusArray['id'])) {
            $this->commons_id = $statusArray['id'];
        }
        $this->save();
        return $status;
    }
    
    protected function initFromRecord($record)
    {
        $this->record_id = $record->id;
        $this->record_type = get_class($record);
    }
    
    private function exportItem($item)
    {
        if(!$item->public) {
            $this->delete();
            throw new Omeka_Validator_Exception("Only public Items can be part of the Omeka Commons");
        }
    
		//if it's part of a collection, add the data about the collection to the export
		if($item->Collection) {
		    $collectionExporter = new Commons_Exporter_Collection($item->Collection);
		    $collectionExporter->addDataToExport();
		    $exporter = new Commons_Exporter_Item($item, $collectionExporter->exportData);
		} else {
		    $exporter = new Commons_Exporter_Item($item);
		}
		$exporter->setRecordData('license', $this->license);
		$exporter->addDataToExport();
		$status = $exporter->sendToCommons();
		release_object($item);
		return $status;
    }
    
    private function exportCollection($collection)
    {
        $exporter = new Commons_Exporter_Collection($collection);
	    $exporter->setRecordData('license', $this->license);
	    $exporter->addDataToExport();
	    release_object($collection);
	    return $exporter->sendToCommons();
    }
    
    private function processItemsForCollection($export = true) {
        if($this->record_type != 'Collection') {
            throw new Exception('processItemsForCollection cannot be called on non-Collection CommonsRecords');
        }
        
        $db = $this->_db;
        
        $items = get_items(array('collection'=>$this->record_id));
        foreach($items as $item) {
            //see if item has a CommonsRecord
            $itemRecord = $db->getTable('CommonsRecord')->findByTypeAndId('Collection', $collection->id);
            if($itemRecord) {
                //if an item has already been separately added to commons, this will override the license!!
                if($itemRecord->license != $this->license ) {
                    $itemRecord->license = $this->license;
                }
            } else {
                $itemRecord = new CommonsRecord();
                $itemRecord->initFromRecord($item);
            }
            $itemRecord->save();
        }
        if($export) {
            $itemRecord->export();
        }
    }
}