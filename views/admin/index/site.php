<?php

if(get_option('commons_key')) {
    $title = 'Update info for the Commons';
} else {
    $title = 'Apply to be part of the Omeka Commons';
}
$head = array('title' => $title, 'content_class' => 'horizontal-nav');
head($head);


?>
<div id='primary'>
<?php echo flash(); ?>

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

<h1><?php echo $title; ?></h1>
<?php if($message):?>

<?php echo $message; ?>

<?php else: ?>
<form enctype="multipart/form-data" action="" method="post">    
    <div class='field'>
    <label for='commons_site_admin_email'>Email address</label>
        <div class='inputs'>
            <?php echo __v()->formText('commons_site_admin_email', get_option('commons_site_admin_email'), array('size'=>'20')); ?>
            <p class='explanation'>This will be used to create an account in the Commons for you to administer you content there.</p>
        </div>
    </div>
    
    
    <div class='field'>
    <label for='commons_site_admin_name'>Name</label>
        <div class='inputs'>
            <?php echo __v()->formText('commons_site_admin_name', get_option('commons_site_admin_name'), array('size'=>'20')); ?>
            <p class='explanation'>Your name</p>
        </div>
    </div>
    
    
    <div class='field'>
    <label for='commons_site_admin_username'>Username</label>
        <div class='inputs'>
            <?php echo __v()->formText('commons_site_admin_username', get_option('commons_site_admin_username'), array('size'=>'20')); ?>
            <p class='explanation'>We will try to create an account with this username. If it is taken, we will create something close, and you can change it in the Commons.</p>
        </div>
    </div>
    
    
    <div class='field'>
    <label for='commons_site_admin_affiliation'>Affiliation</label>
        <div class='inputs'>
            <?php echo __v()->formText('commons_site_admin_affiliation', get_option('commons_site_admin_affiliation'), array('size'=>'42')); ?>
            <p class='explanation'>Organization with which you are affiliated.</p>
        </div>
    </div>        
    
    <div class='field'>
    <label for='commons_site_content_summary'>Content summary</label>
        <div class='inputs'>
            <?php echo __v()->formTextarea('commons_site_content_summary', get_option('commons_site_content_summary'), array('rows'=>10)); ?>
            <p class='explanation'>A brief summary of the kind of content you will be sending to the Commons.</p>
        </div>
    </div>
    
    
    <div class='field'>
    <label for='commons_site_reason'>Reason for contributing</label>
        <div class='inputs'>
            <?php echo __v()->formTextarea('commons_site_reason', get_option('commons_site_reason'), array('rows'=>10)); ?>
            <p class='explanation'>Why you would like to contribute content from your site to the Commons.</p>
        </div>
    </div>

    <input type='hidden' name='commons_in_commons' value='<?php echo get_option('commons_key'); ?>' />
    <?php if(get_option('commons_key')): ?>
    <input id="submit" class="submit" type="submit" value="Update info" name="submit">
    <?php else:?>
    <input id="submit" class="submit" type="submit" value="Apply" name="submit">
    <?php endif; ?>
    
</form>
<?php endif;?>
</div>

<?php 
foot();
?>