<?php

namespace Legacy\Api;
use Legacy\Helper;
use Bitrix\Main\UserTable;

class Company {
    public static function AddCompany($arRequest) {
        $name = $arRequest['name'];
        $address = $arRequest['address'];
        $phone = $arRequest['phone'];
        $email = $arRequest['email'];
        $id_crm = $arRequest['id_crm'];

        $arLoadProperties = Array(
            'NAME' => $name,
            'IBLOCK_ID' => \Legacy\Config::Company,
            'ACTIVE' =>"Y",
            'PROPERTY_VALUES' => [
                'ADDRESS' => $address,
                'PHONE' => $phone,
                'EMAIL' => $email,
                'ID_CRM' => $id_crm,
            ]
        );

        $companyRes = self::GetCompanyBefore($name, $id_crm);
        if(!$companyRes) {
            $el = new \CIBlockElement;
            if($res = $el->Add($arLoadProperties)){
                return $res;
            } else{
                return Helper::GetResponseApi(404, [], 'Ошибка добавления:' . $res->LAST_ERROR);
            }
        } else {
            return $companyRes;
        }

    }

    public static function GetCompanyBefore($id_crm) {
        $arFilter = Array('IBLOCK_ID'=> \Legacy\Config::Company,
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