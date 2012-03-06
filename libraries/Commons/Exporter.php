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
        $this->exportData = $exportData ? $exportData : self::exportTemplate();
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
        $client = new Zend_Http_Client();
        $client->setUri(COMMONS_API_URL);
        $client->setParameterPost('data', $json);
        $response = $client->request('POST');
        return $response->getBody();
    }

    static function exportTemplate()
    {
        $key = get_option('commons_key');
        $site = array(
                    'title' => get_option('site_title'),
                    'admin_email' => get_option('administrator_email'),
                    'description' => get_option('description'),
                    'copyright_info' => get_option('copyright'),
                    'author_info' => get_option('author'),
                    'url' => WEB_ROOT,
                    'logo_url' => get_option('commons_logo_url'),
                    'commons_title_color' =>get_option('commons_title_color')
                    );
        $template = array(
            'key' => $key,
            'site_url' => WEB_ROOT,
            'site' => $site
        );
        return $template;
    }

    public function buildRealUrl($omeka_uri) {
        $parts = explode('/', $omeka_uri);
        unset($parts[0]);
        unset($parts[1]);
        $omeka_uri = implode('/', $parts);
        $url = WEB_ROOT . '/' . $omeka_uri;
        return $url;
    }



}