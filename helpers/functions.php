<?php

function commons_get_current_record()
{
    if(!$record = __v()->record) {
        throw new Exception(__('A commons record has not been set to be displayed on this theme page! Please see Omeka documentation for details.'));
    }
    return $record;
}

function commons_set_current_record($record)
{
    $view = __v();
    $view->previous_record = $view->record;
    $view->record = $record;

}

function commons_source_record_uri($commonsRecord = null)
{
    if(!$commonsRecord) {
        $commonsRecord = commons_get_current_record();
    }
    //$source = $commonsRecord->getRecord();
    return record_uri($commonsRecord->Record, 'edit');


}