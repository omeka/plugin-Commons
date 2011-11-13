<?php


abstract class Commons_Exporter
{
    public $exportData;
    public $recordData;
    public $record;
    protected $typeKey;

    
    public function __construct($record, $exportData = null)
    {
        $this->record = $record;
        $this->exportData = $exportData ? $exportData : $this->exportTemplate();
        $this->recordData = $this->buildRecordData();
    }
    
    public function addDataToExport()
    {
        if(!isset($this->exportData[$this->typeKey])) {
            $this->exportData[$this->typeKey] = array();
        }
        $this->exportData[$this->typeKey][] = $this->recordData;
    }
    
    abstract public function buildRecordData();
    
    public function sendToCommons()
    {
        //build something fancy-schmancy with Zend_Http?
        $json = json_encode($this->exportData);
        $client = new Omeka_Http_Client();
        $client->setUri(COMMONS_API_URL);
        $client->setParameterPost('data', $json);
        $body = $client->request('POST');
        return $body;
    }
    
    protected function exportTemplate()
    {
        $key = get_option('commons_key');
        $template = array(
            'key' => $key,
            'installation_url' => WEB_ROOT,
        );
        return $template;
    }
}