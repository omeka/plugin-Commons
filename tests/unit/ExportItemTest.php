<?php

class ExportItemTest extends Commons_Test_AppTestCase
{
    
    
    public function testNullData()
    {
        $record = $this->db->getTable('Item')->find(1);
        
        $exporter = new Commons_Exporter_Item($record);
        $this->assertEquals( 'Item', get_class($exporter->record));
        $this->assertTrue(is_array($exporter->recordData));
        $this->assertTrue(is_array($exporter->exportData));
        
        $this->assertEquals('123', $exporter->exportData['key']);
        $this->assertNull($exporter->recordData['collection']);
        
    }
    
    public function testArrayData()
    {
        $record = $this->db->getTable('Item')->find(1);
        $template = array(
            'key' => get_option('commons_key'),
            'installation_url' => WEB_ROOT,
            'collection' => 5,
        
        );
        $exporter = new Commons_Exporter_Item($record, $template);
        $this->assertEquals(5, $exporter->exportData['collection']);
    }
    
    public function testExhibitIds()
    {
        $record = $this->db->getTable('Item')->find(1);
        
        $exhibit = new Exhibit;
        $exhibit->title = 'Exhibit';
        $exhibit->slug = 'exhibit';
        $exhibit->save();
        
        $section = new ExhibitSection;
        $section->title = 'ExhibitSection';
        $section->slug = 'exhibit/exhibit-section';
        $section->exhibit_id = $exhibit->id;
        $section->order = 1;
        $section->save();
        
        $page = new ExhibitPage;
        $page->title = 'ExhibitPage';
        $page->section_id = $section->id;
        $page->slug = 'exhibit-page';
        $page->layout = 'text';
        $page->order = 1;
        $page->save();
        
        $entry = new ExhibitPageEntry;
        $entry->item_id = $record->id;
        $entry->page_id = $page->id;
        $entry->text = '';
        $entry->caption = '';
        $entry->order = 1;
        $entry->save();
       
        $exporter = new Commons_Exporter_Item($record);
        $this->assertEquals(1, count($exporter->recordData['exhibits']));
        
    }
    
    public function testAddDataToExport()
    {
        $record = $this->db->getTable('Item')->find(1);
        $exporter = new Commons_Exporter_Item($record);
        $exporter->addDataToExport();
        $this->assertEquals(1, count($exporter->exportData['items']));
                
    }
    
    public function testSendToCommons()
    {
        $record = $this->db->getTable('Item')->find(1);
        $exporter = new Commons_Exporter_Item($record);
        $exporter->addDataToExport();
        $exporter->sendToCommons();
    }
    
}