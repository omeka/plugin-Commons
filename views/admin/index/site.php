<?php

if(get_option('commons_key')) {
    $subtitle = __('Update info for the Commons');
} else {
    $subtitle = __('Apply to be part of the Omeka Commons');
}

queue_js_file('commons');
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
            <label for='commons_admin_email'>Email address</label>
        </div>
        <div class="inputs five columns omega">
            <p class='explanation'>This will be used to create an account in the Commons for you to administer you content there.</p>
            <div class="input-block">
            <?php echo $this->formText('commons_admin_email', get_option('commons_admin_email'), array('size'=>'20')); ?>
            </div>
        </div>
    </div>

    <div class='field'>
        <div class="two columns alpha">
            <label for='commons_admin_name'>Name</label>
        </div>

        <div class="inputs five columns omega">
            <p class='explanation'>Your name</p>
            <div class="input-block">
            <?php echo $this->formText('commons_admin_name', get_option('commons_admin_name'), array('size'=>'20')); ?>
            </div>
        </div>
    </div>



    <div class='field'>
        <div class="two columns alpha">
            <label for='commons_affiliation'>Affiliation</label>
        </div>
        <div class="inputs five columns omega">
            <p class='explanation'>Organization with which you are affiliated.</p>
            <div class="input-block">
            <?php echo $this->formText('commons_affiliation', get_option('commons_affiliation'), array('size'=>'42')); ?>
            </div>
        </div>
    </div>

    <div class='field'>
        <div class="two columns alpha">
            <label for='commons_content_summary'>Content summary</label>
        </div>
        <div class="inputs five columns omega">
            <p class='explanation'>A brief summary of the kind of content you will be sending to the Commons.</p>
            <div class="input-block">
            <?php echo $this->formTextarea('commons_content_summary', get_option('commons_content_summary'), array('rows'=>10)); ?>
            </div>
        </div>
    </div>

    <div class='field'>
    <div class="two columns alpha">
        <label for='commons_join_reason'>Reason for contributing</label>
    </div>
        <div class="inputs five columns omega">
            <p class='explanation'>Why you would like to contribute content from your site to the Commons.</p>
            <div class="input-block">
            <?php echo $this->formTextarea('commons_join_reason', get_option('commons_join_reason'), array('rows'=>10)); ?>
            </div>
        </div>
    </div>
    <h2>Site Settings</h2>
    
    <div class='field'>
    <div class="two columns alpha">
        <label for='administrator_email'><?php echo __('Administrator Email'); ?></label>
    </div>
        <div class="inputs five columns omega">
            <?php echo $this->formText('administrator_email', get_option('administrator_email')); ?>
        </div>
    </div>    
        
    <div class='field'>
    <div class="two columns alpha">
        <label for='site_title'><?php echo __('Site Title'); ?></label>
    </div>
        <div class="inputs five columns omega">
            <?php echo $this->formText('site_title', get_option('site_title')); ?>
        </div>
    </div>        
        
    <div class='field'>
    <div class="two columns alpha">
        <label for='description'><?php echo __('Site Description'); ?></label>
    </div>
        <div class="inputs five columns omega">
            <?php echo $this->formTextarea('description', get_option('description'), array('rows'=>10)); ?>
        </div>
    </div>

    <div class='field'>
    <div class="two columns alpha">
        <label for='copyright'><?php echo __('Site Copyright Information'); ?></label>
    </div>
        <div class="inputs five columns omega">
            <?php echo $this->formText('copyright', get_option('copyright')); ?>
        </div>
    </div>    
    
    <div class='field'>
    <div class="two columns alpha">
        <label for='author'><?php echo __('Site Author Information'); ?></label>
    </div>
        <div class="inputs five columns omega">
            <?php echo $this->formText('author', get_option('author')); ?>
        </div>
    </div>    
    
    </section>
    <section class="three columns omega">
        <div id="save" class="panel">
            <?php $tos = get_option('commons_tos'); ?>
            <?php if($key = get_option('commons_key')): ?>
            <input type='hidden' name='api_key' value='<?php echo $key; ?>' />
            <input id="submit" class="big green button" type="submit" value="Update" name="submit" <?php if(! $tos) { echo "disabled='disabled'"; }; ?> />
            <?php else:?>
            <input id="submit" class="big green button" type="submit" value="Apply" name="submit" <?php if(! $tos) { echo "disabled='disabled'"; }; ?> />
            <?php endif; ?>
            <?php if(! $tos): ?>
            <p class="error">You must agree to the Terms of Service before sending data to Omeka Commons.</p>
            <?php endif;?>
            <div class="tos">
                <label for='tos'>Terms and Conditions</label>
                <p class='explanation'>I agree to the <a href="<?php echo url('commons/index/tos'); ?>">Terms and Conditions</a> for contributing to Omeka Commons.</p>
                <?php echo $this->formCheckbox('commons_tos', get_option('commons_tos'), array(), array(1,0)); ?>
            </div>
        </div>
    </section>
</form>
</div>

<?php
echo foot();
?>