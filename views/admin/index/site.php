<?php

if(get_option('commons_key')) {
    $subtitle = __('Update info for the Commons');
} else {
    $subtitle = __('Apply to be part of the Omeka Commons');
}

$title = 'Omeka Commons';
$head = array('title' => $title, 'content_class' => 'horizontal-nav');
echo head($head);

?>
<div id='primary'>

<?php include('admin-nav.php'); ?>

<?php echo flash(); ?>

<h2><?php echo $subtitle; ?></h2>

<form enctype="multipart/form-data" action="" method="post">    
<section class="seven columns alpha">

    <div class='field'>
        <div class="two columns alpha">    
            <label for='admin_email'>Email address</label>
        </div>
        <div class="inputs five columns omega">
            <p class='explanation'>This will be used to create an account in the Commons for you to administer you content there.</p>
            <div class="input-block">
            <?php echo $this->formText('admin_email', get_option('commons_admin_email'), array('size'=>'20')); ?>
            </div>
        </div>
    </div>
    
    
    <div class='field'>
        <div class="two columns alpha">
            <label for='admin_name'>Name</label>
        </div>
            
        <div class="inputs five columns omega">
            <p class='explanation'>Your name</p>
            <div class="input-block">
            <?php echo $this->formText('admin_name', get_option('commons_admin_name'), array('size'=>'20')); ?>
            </div>
        </div>
    </div>
    
    
    <div class='field'>
        <div class="two columns alpha">
            <label for='admin_username'>Username</label>
        </div>
        <div class="inputs five columns omega">
            <p class='explanation'>We will try to create an account with this username. If it is taken, we will create something close, and you can change it in the Commons.</p>
            <div class="input-block">
            <?php echo $this->formText('admin_username', get_option('commons_admin_username'), array('size'=>'20')); ?>
            </div>
        </div>
    </div>
    
    
    <div class='field'>
        <div class="two columns alpha">
            <label for='affiliation'>Affiliation</label>
        </div>
        <div class="inputs five columns omega">
            <p class='explanation'>Organization with which you are affiliated.</p>
            <div class="input-block">
            <?php echo $this->formText('affiliation', get_option('commons_affiliation'), array('size'=>'42')); ?>
            </div>
        </div>
    </div>        
    
    <div class='field'>
        <div class="two columns alpha">
            <label for='content_summary'>Content summary</label>
        </div>
        <div class="inputs five columns omega">
            <p class='explanation'>A brief summary of the kind of content you will be sending to the Commons.</p>
            <div class="input-block">
            <?php echo $this->formTextarea('content_summary', get_option('commons_content_summary'), array('rows'=>10)); ?>
            </div>
        </div>
    </div>
    
    
    <div class='field'>
    <div class="two columns alpha">
        <label for='join_reason'>Reason for contributing</label>
    </div>
        <div class="inputs five columns omega">
            <p class='explanation'>Why you would like to contribute content from your site to the Commons.</p>
            <div class="input-block">
            <?php echo $this->formTextarea('join_reason', get_option('commons_join_reason'), array('rows'=>10)); ?>
            </div>
        </div>
    </div>
</section>
    <section class="three columns omega">
        <div id="save" class="panel">
            <?php if($key = get_option('commons_key')): ?>
            <input type='hidden' name='api_key' value='<?php echo $key; ?>' />
            <input id="submit" class="big green button" type="submit" value="Update" name="submit" />
            <?php else:?>
            <input id="submit" class="big green button" type="submit" value="Apply" name="submit" />
            <?php endif; ?>
        </div>
    </section>    
</form>
</div>

<?php 
echo foot();
?>