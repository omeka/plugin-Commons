<?php
$head = array('title' => 'Omeka Commons Branding', 'content_class' => 'horizontal-nav');
head($head);


?>
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

<div id='primary'>
<?php echo flash(); ?>
<div class="pagination"><?php echo pagination_links(); ?></div>
<form method="post" >

<style type="text/css">
input#commons-submit {
    float:none;
}

label#commons-check-label, label#commons-delete-all-label {
    display: inline;
    margin-right: 10px;
    float:none;
}
</style>


<input id='commons-submit' type="submit" class="delete" name="submit" value="Submit" />
<div id="commons-check-all-div">    
    <input type="checkbox" id="commons-check-all"  />
    <label id='commons-check-label' for="check-all">Check all on this page for deletion</label>
    <input type="checkbox" id="commons-delete-all" name='commons-delete-all' />
    <label id="commons-delete-all-label" for="commons-delete-all">Delete ALL the records on all pages</label>
    <p>Deleting this information does not affect your items or the Commons in any way. It just erases the record of the transfer of data to the Commons.</p>
</div>

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
    <input type="checkbox" class='commons-batch-select' name="commons-delete[]" value="<?php echo $record->id; ?>" />
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

</form>
</div>




<?php foot();?>