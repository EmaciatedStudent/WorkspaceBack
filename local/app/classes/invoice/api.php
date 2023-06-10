<?php

namespace Legacy\Api;
use Legacy\Config;
use Legacy\Helper;
use Bitrix\Main\UserTable;

class Invoice {
    public static function GetInvoiceInfo($arRequest) {
        $actID = $arRequest['act_id'];

        $arFilter = Array('IBLOCK_ID'=> \Legacy\Config::Invoice,
            'ACTIVE'=>'Y',
            'PROPERTY_ACT' => $actID
        );

        $arSelect = [
            'ID',
            'NAME',
            'PROPERTY_ACT',
            'PROPERTY_DATE_INSERT',
            'PROPERTY_PRICE',
            'PROPERTY_STATUS',
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
                'act' => $item['ACT'],
                'date_insert' => $item['DATE_INSERT'],
                'price' => $item['PRICE'],
                'status' => $item['STATUS'],
                'link' => $item['LINK']
            ];
        }
        return Helper::GetResponseApi(200, ['invoice_info' => $arResult]);
    }
}