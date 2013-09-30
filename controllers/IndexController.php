<?php

class Commons_IndexController extends Omeka_Controller_AbstractActionController
{
    protected $_browseRecordsPerPage = 10;

    public function init()
    {
            $this->_helper->db->setDefaultModelName('CommonsRecord');
    }
    
    public function browseAction()
    {
        if(isset($_POST['commons-delete-all']) && $_POST['commons-delete-all'] == 'on') {
            $records = $this->_helper->db->getTable('CommonsRecord')->findAll();
            foreach($records as $record) {
                $record->delete();
            }
        }
        
        if(isset($_POST['commons-delete'])) {
            $table = $this->_helper->db->getTable('CommonsRecord');
            foreach($_POST['commons-delete'] as $id) {
                $record = $table->find($id);
                $record->delete();
            }
        }
        
        if(!$this->_hasParam('sort_field')) {
            $this->_setParam('sort_field', 'last_export');
        }
    
        if(!$this->_hasParam('sort_dir')) {
            $this->_setParam('sort_dir', 'd');
        }
        parent::browseAction();
    }
    
    
    /**
     * passes the request to apply along to the Commons so JS doesn't try to AJAX to different domain
     */

    public function applyAction()
    {
        $client = new Zend_Http_Client();
        $client->setURI(COMMONS_API_SETTINGS_URL . 'apply');
        if(empty($_POST)) {
            $response = array('status' => 'ERROR', 'message' => "Couldn't connect to server. Please try again");
            $this->_helper->json($response);
            die();
        }
        $client->setParameterPost('data', $_POST['data']);

        $response = $client->request('POST');
        //$this->_helper->json($response->getBody());
        echo $response->getBody();
        die();
    }

    public function settingsAction()
    {
        $client = new Zend_Http_Client();
        $client->setUri(COMMONS_API_SETTINGS_URL . 'update');
        if(! is_writable(COMMONS_PLUGIN_DIR . '/commons_images')) {
            $this->_helper->flashMessenger('commons_images directory must be writable by the server', 'error');
        }

        if(!empty($_POST)) {
            set_option('commons_key', $_POST['api_key']);
            if(!empty($_FILES['commons_logo']['name'])) {
                $filePath = COMMONS_PLUGIN_DIR . '/commons_images/' . $_FILES['commons_logo']['name'];
                if(!move_uploaded_file($_FILES['commons_logo']['tmp_name'], $filePath)) {
                    $this->_helper->flashMessenger('Could not save the file to ' . $filePath, 'error');
                    return;
                }
                $client->setFileUpload($filePath, 'logo');
                $logo_url = WEB_ROOT . '/plugins/Commons/commons_images/' . $_FILES['commons_logo']['name'];
                set_option('commons_logo_url', $logo_url);
            }

            $data = Commons_Exporter::exportTemplate();
            $data['configSite'] = true;
            $client->setParameterPost('data', $data);
            $response = $client->request('POST');
            $responseArray = json_decode($response->getBody(), true);
            if($responseArray['status'] == 'ERROR') {
                $this->_helper->flashMessenger($responseArray['message'], 'error');
            } else {
                $this->_helper->flashMessenger($responseArray['message'], 'success');
            }
        }
    }
    
    public function shareAction()
    {
        //first check that any export can happen yet.
        $response = $this->checkCommonsStatus();
        if($response['status'] == 'OK') {
            if(isset($_POST['commons_export_all']) && $_POST['commons_export_all'] == 'on') {
                require_once COMMONS_PLUGIN_DIR . '/libraries/Commons/ItemsExportJob.php';
                Zend_Registry::get('bootstrap')->getResource('jobs')->send('Commons_ItemsExportJob');
            } else if(!empty($_POST['commons-collections'])) {
                foreach($_POST['commons-collections'] as $collectionId) {
                    $record = $this->_helper->db->getTable('CommonsRecord')->findByTypeAndId('Collection', $collectionId);
                    if(!$record) {
                        $record = new CommonsRecord();
                        $record->record_id = $collectionId;
                        $record->record_type = 'Collection';
                    }
                    $record->export(true);
                    $record->save();
                    if($record->status == 'error') {
                        debug("shareAction: record status error");
                        $this->_helper->flashMessenger($record->status_message, 'error');
                        break;
                    }          
                }
            }
        } else {
            switch($response['status']) {
                case 'EXISTS':
                    $flashStatus = 'alert';
                    break;
            
                case 'ERROR':
                    $flashStatus = 'error';
                    break;
            
                default:
                    $flashStatus = 'info';
            }
            $this->_helper->flashMessenger($response['message'], $flashStatus);            
        }
        //get all the collections, and echo a note that public ITEMs will go, regardless of whether the collection is public
        //DATA about the collection will only go if the collection itself is public
        $collections = $this->_helper->db->getTable('Collection')->findBy(array('public'=>true));
        $this->view->collections = $collections;        
    }

    public function siteAction()
    {
        if(isset($_POST['submit'])) {
            $client = new Zend_Http_Client();
            if($_POST['submit'] == 'Apply') {
                $client->setURI(COMMONS_API_SETTINGS_URL . 'apply');
            } else {
                $client->setURI(COMMONS_API_SETTINGS_URL . 'update');
            }
            $data = $_POST;
            foreach($data as $option=>$value) {
                set_option('commons_' . $option, $value);
            }
            
            //some data about the site isn't in the form, but in site options
            $data['super_email'] = get_option('administrator_email');
            $data['omeka_version'] = OMEKA_VERSION;
            $data['title'] = get_option('site_title');
            $data['description'] = get_option('site_description');
            $data['url'] = WEB_ROOT;
            $data['copyright_info'] = get_option('copyright');
            
            $client->setParameterPost('data', $data);
            $response = $client->request('POST');
            if($response->getStatus() != 200) {
                $this->_helper->flashMessenger("Error sending data to Omeka Commons. Please try again", 'error');
                return;
            }
            $message = json_decode($response->getBody(), true);
            if(!is_array($message)) {
                debug("Indexcontroller 149 message from Commons: $message");
            }
            switch($message['status']) {
                case 'OK':
                    $flashStatus = 'success';
                    break;
    
                case 'EXISTS':
                    $flashStatus = 'alert';
                    break;
    
                case 'ERROR':
                    $flashStatus = 'error';
                    break;
                    
                default:
                    $flashStatus = 'info';
            }
            $this->_helper->flashMessenger($message['message'], $flashStatus);
        }
    }
    
    protected function checkCommonsStatus()
    {
        $client = new Zend_Http_Client();
        $client->setURI(COMMONS_API_SETTINGS_URL . 'apply');
        $data = array();
        $data['url'] = WEB_ROOT;
        if($key = get_option('commons_key')) {
            $data['api_key'] = $key;
            $client->setURI(COMMONS_API_SETTINGS_URL . 'update');
        } else {
            $client->setURI(COMMONS_API_SETTINGS_URL . 'apply');
        }
        
        $client->setParameterPost('data', $data);
        $response = $client->request('POST');
        if($response->getStatus() != 200) {
            $this->_helper->flashMessenger("Error sending data to Omeka Commons. Please try again", 'error');
            
        }
        $responseArray = json_decode($response->getBody(), true);  
        return $responseArray; 
    }
}