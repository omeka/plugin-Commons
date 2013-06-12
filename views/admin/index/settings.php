<?php
$head = array('title' => 'Omeka Commons Settings', 'content_class' => 'horizontal-nav');
head($head);

?>
<ul id="section-nav" class="navigation">
    <li class="">
        <a href="<?php echo uri('commons/index/site'); ?>">Site information</a>
    </li>
    <li class="">
        <a href="<?php echo uri('commons/index/share'); ?>">Share via Omeka Commons</a>
    </li>
    <li class="">
        <a href="<?php echo uri('commons/index/settings'); ?>">Omeka Commons Settings</a>
    </li>
    <li class="">
        <a href="<?php echo uri('commons/index/browse'); ?>">Status and Overview of items in Omeka Commons</a>
    </li>    

</ul>
<div id='primary'>
<?php echo flash(); ?>
<form enctype="multipart/form-data" action="" method="post">

    
    <div class='field'>
    <label for='commons_key'>API Key</label>
        <div class='inputs'>
            <?php echo __v()->formText('commons_key', get_option('commons_key'), array('size'=>'42')); ?>
            <?php if(get_option('commons_key')) :?>
            <p class='explanation'>Do not change this!</p>
            <?php else: ?>
            <p class='explanation'>You will receive instructions for obtaining your API key when your request has been approved.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class='field'>
    <label for='commons_logo'>Upload a logo</label>
        <div class='inputs'>
            <input name='commons_logo' type='file' />
            <p class='explanation'>Logo image will be displayed at x by y px</p>
            <img src="<?php echo get_option('commons_logo_url'); ?>" />
        </div>
    </div>

    <div class='field'>
    <label for='commons_key'>Consider my items for inclusion in the Digital Public Library of America</label>
        <div class='inputs'>
            <input name='commons_dpla' <?php echo get_option("omeka_dpla") ? "checked='checked'" : ""; ?> type='checkbox' />
            <p class='explanation'>Check this box if you also want to your content to be submitted to the DPLA.</p>
        </div>
    </div>
     <input id="submit" class=" submit" type="submit" value="Apply to join Omeka Commons" name="submit">
</form>

</div>

<?php foot();?>