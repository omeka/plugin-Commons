<?php
head();

?>

<div id='primary'>

<?php foreach($commons_records as $record):  ?>
<div class='commons_record'>
<h2><a href='<?php echo record_uri($record->Record, 'show'); ?>'><?php echo $record->recordLabel(); ?></a></h2>
<p><?php echo cc_license_link($record->license); ?></p>
</div>

<?php endforeach; ?>



</div>




<?php foot();?>