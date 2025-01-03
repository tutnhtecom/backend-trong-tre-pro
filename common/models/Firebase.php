<?php


namespace common\models;


class Firebase
{
    public static function getData($dbName, $id){
        $database = \Yii::$app->firebase->getDatabase();
        if(empty($id) || !isset($id)){
            return FALSE;
        }
        if($database->getReference($dbName)->getSnapshot()->hasChild($id))
            return $database->getReference($dbName)->getChild($id)->getValue();
        else
            return FALSE;
    }

    public static function insert($dbName, array $data){
        if(empty($data) || !isset($data)){return FALSE; }
        $database = \Yii::$app->firebase->getDatabase();
        foreach ($data as $key => $value) {
            $database->getReference()->getChild($dbName)->getChild($key)->set($value);
        }

        return TRUE;
    }

    public static function delete($dbName, int $id){
        if(empty($id) || !isset($id)) return FALSE;
        $database = \Yii::$app->firebase->getDatabase();

        if($database->getReference($dbName)->getSnapshot()->hasChild($id)){
            $database->getReference($dbName)->getChild($id)->remove();
            return TRUE;
        }else{
            return FALSE;
        }
    }
}
