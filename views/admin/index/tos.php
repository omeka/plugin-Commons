<?php

echo head(array('title'=>__("Omeka Commons Terms of Service and Usage")));

?>
<div id='primary'>
<?php include('admin-nav.php'); ?>
<form enctype="multipart/form-data" action="" method="post">
<section class="seven columns alpha">
<h2>ToS text to go here</h2>

</section>
    <section class="three columns omega">
        <div id="save" class="panel">
            <input id="agree" class="big green button" type="submit" value="<?php echo __('Agree'); ?>" name="submit" />
        </div>
    </section>   
</form>
</div>


<?php echo foot(); ?>