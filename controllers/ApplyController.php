<?php

class Commons_ApplyController extends Omeka_Controller_Action
{
    public $_modelClass = 'CommonsRecord';

    public function applyAction()
    {
        $this->_helper->viewRenderer->setNoRender();

       $site = array(
                    'title' => get_option('site_title'),
                    'admin_email' => get_option('administrator_email'),
                    'description' => get_option('description'),
                    'copyright_info' => get_option('copyright'),
                    'author_info' => get_option('author'),
                    'url' => WEB_ROOT
                    );
        $data = array();
        $data['site'] = $site;
        $data['institution'] = array(
            'name' => "Institution Name",
            'url' => rtrim(trim("http://example.com/ "), '/')
        );
        $json = json_encode($data);
        $client = new Zend_Http_Client();
        $client->setUri(COMMONS_API_APPLY_URL);
        $client->setParameterPost('data', $json);
        $response = $client->request('POST');

        $response = $response->getBody();
        echo $response;



    }

}