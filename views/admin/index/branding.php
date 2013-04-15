<?php
$head = array('title' => 'Omeka Commons Branding', 'content_class' => 'horizontal-nav');
echo head($head);

?>
<ul id="section-nav" class="navigation">
    <li class="">
        <a href="<?php echo url('commons/index/share'); ?>">Share via Omeka Commons</a>
    </li>
    <li class="">
        <a href="<?php echo url('commons/index/branding'); ?>">Commons branding options</a>
    </li>
    <li class="">
        <a href="<?php echo url('commons/index/browse'); ?>">Status and Overview of items in Omeka Commons</a>
    </li>    

</ul>
<div id='primary'>
<?php echo flash(); ?>
<form enctype="multipart/form-data" action="" method="post">

    <div class='field'>
        <label for='commons_title_color'>Pick a color for the title of your site on Omeka Commons</label>
        <div class='inputs'>
            <input name='commons_title_color' value='<?php echo get_option('commons_title_color'); ?>' type='text' />
        </div>
    </div>

    <div class='field'>
    <label for='commons_logo'>Upload a logo</label>
        <div class='inputs'>
            <input name='commons_logo' type='file' />
            <p class='explanation'>Logo should be no wider than 250px (or some other number Kim will figure out)</p>
            <img src="<?php echo get_option('commons_logo_url'); ?>" />
        </div>
    </div>


    <div class='field'>
    <label for='commons_banner'>Upload a banner image</label>
        <div class='inputs'>
            <input name='commons_banner' type='file' />
            <p class='explanation'>Banner should be no wider than 500px or whatever</p>
            <img src="<?php echo get_option('commons_banner_url'); ?>" />
        </div>
    </div>
     <input id="submit" class=" submit" type="submit" value="Save Changes" name="submit">
</form>

</div>

<?php echo foot();?>