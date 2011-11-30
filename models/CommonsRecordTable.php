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
        if(isset($params['record_id'])) {
            $select->where('record_id = ?' , $params['record_id']);
        }
        if(isset($params['record_type'])) {
            $select->where('record_type = ?' , $params['record_type']);
        }
        return $select;
    }
    
    public function findByTypeAndId($type, $id)
    {
        $params = array('record_type'=>$type, 'record_id'=>$id);
        $select = $this->getSelectForFindBy($params);
        return $this->fetchObject($select);
    
    }
    
    public function findOmekaRecord($type, $id)
    {
        return $this->_db->getTable($type)->find($id);
    }
    
    public function itemPartOfCommonsCollection($item)
    {
        if($item->Collection) {
            $records = $this->findBy(array('record_id'=>$item->Collection->id, 'record_type'=>'Collection'));
            if(empty($records)) {
                return false;
            } else {
                return $records[0];
            }
        }
        return false;
        
    }
    
}