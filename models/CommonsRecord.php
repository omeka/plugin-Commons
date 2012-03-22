<?php

class CommonsRecord extends Omeka_Record
{
    public $id;
    public $commons_item_id;
    public $record_id;
    public $record_type;
    public $last_export;
    public $status;
    public $status_message;

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

    public function export($options = false)
    {
        $recordToExport = $this->getTable()->findOmekaRecord($this->record_type, $this->record_id);
        $method = "export" . $this->record_type;
        $response = json_decode($this->$method($recordToExport, $options), true);
        $this->last_export = Zend_Date::now()->toString('yyyy-MM-dd HH:mm:ss');
        $this->save();
        return true;
    }

    public function makePrivate($item)
    {
        $data = Commons_Exporter::getTemplate();
        $data['deleteItem'] = $item->id;
        $json = json_encode($data);
        $client = new Zend_Http_Client();
        $client->setUri(COMMONS_API_URL);
        $client->setParameterPost('data', $json);
        $response = $client->request('POST');
        return $response->getBody();
    }

    public function initFromRecord($record)
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
        $exporter->addDataToExport();
        $response = $exporter->sendToCommons();
        $itemStatuses = $response['items'];
        $status = $response['items'][$this->record_id];
        foreach($status as $column=>$value) {
            $this->$column = $value;
        }
        release_object($item);
    }

    private function exportCollection($collection, $withItems = false)
    {
        //push this off to a job in case there are many items

        $exporter = new Commons_Exporter_Collection($collection);
        $exporter->addDataToExport();
        if($withItems) {
            $exporter->addItemsToExport();
        }
        release_object($collection);
        return $exporter->sendToCommons();
    }

}