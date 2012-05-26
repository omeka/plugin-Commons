<?php
$head = array('title' => 'Omeka Commons Branding', 'content_class' => 'horizontal-nav');
head($head);


?>
<ul id="section-nav" class="navigation">

    <li class="">
        <a href="<?php echo uri('commons/index/browse'); ?>">Status and Overview of items in Omeka Commons</a>
    </li>
    <li class="">
        <a href="<?php echo uri('commons/index/config'); ?>">Commons branding options</a>
    </li>



</ul>
<div id='primary'>
<?php echo flash(); ?>
<table>
<thead>
<tr>
    <th>Record</th>
    <th>Type</th>
    <th>Last Update to Commons</th>
    <th>Status</th>
    <th>Status Details</th>
    <th>Collection/Site Export Info</th>
</tr>
<tbody>
<?php while(loop_records('commons_records', $commonsrecords, 'commons_set_current_record')):  ?>
<tr>
<?php $record = commons_get_current_record(); ?>
<td>

    <a><?php echo $record->recordLabel(); ?></a>
    <br/>
    <a href="<?php echo commons_source_record_uri($record, 'edit'); ?>">Local page</a>
    <br/>
    <?php if($record->type == 'Item'): ?>
        <a href="<?php echo COMMONS_BASE_URL . '/items/show/' . $record->commons_id; ?>">Commons page</a>
    <?php endif; ?>
</td>
<td><?php echo $record->record_type; ?></td>
<td><?php echo $record->last_export;  ?></td>
<td><?php echo $record->status;  ?></td>
<td><?php echo $record->status_message;  ?></td>
<td>
    <?php if($record->Process): ?>
    <ul>
        <li>Status: <?php echo $record->Process->status; ?></li>
        <li>Started: <?php echo $record->Process->started; ?></li>
        <li>Stopped: <?php echo $record->Process->stopped; ?></li>
    </ul>
    <?php else: ?>
    Individual export
    <?php endif; ?>
</td>

</tr>
<?php endwhile; ?>
</tbody>
</table>


</div>




<?php foot();?>