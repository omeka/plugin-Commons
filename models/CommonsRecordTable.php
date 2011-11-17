<?php

class CommonsRecordTable extends Omeka_Db_Table
{
    protected $_alias = 'crt';
    
    
    public function init()
    {
        $this->_modelClass = 'CommonsRecord';
    }
    
    public function applySearchFilters($select, $params)
    {
        $select->where('record_type = ?', $params['record_type']);
        $select->where('record_id = ?' , $params['record_id']);
        return $select;
    }
    
}