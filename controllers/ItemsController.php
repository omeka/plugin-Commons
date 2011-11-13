<?php

class Commons_ItemsController extends Omeka_Controller_Action
{
    /**
     *
     * Browse, filter, and select individual items to send to the Commons
     */
    
    public function addAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$exporter = new Commons_Exporter_Item(get_item_by_id($_POST['itemId']));
		$result = $exporter->buildRecordData();
		print_r($result);
		//$result = $exporter->sendToCommons();
    }
    
    
    
    
}