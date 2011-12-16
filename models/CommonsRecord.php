<?php

class CommonsRecord extends Omeka_Record
{
    public $id;
    public $commons_id;
    public $record_id;
    public $record_type;
    public $last_export;
    public $status;

    protected $_related = array(
        'Record' => 'getRecord'
    );
    
    public function getRecord()
    {
        return $this->getTable($this->record_type)->find($this->record_id);
    }
    
    public function recordLabel()
    {
        //DC:Title if item, name if collection
        switch($this->record_type) {
            case 'Item':
                $title = item('Dublin Core', 'Title', array(), $this->Record);
                if($title) {
                    return $title;
                }
            break;
                
            case 'Collection':
                return $this->Record->name;
                
            break;
        }
    }
    
    public function export($options)
    {
        $recordToExport = $this->getTable()->findOmekaRecord($this->record_type, $this->record_id);
        $method = "export" . $this->record_type;
        $status = $this->$method($recordToExport, $options);
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
    
    private function exportItem($item, $options = null)
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
		$exporter->addDataToExport();

		$status = $exporter->sendToCommons();
		release_object($item);
		return $status;
    }
    
    private function exportCollection($collection, $withItems = false)
    {
        $exporter = new Commons_Exporter_Collection($collection);
	    $exporter->addDataToExport();
	    if($withItems) {
	        $exporter->addItemsToExport();
	    }
	    release_object($collection);
	    return $exporter->sendToCommons();
    }

}