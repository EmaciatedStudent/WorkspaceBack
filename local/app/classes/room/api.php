<?php

namespace Legacy\Api;
use Legacy\Config;
use Legacy\Helper;
use Bitrix\Main\UserTable;

class Room {
    public static function GetRoom($arRequest) {
        $roomID = $arRequest['room_id'];

        $res = self::GetRoomInfo($roomID);

        return Helper::GetResponseApi(200, [
            'room_info' => $res
        ]);
    }

    public static function GetRoomInfo($roomID) {
        $arFilter = Array('IBLOCK_ID'=> \Legacy\Config::Rooms,
            'ACTIVE'=>'Y',
            'ID'=>$roomID,
        );

        $arSelect = [
            'ID',
            'NAME',
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
                'office' => $item['OFFICE'],
                'roominess' => $item['ROOMINESS']
            ];
        }
        return Helper::GetResponseApi(200, ['rooms_info' => $arResult]);
    }

    public static function AddRoom($arRequest){
        $name = $arRequest['name'];
        $office = $arRequest['office'];
        $roominess = $arRequest['roominess'];

        $arLoadProperties = Array(
            'NAME' => $name,
            'IBLOCK_ID' => \Legacy\Config::Rooms,
            'ACTIVE' =>"Y",
            'PROPERTY_VALUES' => [
                'OFFICE' => $office,
                'ROOMINESS' => $roominess,
            ]
        );

        $el = new \CIBlockElement;
        if($res = $el->Add($arLoadProperties)){
            return Helper::GetResponseApi(200, ['room_id' => $res]);
        } else{
            return Helper::GetResponseApi(404, [], 'Ошибка добавления:' . $res->LAST_ERROR);
        }
    }

    public static function UpdateRoom($arRequest) {
        $id = $arRequest['room_id'];
        $name = $arRequest['name'];
        $office = $arRequest['office'];
        $roominess = $arRequest['roominess'];

        $arFields = Array(
            'NAME' => $name,
            'PROPERTY_OFFICE' => $office,
            'PROPERTY_ROOMINESS' => $roominess
        );

        $el = new \CIBlockElement;
        if ($res = $el->Update($id, $arFields)) {
            return Helper::GetResponseApi(200, ['room_id' => $res]);
        } else {
            return Helper::GetResponseApi(404, [], 'Ошибка добавления:' . $res->LAST_ERROR);
        }
    }

    public static function DeleteRoom($arRequest) {
        $id = $arRequest['room_id'];

        $arFields = Array(
            'ACTIVE' => "N"
        );

        $el = new \CIBlockElement;
        if ($res = $el->Update($id, $arFields)) {
            return Helper::GetResponseApi(200, ['room_id' => $res]);
        } else {
            return Helper::GetResponseApi(404, [], 'Ошибка добавления:' . $res->LAST_ERROR);
        }
    }
}