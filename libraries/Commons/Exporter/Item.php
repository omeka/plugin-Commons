<?php


class Commons_Exporter_Item extends Commons_Exporter
{
    protected $typeKey = 'items';

    public function buildRecordData()
    {
        $itemArray = array(
            'orig_id' => $this->record->id,
            'url' => WEB_ROOT . '/items/show/' . $this->record->id,
            'collection' => $this->record->collection_id,
            'itemTypeName' => $this->itemTypeName(),
            'elementTexts' => $this->elementTexts(),
            'files' => $this->files(),
            'tags' => $this->tags(),
        );
        if(plugin_is_active('ExhibitBuilder')) {
            $itemArray['exhibitPages'] = $this->exhibitPages();
        }
        return $itemArray;

    }

    private function exhibitPages()
    {
     //   $exhibitPageEntryTable = get_db()->getTable('ExhibitPageEntry');
        $db = get_db();
        $itemId = $this->record->id;
        $select = "	SELECT DISTINCT sp.* FROM $db->ExhibitPage sp
                    INNER JOIN $db->ExhibitPageEntry epe ON epe.page_id = sp.id
                    WHERE epe.item_id = $itemId ";
        $exhibitPages = $db->getTable('ExhibitPage')->fetchObjects($select);
        $exhibitPageIds = array();
        foreach ($exhibitPages as $record) {
            $exporter = new Commons_Exporter_ExhibitPage($record);
            $this->addDataToExport($exporter->recordData, 'exhibits');
            $exhibitPageIds[] = $record->id;
        }
        return $exhibitPageIds;
    }

    private function elementTexts()
    {
        $ops = array('return_type'=> 'array');
        $elTexts = show_item_metadata($ops, $this->record);
        foreach($elTexts as $set=>$elements) {
          foreach($elements as $element=>$texts) {
            foreach($texts as $index=>$text) {
              $elTexts[$set][$element][$index] = array('text'=>$text, 'html'=>true);
            }
          }
        }
        return $elTexts;
    }

    private function itemTypeName()
    {
        $itemType = $this->record->getItemType();
        if($itemType) {
            return $itemType->name;
        }
        return false;
    }

    private function files()
    {
        $filesArray = array();
        foreach($this->record->Files as $file) {
            $filesArray[] = WEB_ROOT . "/archive/files/" . $file->archive_filename;
        }
        return $filesArray;
    }

    private function tags()
    {
        $tags = get_tags(array('record'=>$this->record), null);
        foreach($tags as $tag) {
            $tagsArray[] = $tag->name;
        }
        return $tagsArray;
    }
}