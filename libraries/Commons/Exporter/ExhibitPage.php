<?php

class Commons_Exporter_ExhibitPage extends Commons_Exporter
{

    protected $typekey = 'exhibits';

    /**
     * Since we're only concerned with exporting the context of Items,
     * start with the page, grab the Section and Exhibit data upstream,
     * and the importer will sort it out.
     *
     *
     * @see plugins/Commons/libraries/Commons/Commons_Exporter::buildRecordData()
     */

    public function buildRecordData()
    {
        //We'll export the full set of data about the exhibit page, chaining up to the Section
        //and Exhibit
        $exhibit = $this->record->Section->Exhibit;
        $section = $this->record->Section;

        $exhibitArray = array();
        $exhibitArray['page'] = array(
            'orig_id' => $this->record->id,
            'title' => $this->record->title,
            'url' => $this->buildRealExhibitUrl(exhibit_builder_exhibit_uri($exhibit, $section, $this->record), -5),
            'site_section_id' => $section->id
        );
        $exhibitArray['section'] = array(
            'orig_id' => $section->id,
            'title' => $section->title,
            'description' => $section->description,
            'url' => $this->buildRealExhibitUrl(exhibit_builder_exhibit_uri($exhibit, $section), -4),
            'site_exhibit_id' => $section->exhibit_id
        );

        $exhibitArray['exhibit'] = array(
            'orig_id'=> $exhibit->id,
            'title'=> $exhibit->title,
            'description' => $exhibit->description,
            'url' => $this->buildRealExhibitUrl(exhibit_builder_exhibit_uri($exhibit), -3)
        );

        return $exhibitArray;
    }

}