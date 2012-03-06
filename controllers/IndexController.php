<?php

class Commons_IndexController extends Omeka_Controller_Action
{

    public function init()
    {
        if (version_compare(OMEKA_VERSION, '2.0-dev', '>=')) {
            $this->_helper->db->setDefaultModelName('CommonsRecord');
        } else {
            $this->_modelClass = 'CommonsRecord';
        }
    }

    public function browseAction()
    {
        $cRecords = $this->getTable()->findAll();
        $this->view->assign('commons_records', $cRecords);
    }


    public function configAction()
    {

        $client = new Zend_Http_Client();
        $client->setUri(COMMONS_API_URL);

        if(!empty($_POST)) {
            if(!empty($_FILES['commons_logo']['name'])) {
                $filePath = COMMONS_PLUGIN_DIR . '/commons_logo/' . $_FILES['commons_logo']['name'];
                if(!move_uploaded_file($_FILES['commons_logo']['tmp_name'], $filePath)) {
                    throw new Exception('Could not save the file to ' . $filePath);
                }
                $client->setFileUpload($filePath, 'logo');
                $logo_url = WEB_ROOT . '/plugins/Commons/commons_logo/' . $_FILES['commons_logo']['name'];
                set_option('commons_logo_url', $logo_url);
            }


            set_option('commons_title_color', $_POST['commons_title_color']);

            $data = Commons_Exporter::exportTemplate();
            $data['configSite'] = true;
            $json = json_encode($data);

            $client->setParameterPost('data', $json);
            $response = $client->request('POST');
            return $response->getBody();
        }
    }

}