<?php
$head = array('title' => 'Omeka Commons Branding', 'content_class' => 'horizontal-nav');
head($head);


?>
<ul id="section-nav" class="navigation">

    <li class="">
        <a href="<?php echo uri('commons/index/config'); ?>">Commons branding options</a>
    </li>
    <li class="">
        <a href="<?php echo uri('commons/index/browse'); ?>">Status and Overview of items in Omeka Commons</a>
    </li>


</ul>
<div id='primary'>

<?php while(loop_records('commons_records', $commons_records, 'commons_set_current_record')):  ?>
<div>
<?php $record = commons_get_current_record(); ?>
<h2><?php echo $record->recordLabel(); ?></a></h2>
<p>Status: <?php echo $record->status;  ?></p>
</div>
<?php endwhile; ?>



</div>




<?php foot();?>