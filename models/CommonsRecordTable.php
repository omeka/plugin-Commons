<?php

class CommonsRecordTable extends Omeka_Db_Table
{
    public function findByTypeAndId($type, $id)
    {
        $params = array('record_type'=>$type, 'record_id'=>$id);
        $select = $this->getSelectForFindBy($params);
        return $this->fetchObject($select);

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