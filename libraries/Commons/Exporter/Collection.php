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
_log('exportItems 1');
        require_once COMMONS_PLUGIN_DIR . '/libraries/Commons/ItemsExportProcess.php';
        $processDispatcher = new ProcessDispatcher;
_log('exportItems 2');
        $process = $processDispatcher->startProcess('Commons_ItemsExportProcess', null, array('collectionId'=>$this->record->id, 'webRoot'=>WEB_ROOT));
_log('exportItems 3');
        return $process->id;
    }
}
