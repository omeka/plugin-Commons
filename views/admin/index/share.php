<?php
$head = array('title' => 'Omeka Commons Branding', 'content_class' => 'horizontal-nav');
echo head($head);

?>
<ul id="section-nav" class="navigation">
<?php $navArray = array(
        'site' => array('label' => 'Site Information', 'uri' => url('commons/index/site')),
        'share' => array('label' => 'Share', 'uri' => url('commons/index/share')),
        'settings' => array('label' => 'Settings', 'uri' => url('commons/index/settings')),
        'browse' => array('label' => 'Items Overview', 'uri' => url('commons/index/browse'))
        );

echo nav($navArray);

?>
</ul>
<div id='primary'>
<?php echo flash(); ?>
<p>You can start sending your public items to the Omeka Commons in bulk here. You can also send individual items via their edit screen,
or send items in collections via the collection edit screen. Note that only public items will be sent to the Omeka Commons.
Contextual information about items (collections and exhibit information) will also be sent if those contexts are also public.
</p> 

<p>You can also customize your display case in the Omeka Commons at <a href="<?php echo url('commons/index/branding'); ?>">Commons branding options</a></p>

<form enctype="multipart/form-data" action="" method="post">

    <div class='field'>
        <label for='commons_export_all'>Export ALL the items!</label>
        <div class='inputs'>
            <input name='commons_export_all' type='checkbox' />
            <p class='explanation'>You usually only have to do this once. After that, add items to the Omeka Commons as you create them.</p>
        </div>
    </div>

    <div class='field commons-collections'>
        <label for="commons-collections[]">Export by collection</label>
        <p class="explanation">Send items to the Omeka Commons by collection. Only public items within each collection are sent.</p>
        <div class="inputs">
            <?php foreach(loop('collection') as $collection): ?>
                <input type="checkbox" name="commons-collections[]" value="<?php echo metadata('collection', 'id'); ?>" /><?php echo metadata('collection', array('Dublin Core', 'Title'));  ?>
                 <br />
            <?php endforeach; ?>
        </div>
    </div>
    
     <input id="submit" class=" submit" type="submit" value="Save Changes" name="submit">
</form>

</div>

<?php echo foot();?>