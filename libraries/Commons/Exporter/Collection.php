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
            'url' => WEB_ROOT . '/collections/show/' . $this->record->id,
        );
        return $collectionArray;
    }

    public function addItemsToExport()
    {
        $commonsRecordTable = get_db()->getTable('CommonsRecord');
        $items = get_items(array('collection'=>$this->record->id), null);
        foreach($items as $item) {
            //see if item has a CommonsRecord
            $itemRecord = $commonsRecordTable->findByTypeAndId('Item', $item->id);
            if(!$itemRecord) {
                $itemRecord = new CommonsRecord();
                $itemRecord->initFromRecord($item);
            }
            $itemRecord->export();
            $itemRecord->save();
            //$itemExporter = new Commons_Exporter_Item($item);
            //$this->addDataToExport($itemExporter->recordData, 'items');
            release_object($item);
        }

    }



}