<?php

namespace Legacy\Api;
//use Legacy\Config;
use Legacy\Helper;
use Bitrix\Main\UserTable;

class Room {
    public static function GetRoom($arRequest) {
        $roomID = $arRequest['room_id'];

        return Helper::GetResponseApi(200, [
            'room_info' => self::GetRoomInfo($roomID)
        ]);
    }

    public static function GetRoomInfo($roomID) {
        global $USER;

        $arFilter = Array('IBLOCK_ID'=> \Legacy\Config::Rooms,
            'ACTIVE'=>'Y',
            'ID'=>$roomID,
        );

        $arSelect = [
            'ID',
            'NAME',
            'PROPERTY_TYPE',
            'PROPERTY_OFFICE',
            'PROPERTY_ROOMINESS'
        ];

        $res = \CIBlockElement::GetList('ASC', $arFilter, false, false, $arSelect);
        $arResult = [];

        while($item = $res->Fetch()){
            foreach($item as $key => $value){
                if(strripos($key,'VALUE_ID')){
                    unset($item[$key]);
                    continue;
                }
                if(strripos($key,'PROPERTY') !== false){
                    $old_key = $key;
                    $key = str_replace(['PROPERTY_','_VALUE','~'], '', $key);
                    $item[$key] = $value;
                    unset($item[$old_key]);
                }
            }

            $arResult[] = [
                'ID' => $item['ID'],
                'name' => $item['NAME'],
                'type' => $item['TYPE'],
                'office' => $item['OFFICE'],
                'roominess' => $item['ROOMINESS']
            ];
        }

        if (isset($arResult[0])) {
            return $arResult[0];
        } else{
            return null;
        }
    }

    public static function GetRoomsInfo($IDs = []) {

        $arFilter = Array('IBLOCK_ID'=> \Legacy\Config::Rooms,
            'ACTIVE'=>'Y',
            'ID' => $IDs
        );

        $arSelect = [
            'ID',
            'NAME',
            'PROPERTY_TYPE',
            'PROPERTY_OFFICE',
            'PROPERTY_ROOMINESS'
        ];

        $res = \CIBlockElement::GetList('ASC', $arFilter, false, false, $arSelect);
        $arResult = [];

        while($item = $res->Fetch()){
            foreach($item as $key => $value){
                if(strripos($key,'VALUE_ID')){
                    unset($item[$key]);
                    continue;
                }
                if(strripos($key,'PROPERTY') !== false){
                    $old_key = $key;
                    $key = str_replace(['PROPERTY_','_VALUE','~'], '', $key);
                    $item[$key] = $value;
                    unset($item[$old_key]);
                }
            }

            $arResult[] = [
                'ID' => $item['ID'],
                'name' => $item['NAME'],
                'type' => $item['TYPE'],
                'office' => $item['OFFICE'],
                'roominess' => $item['ROOMINESS']
            ];
        }
        return Helper::GetResponseApi(200, ['rooms_info' => $arResult]);
    }


//
//    public static function AddRoom($arRequest){
//
//    }
//
//    public static function UpdateRoom() {
//
//    }
//
//    public static function DeleteRoom() {
//
//    }
}