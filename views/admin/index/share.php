<?php
$head = array('title' => 'Omeka Commons', 'content_class' => 'horizontal-nav');
echo head($head);

?>
<div id='primary'>

<?php include('admin-nav.php'); ?>

<?php echo flash(); ?>

<p>You can start sending your public items to the Omeka Commons in bulk here. You can also send individual items via their edit screen,
or send items in collections via the collection edit screen. Note that only public items will be sent to the Omeka Commons.
Contextual information about items (collections and exhibit information) will also be sent if those contexts are also public.
</p>


<form enctype="multipart/form-data" action="" method="post">
    <section class="seven columns alpha">
    <div class='field'>
        <div class="two columns alpha">
            <label for='commons_export_all'>Export ALL the items!</label>
        </div>
        <div class="inputs five columns omega">
            <p class='explanation'>You usually only have to do this once. After that, add items to the Omeka Commons as you create them.</p>
            <div class="input-block">
            <input name='commons_export_all' type='checkbox' />
            </div>
        </div>
    </div>

    <div class='field'>
        <div class="two columns alpha">
            <label for="commons-collections[]">Export by collection</label>
        </div>

        <div class="inputs five columns omega">
            <p class="explanation">Send items to the Omeka Commons by collection. Only public items within each collection are sent.</p>

            <?php foreach(loop('collections') as $collection): ?>
            <?php $title = metadata($collection, array('Dublin Core', 'Title')); ?>
            <?php $title = !empty($title) ? $title : __('[Untitled]');?>
            <div>
                <input type="checkbox" name="commons-collections[]" value="<?php echo metadata('collection', 'id'); ?>" /><?php echo $title;  ?>
             </div>
            <?php endforeach; ?>
        </div>
    </div>


     </section>
     <section class="three columns omega">
     <div id="save" class="panel">
     <?php if(get_option('commons_tos')): ?>
         <input id="submit" class="big green button" type="submit" value="Update Commons" name="submit">
     <?php else:?>
         <input id="submit" class="big green button" type="submit" value="Update Commons" name="submit" disabled="disabled">
         <p class='error'>You must agree to the <a href="<?php echo url('commons/index/tos'); ?>">Terms and Conditions</a> before changing settings.</p>
     <?php endif; ?>
     </div>
     </section>
</form>

</div>

<?php echo foot();?>