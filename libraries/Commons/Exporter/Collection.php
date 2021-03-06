<?php

class Commons_Exporter_Collection extends Commons_Exporter
{
    protected $typeKey = 'collections';

    public function buildRecordData()
    {
        $collectionArray = array(
            'orig_id' => $this->record->id,
            'title' => $this->record->name,
            'description' => $this->record->description,
            'url' => $this->buildRealUrl(WEB_ROOT . '/collections/show/' . $this->record->id),
        );
        return $collectionArray;
    }

    public function exportItems()
    {
        require_once COMMONS_PLUGIN_DIR . '/libraries/Commons/ItemsExportProcess.php';
        $processDispatcher = new ProcessDispatcher;
        $process = $processDispatcher->startProcess('Commons_ItemsExportProcess', current_user(), array('collectionId'=>$this->record->id, 'webRoot'=>WEB_ROOT));
        return $process->id;        
    }
}
