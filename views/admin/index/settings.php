<?php
$head = array('title' => 'Omeka Commons', 'content_class' => 'horizontal-nav');
echo head($head);

?>
<div id='primary'>
<?php include('admin-nav.php'); ?>
<?php echo flash(); ?>
<form enctype="multipart/form-data" action="" method="post">
<section class="seven columns alpha">

    <div class='field'>
        <div class="two columns alpha">
            <label for='commons_key'>API Key</label>
        </div>
        <div class="inputs five columns omega">
            <?php if(get_option('commons_key')) :?>
            <p class='explanation'>Do not change this unless you have been issued a new key.</p>
            <?php else: ?>
            <p class='explanation'>You will receive instructions for obtaining your API key when your request has been approved.</p>
            <?php endif; ?>
            <div class="input-block">
                <?php echo $this->formText('api_key', get_option('commons_key'), array('size'=>'42')); ?>
            </div>

        </div>
    </div>

    <div class='field'>
    <div class="two columns alpha">
        <label for='commons_logo'>Upload a logo</label>
    </div>
        <div class="inputs five columns omega">
            <p class='explanation'>Logo image will be displayed at x by y px</p>
            <div class="input-block">
                <input name='commons_logo' type='file' />
                <br />
                <img style="margin: 5px" width="50" height="50" src="<?php echo get_option('commons_logo_url'); ?>" />
            </div>

        </div>
    </div>

</section>
<section class="three columns omega">
    <div id="save" class="panel">
        <input id="submit" class="big green button" type="submit" value="Update settings" name="submit">
    </div>
 </section>
</form>

</div>

<?php echo foot();?>