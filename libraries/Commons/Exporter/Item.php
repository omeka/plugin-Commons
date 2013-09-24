<?php


class Commons_Exporter_Item extends Commons_Exporter
{
    protected $typeKey = 'items';

    public function buildRecordData()
    {
        $itemArray = array(
            'orig_id' => $this->record->id,
            'url' => $this->buildRealUrl(WEB_ROOT . '/items/show/' . $this->record->id),
            'collection' => $this->record->collection_id,
            'itemTypeName' => $this->itemTypeName(),
            'elementTexts' => $this->elementTexts(),
            'files' => $this->files(),
            'tags' => $this->tags(),
        );
        return $itemArray;
    }

    private function elementTexts()
    {
        $ops = array('return_type'=> 'array');
        $elTexts = all_element_texts($this->record, $ops);
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
            $filesArray[] = absolute_url(file_display_url($file, 'original'));
        }
        return $filesArray;
    }

    private function tags()
    {
        $tagsArray = array();
        $tags = $this->record->Tags;
        foreach($tags as $tag) {
            $tagsArray[] = $tag->name;
        }
        return $tagsArray;
    }
}