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
        $db = get_db();
        $items = get_items(array('collection'=>$this->record->id), null);
        foreach($items as $item) {
            //see if item has a CommonsRecord
            $itemRecord = $db->getTable('CommonsRecord')->findByTypeAndId('Item', $item->id);
            if($itemRecord) {
                //if an item has already been separately added to commons, this will override the license!!
                if($itemRecord->license != $this->license ) {
                    $itemRecord->license = $this->license;
                }
            } else {
                $itemRecord = new CommonsRecord();
                $itemRecord->initFromRecord($item);
            }
            $itemRecord->license = $this->license;
            $itemRecord->save();
            $itemExporter = new Commons_Exporter_Item($item);
            $this->addDataToExport($itemExporter->recordData, 'items');
            release_object($item);
        }
        
    }
    
    

}