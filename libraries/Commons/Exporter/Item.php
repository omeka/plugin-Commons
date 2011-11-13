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
            'exhibits' => $this->exhibitIds(),
            'itemTypeName' => $this->itemTypeName(),
            'elementTexts' => $this->elementTexts(),
            'files' => $this->files(),
            'tags' => $this->tags(),
        );
        return $itemArray;
        
    }
 
    private function exhibitIds()
    {
     //   $exhibitPageEntryTable = get_db()->getTable('ExhibitPageEntry');
        $db = get_db();
        $itemId = $this->record->id;
        $select = "	SELECT DISTINCT e.id FROM $db->Exhibit e
        			INNER JOIN $db->ExhibitSection s ON s.exhibit_id = e.id
        			INNER JOIN $db->ExhibitPage sp ON sp.section_id = s.id
        			INNER JOIN $db->ExhibitPageEntry ip ON ip.page_id = sp.id
        			WHERE ip.item_id = $itemId ";

        $exhibits = $db->fetchCol($select);
        return $exhibits;
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