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
        $client->setURI(COMMONS_API_APPLY_URL);
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


    public function brandingAction()
    {
        $client = new Zend_Http_Client();
        $client->setUri(COMMONS_API_URL);

        if(! is_writable(COMMONS_PLUGIN_DIR . '/commons_images')) {
            debug( COMMONS_PLUGIN_DIR . '/commons_images' );
            $this->_helper->flashMessenger('commons_images directory must be writable by the server', 'error');
        }


        if(!empty($_POST)) {
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
            if(!empty($_FILES['commons_banner']['name'])) {
                $filePath = COMMONS_PLUGIN_DIR . '/commons_images/' . $_FILES['commons_banner']['name'];
                if(!move_uploaded_file($_FILES['commons_banner']['tmp_name'], $filePath)) {
                    throw new Exception('Could not save the file to ' . $filePath);
                }
                $client->setFileUpload($filePath, 'banner');
                $banner_url = WEB_ROOT . '/plugins/Commons/commons_images/' . $_FILES['commons_banner']['name'];
                set_option('commons_banner_url', $banner_url);
            }
            set_option('commons_title_color', $_POST['commons_title_color']);

            $data = Commons_Exporter::exportTemplate();
            $data['configSite'] = true;
            $json = json_encode($data);

            $client->setParameterPost('data', $json);
            $response = $client->request('POST');
            $responseJson = json_decode( $response->getBody() , true );
            if($responseJson['status'] == 'error') {
                $this->_helper->flashMessenger($responseJson['status'], 'error');
            }
        }
    }
    
    public function shareAction()
    {
        
        if(isset($_POST['commons_export_all']) && $_POST['commons_export_all'] == 'on') {
            require_once COMMONS_PLUGIN_DIR . '/libraries/Commons/ItemsExportProcess.php';
            $processDispatcher = new ProcessDispatcher;
            $process = $processDispatcher->startProcess('Commons_ItemsExportProcess', current_user(), array('webRoot'=>WEB_ROOT));
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
            }
            
        }
        
        //get all the collections, and echo a note that public ITEMs will go, regardless of whether the collection is public
        //DATA about the collection will only go if the collection itself is public
        $collections = $this->_helper->db->getTable('Collection')->findBy(array('public'=>true));
        $this->view->collections = $collections;        
    }

}