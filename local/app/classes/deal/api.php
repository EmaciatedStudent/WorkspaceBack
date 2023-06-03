<?php

namespace Legacy\Api;
use Legacy\Helper;
use Bitrix\Main\UserTable;

class Deal {
    public static function AddDeal($arRequest) {
        $name = $arRequest['name'];
        $hours_count = $arRequest['hours_count'];
        $company = $arRequest['company'];
        $id_crm = $arRequest['id_crm'];

        $arLoadProperties = Array(
            'NAME' => $name,
            'IBLOCK_ID' => \Legacy\Config::Deal,
            'ACTIVE' =>"Y",
            'PROPERTY_VALUES' => [
                'HOURS_COUNT' => $hours_count,
                'COMPANY' => $company,
                'ID_CRM' => $id_crm,
            ]
        );

        $dealRes = self::GetDealBefore($id_crm);
        if(!$dealRes) {
            $el = new \CIBlockElement;
            if($res = $el->Add($arLoadProperties)){
                return $res;
            } else{
                return Helper::GetResponseApi(404, [], 'Ошибка добавления:' . $res->LAST_ERROR);
            }
        } else {
            return $dealRes;
        }
    }

    public static function GetDealBefore($id_crm) {
        $arFilter = Array('IBLOCK_ID'=> \Legacy\Config::Deal,
            'ACTIVE' => 'Y',
            'PROPERTY_ID_CRM' => $id_crm
        );

        $arSelect = ['ID'];

        $res = \CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false, false, $arSelect);

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

            $arResult[] = $item['ID'];
        }

        return count($arResult) > 0 ? $arResult[0] : false;
    }
}