<?php

class CommonsPlugin extends Omeka_Plugin_Abstract
{
    protected $_hooks = array(
        'admin_append_to_items_show_secondary',
        'admin_append_to_collections_show_primary',
        'admin_theme_header',
        'admin_append_to_collections_form',
        'after_save_form_collection',
        'after_save_form_item',
        'before_delete_item',
        'config',
        'config_form',
        'install',
        'uninstall'
        );

    protected $_filters = array(
        'admin_items_form_tabs',
        'admin_navigation_main'
        );

    protected $_options = array();

    public function hookAdminAppendToCollectionsShowPrimary($collection)
    {
        $record = get_db()->getTable('CommonsRecord')->findByTypeAndId('Collection', $collection->id);
        $html = "<h2>Omeka Commons</h2>";
        if($record) {
            $html .= "<p>Items in this collection are part of the Omeka Commons.</p>";
            $html .= "<p>You can check the status of recently added collections on the <a href='" . uri('commons/index/browse') . "'>Omeka Commons status tab</a>.</p>";
        }
        echo $html;
    }

    public function hookAdminAppendToItemsShowSecondary($item)
    {
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

        $html = "<div class='info-panel'>";
        $html .= "<h2>Omeka Commons</h2>";
        $html .= $link;
        $html .= "</div>";
        echo $html;
    }

    public function hookAdminThemeHeader()
    {

        if(version_compare(OMEKA_VERSION, '1.3', '<')) {
            $jquerySrc = WEB_PLUGIN . '/Commons/views/admin/javascripts/jquery.js';
            $commonsJsSrc = WEB_PLUGIN . '/Commons/views/admin/javascripts/commons.js';
            $commonsCssLink = WEB_PLUGIN . '/Commons/views/admin/css/commons.css';
            echo "<script type='text/javascript' src='$jquerySrc'></script>";
            echo "<script type='text/javascript' src='$commonsJsSrc'></script>";
            echo "<link rel='stylesheet' href='$commonsCssLink'></link>";
        } else {
            queue_js('commons');
            queue_css('commons');
        }
    }

    public function hookAdminAppendToCollectionsForm($collection)
    {
        if(!get_option('commons_key')) {
            return;
        }
        $record = get_db()->getTable('CommonsRecord')->findByTypeAndId('Collection', $collection->id);
        echo $this->commonsForm($record, 'Collection');
    }

    public function hookAfterSaveFormCollection($collection)
    {
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

    public function filterAdminNavigationMain($tabs)
    {
        $tabs['Omeka Commons'] = uri('commons/index/share');
        return $tabs;
    }

    public function filterAdminItemsFormTabs($tabs, $item)
    {
        if(!get_option('commons_key')) {
            return;
        }

        if($item->exists()) {
            $record = get_db()->getTable('CommonsRecord')->findByTypeAndId('Item', $item->id);
        }

        $tabs['Omeka Commons'] = $this->commonsForm($record, 'Item');
        return $tabs;
    }

    public function hookAfterSaveFormItem($item, $post)
    {
        $db = get_db();
        if(!get_option('commons_key')) {
            return;
        }
        $record = $db->getTable('CommonsRecord')->findByTypeAndId('Item', $item->id);
        if(isset($_POST['in_commons']) && $_POST['in_commons'] == 'on' ) {
            $db = get_db();
            if(!$item->public) {
                if($record) {
                    $record->delete();
                }
                throw new Omeka_Validator_Exception("Only public Items can be part of the Omeka Commons");
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

    public function hookBeforeDeleteItem($item)
    {
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
        $sql = "DROP TABLE `$db->CommonsRecord` ;";
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
        $html = "<fieldset>";
        $html .= "<label for='in_commons'>$text</label><input type='checkbox' name='in_commons'";

        if($record) {
            $html .= " checked='checked' />";
        } else {
            $html .= " />";
        }

        $html .= "</fieldset>";

        return $html;
    }

}


