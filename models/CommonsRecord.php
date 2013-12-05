<?php

class CommonsRecord extends Omeka_Record_AbstractRecord
{
    public $id;
    public $commons_id;
    public $record_id;
    public $record_type;
    public $last_export;
    public $status;
    public $status_message;
    protected $response;

    protected $_related = array(
        'Record' => 'getRecord',
    );

    public function getRecord()
    {
        return $this->getTable($this->record_type)->find($this->record_id);
    }

    public function getRecordLabel()
    {
        return metadata($this->Record, array('Dublin Core', 'Title'));
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function export($options = false)
    {
        $method = "export" . $this->record_type;
        $response = json_decode($this->$method($this->Record, $options), true);
        $this->last_export = Zend_Date::now()->toString('yyyy-MM-dd HH:mm:ss');
        $this->save();
    }

    public function makePrivate($item)
    {
        $data = Commons_Exporter::exportTemplate();
        $data['privatizeItem'] = $item->id;
        $json = json_encode($data);
        $client = new Zend_Http_Client();
        $client->setUri(COMMONS_API_URL . '/privatize-item');
        $client->setParameterPost('data', $json);
        $response = $client->request('POST');
        return $response->getBody();
    }

    public function initFromRecord($record)
    {
        $this->record_id = $record->id;
        $this->record_type = get_class($record);
    }

    public function getProperty($property)
    {
        if($property == 'label') {
            return $this->getRecordLabel();
        }
        return parent::getProperty($property);
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
        debug('exportItem exporter: ' . get_class($exporter));
        $exporter->addDataToExport();
        if(isset($options['webRoot'])) {
            $exporter->exportData['site_url'] = $options['webRoot'];
            $exporter->exportData['site']['url'] = $options['webRoot'];
        }

        $response = $exporter->sendToCommons();
        if(!$response) {
            debug('no response sending record to commons');
            return;
        }
        $this->response = $response;
        //record errors related to authentication before checking item save status
        if(isset($response['status']) && $response['status'] == 'error') {
            $this->status_message = $response['messages'];
            $this->status = 'error';
        } else {
            $itemStatuses = $response['items'];
            $status = $response['items'][$this->record_id];
            foreach($status as $column=>$value) {
                $this->$column = $value;
                debug($column . ' ' . $value);
            }
            $fileStatuses = $response['files'];

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
            $exporter->exportItems();
        }
        release_object($collection);
        $response = $exporter->sendToCommons();
        //@TODO: what happens if commons is unavailable or times out?
        //record errors related to authentication before checking collection save status
        if(isset($response['status']) && $response['status'] == 'error') {
            $this->status_message = $response['messages'];
            $this->status = 'error';
        } else {
            $collectionStatuses = $response['Collections'];
            $status = $collectionStatuses[$this->record_id];
            foreach($status as $column=>$value) {
                $this->$column = $value;
            }
        }
    }
}