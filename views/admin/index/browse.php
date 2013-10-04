<?php
$head = array('title' => 'Omeka Commons', 'content_class' => 'horizontal-nav');
echo head($head);


?>
<div id='primary'>
<?php include('admin-nav.php'); ?>
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
<section class="ten columns alpha">

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
    <?php foreach(loop('commons_records', $commons_records) as $commonsrecord):  ?>
    <tr>
    <?php $record = get_current_record('commons_records'); ?>
    <td>
        <span class="title"><?php echo metadata($record, 'label'); ?></span>
        <ul class="action-links group">
            <li class="details-link">
                <a href="<?php echo record_url($record->Record, 'edit'); ?>">Local page</a>    
            </li>
            <?php if($record->type == 'Item'): ?>
                <li>
                <a href="<?php echo COMMONS_BASE_URL . '/items/show/' . $record->commons_id; ?>">Commons page</a>
                </li>
            <?php endif; ?>
        </ul>

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
    <?php endforeach; ?>
    </tbody>
    </table>
</section>


</form>
</div>




<?php echo foot();?>