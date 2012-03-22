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
<table>
<thead>
<tr><th>Record</th><th>Type</th><th>Commons Page</th><th>Last Update to Commons</th><th>Status</th><th>Status Details</th></tr>
<tbody>
<?php while(loop_records('commons_records', $commons_records, 'commons_set_current_record')):  ?>
<tr>
<?php $record = commons_get_current_record(); ?>
<td><?php echo $record->recordLabel(); ?></a></td>
<td><?php echo $record->record_type; ?></td>
<td><a href="<?php echo COMMONS_BASE_URL . '/items/show/' . $record->commons_item_id; ?>">Go to page</a></td>
<td><?php echo $record->last_export;  ?></td>
<td><?php echo $record->status;  ?></td>
<td><?php echo $record->status_message;  ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>


</div>




<?php foot();?>