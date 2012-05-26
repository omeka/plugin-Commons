<?php

class CommonsRecord extends Omeka_Record
{
    public $id;
    public $commons_id;
    public $record_id;
    public $record_type;
    public $last_export;
    public $status;
    public $status_message;
    public $process_id;

    protected $_related = array(
        'Record' => 'getRecord',
        'Process' => 'getProcess'
    );

    public function getRecord()
    {
        return $this->getTable($this->record_type)->find($this->record_id);
    }

    public function getProcess()
    {
        return $this->getTable('Process')->find($this->process_id);
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
        $method = "export" . $this->record_type;
        $response = json_decode($this->$method($this->Record, $options), true);
        $this->last_export = Zend_Date::now()->toString('yyyy-MM-dd HH:mm:ss');
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

    private function exportItem($item, $options = array())
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
        if(isset($options['webRoot'])) {
            $exporter->exportData['site_url'] = $options['webRoot'];
            $exporter->exportData['site']['url'] = $options['webRoot'];
        }

        $response = $exporter->sendToCommons();

        //record errors related to authentication before checking item save status
        if($response['status'] == 'error') {
            $this->status_message = $response['messages'];
            $this->status = 'error';
        } else {
            $itemStatuses = $response['items'];
            $status = $response['items'][$this->record_id];
            foreach($status as $column=>$value) {
                $this->$column = $value;
            }
        }

        release_object($item);
    }

    private function exportCollection($collection, $withItems = false)
    {

        $exporter = new Commons_Exporter_Collection($collection);
        $exporter->addDataToExport();
        if($withItems) {
            //$exporter->exportItems fires off a job to prevent timeouts
            //each item is exported separately
            $processId = $exporter->exportItems();
            $this->process_id = $processId;
        }

        release_object($collection);
        $response = $exporter->sendToCommons();
        //record errors related to authentication before checking collection save status
        if($response['status'] == 'error') {
            $this->status_message = $response['messages'];
            $this->status = 'error';
        } else {
            $collectionStatuses = $response['Collections'];
            $status = array_values($collectionStatuses);
            foreach($status[0] as $column=>$value) {
                $this->$column = $value;
            }
        }
        $this->save();
    }
}