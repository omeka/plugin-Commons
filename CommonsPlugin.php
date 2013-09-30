<?php

define('COMMONS_PLUGIN_DIR', PLUGIN_DIR . '/Commons');
define('COMMONS_BASE_URL', 'http://localhost/commons');

define('COMMONS_API_URL', 'http://localhost/commons/commons-api/import');
define('COMMONS_API_SETTINGS_URL', 'http://localhost/commons/commons-api/site/');

/*
define('COMMONS_API_URL', 'http://test.omeka.org/omeka-commons/commons-api/import');
define('COMMONS_API_APPLY_URL', 'http://test.omeka.org/omeka-commons/commons-api/site/apply');
*/

class CommonsPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array(
        'admin_items_show_sidebar',
        'admin_items_panel_fields',
        'admin_collections_panel_fields',
        //'admin_collections_show_sidebar',
        'admin_collections_show',
        'admin_theme_header',
        'after_save_collection',
        'after_save_item',
        'before_delete_item',
        'install',
        'uninstall'
        );

    protected $_filters = array(
        'admin_navigation_main'
        );

    protected $_options = array();

    public function hookAdminCollectionsShow($args)
    {
        $collection = $args['collection'];
        $record = get_db()->getTable('CommonsRecord')->findByTypeAndId('Collection', $collection->id);
        $html = "<h4>Omeka Commons</h4>";
        if($record) {
            $html .= "<p>Items in this collection are part of the Omeka Commons.</p>";
            $html .= "<p>You can check the status of recently added collections on the <a href='" . url('commons/index/browse') . "'>Omeka Commons status tab</a>.</p>";
        } else {
            $html .= "<p>Items have not been added to Omeka Commons (unless they were added individually)</p>";
        }
        echo $html;
    }

    public function hookAdminItemsShowSidebar($args)
    {
        $item = $args['item'];
        if(!get_option('commons_key')) {
            return;
        }
        $db = get_db();
        $commonsRecordTable = $db->getTable('CommonsRecord');

        $commonsCollection = $commonsRecordTable->itemPartOfCommonsCollection($item);
        if($commonsCollection) {
            $collection = $db->getTable('Collection')->find($commonsCollection->record_id);
            $link = "<p id='commons-item-collection'>This item is part of the Omeka Commons via collection {$collection->name}</p>";
            $license = $commonsCollection->license;
        } else {
            $findParams = array('record_type'=>'Item', 'record_id'=>$item->id);
            $commonsRecords = $commonsRecordTable->findBy($findParams);
            if(empty($commonsRecords)) {
                $link = "<p id='commons-item-add'>Make this item part of the Omeka Commons</p>";
            } else {
                $commonsRecord = $commonsRecords[0];
                $link = "<p id='commons-item-status'>Already part of the Omeka Commons</p>";
                $license = $commonsRecord->license;
            }
        }

        $html = "<div class='info panel'>";
        $html .= "<h2>Omeka Commons</h2>";
        $html .= $link;
        $html .= "</div>";
        echo $html;
    }

    public function hookAdminThemeHeader($args)
    {
        queue_js_file('commons');
        queue_css_file('commons');
    }

    public function hookAdminCollectionsPanelFields($args)
    {
        $collection = $args['record'];
        if(!get_option('commons_key')) {
            return;
        }
        $record = get_db()->getTable('CommonsRecord')->findByTypeAndId('Collection', $collection->id);
        echo $this->commonsForm($record, 'Collection');
    }

    public function hookAfterSaveCollection($args)
    {
        $collection = $args['record'];
        if(!get_option('commons_key')) {
            return;
        }
        $record = get_db()->getTable('CommonsRecord')->findByTypeAndId('Collection', $collection->id);
        if($collection->public && isset($_POST['in_commons']) && $_POST['in_commons'] == 'on') {
            if(!$record) {
                $record = new CommonsRecord();
                $record->record_id = $collection->id;
                $record->record_type = 'Collection';
            }
            $record->export(true);
            $record->save();
        } else {
            if($record) {
                $record->delete();
            }
        }
    }

    public function filterAdminNavigationMain($navArray)
    {
        $navArray['Omeka Commons'] = array('label'=>__('Omeka Commons'), 'uri'=>url('commons/index/share'));
        return $navArray;
    }

    public function hookAdminItemsPanelFields($args)
    {
        $item = $args['record'];
        if(!get_option('commons_key')) {
            return;
        }

        if($item->exists()) {
            $record = get_db()->getTable('CommonsRecord')->findByTypeAndId('Item', $item->id);
            
            echo $this->commonsForm($record, 'Item');
        }
    }

    public function hookAfterSaveItem($args)
    {
        $item = $args['record'];
        $post = $args['post'];
        $db = get_db();
        if(!get_option('commons_key')) {
            return;
        }
        $record = $db->getTable('CommonsRecord')->findByTypeAndId('Item', $item->id);
        if(isset($post['in_commons']) && $post['in_commons'] == 'on' ) {
            if(!$item->public) {
                if($record) {
                    $record->delete();
                }
                throw new Omeka_Validate_Exception(__("Only public Items can be part of the Omeka Commons"));
            }
            if(!$record) {
                $record = new CommonsRecord();
                $record->record_id = $item->id;
                $record->record_type = 'Item';
            }
            $record->export();
            $record->save();
        } else {
            if($record) {
                $record->delete();
            }
        }
    }

    public function hookBeforeDeleteItem($args)
    {
        $item = $args['item'];
        $db = get_db();
        if(!get_option('commons_key')) {
            return;
        }
        $record = $db->getTable('CommonsRecord')->findByTypeAndId('Item', $item->id);
        if($record) {
            $record->makePrivate($item);
            $record->delete();
        }
    }

    public function hookConfig()
    {
        $this->setOptions($_POST);
    }

    public function hookConfigForm()
    {
        include 'config_form.php';
    }

    public function hookInstall()
    {
        $db = get_db();
        $sql = "
            CREATE TABLE IF NOT EXISTS `$db->CommonsRecord` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `commons_id` int(10) unsigned DEFAULT NULL,
              `record_id` int(10) unsigned NOT NULL,
              `record_type` tinytext NOT NULL,
              `last_export` text,
              `status` tinytext,
              `status_message` text,
              `process_id` int(10) unsigned NULL,
              PRIMARY KEY (`id`),
              KEY `record_id` (`record_id`),
              KEY `process_id` (`process_id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
        ";
        $db->query($sql);
    }

    public function hookUninstall()
    {
        $db = get_db();
        $sql = "DROP TABLE IF EXISTS `$db->CommonsRecord` ;";
        $db->query($sql);
    }

    private function setOptions($post)
    {
        unset($post['install_plugin']);
        foreach($post as $name=>$value) {
            set_option($name, $value);
        }
    }

    private function commonsForm($record, $recordType)
    {
        switch($recordType) {
            case 'Item':
                $text = "Add this item to the Omeka Commons?";
            break;

            case 'Collection':
                $text = "Add items in this collection to the Omeka Commons?";
            break;
        }
        $html = "<div class='field'>";
        $html .= "<label for='in_commons'>$text</label><input type='checkbox' name='in_commons'";

        if($record) {
            $html .= " checked='checked' />";
        } else {
            $html .= " />";
        }
        $html .= "</div>";
        return $html;
    }
}


