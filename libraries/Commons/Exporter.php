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
    
    public function addDataToExport($recordData = null, $typeKey = null)
    {
        $data = $recordData ? $recordData : $this->recordData;
        $typeKey = $typeKey ? $typeKey : $this->typeKey;

        if(!isset($this->exportData[$typeKey])) {
            $this->exportData[$typeKey] = array();
        }
        $this->exportData[$typeKey][] = $data;
        return $this->exportData;
    }
    
    public function setRecordData($key, $value) {
        $this->recordData[$key] = $value;
    }
    
    abstract public function buildRecordData();
    
    public function sendToCommons()
    {
        $json = json_encode($this->exportData);
        $client = new Omeka_Http_Client();
        $client->setUri(COMMONS_API_URL);
        $client->setParameterPost('data', $json);
        $response = $client->request('POST');
        
        return $response->getBody();
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
    
    public function buildRealUrl($omeka_uri) {
        $exploded = explode('/', WEB_ROOT);
        unset($exploded[count($exploded) -1]);
        $url = '';
        foreach($exploded as $part) {
            $url .= $part;
        }
        $url.= $omeka_uri;
        return html_escape($url);
    }
            
    
    
}