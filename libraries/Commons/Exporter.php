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
        try {
            $response = $client->request('POST');
            //$responseBody = substr(stripslashes($response->getBody()), 1, -1);
            $responseBody = $response->getBody();
            debug("Exporter::sendToCommons() response: $responseBody");
            $responseArray = json_decode($responseBody, true);
        } catch (Exception $e) {
            _log($e);
            $responseArray = array('status'=>'error', 'messages'=>$e->getMessage());
        }
        return $responseArray;
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
                    'banner_url' => get_option('commons_banner_url'),
                    'commons_title_color' =>get_option('commons_title_color')
                    );
        $template = array(
            'api_key' => $key,
            'site_url' => WEB_ROOT,
            'site' => $site
        );
        return $template;
    }

    public function buildRealUrl($url) {

        if(defined('COMMONS_WEB_ROOT')) {
            $url = str_replace(WEB_ROOT, COMMONS_WEB_ROOT, $url);
        }
        return $url;
    }
}