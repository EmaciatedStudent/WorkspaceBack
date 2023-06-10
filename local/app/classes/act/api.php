<?php

namespace Legacy\Api;
use Legacy\Config;
use Legacy\Helper;
use Bitrix\Main\UserTable;

class Act {
    public static function GetActInfo($arRequest) {
        $dealID = $arRequest['deal_id'];

        $arFilter = Array('IBLOCK_ID'=> \Legacy\Config::Act,
            'ACTIVE'=>'Y',
            'PROPERTY_DEAL' => $dealID
        );

        $arSelect = [
            'ID',
            'NAME',
            'PROPERTY_DEAL',
            'PROPERTY_DATE_INSERT',
            'PROPERTY_LINK'
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
                'id' => $item['ID'],
                'name' => $item['NAME'],
                'deal' => $item['DEAL'],
                'date_insert' => $item['DATE_INSERT'],
                'link' => $item['LINK']
            ];
        }
        return Helper::GetResponseApi(200, ['act_info' => $arResult]);
    }
}