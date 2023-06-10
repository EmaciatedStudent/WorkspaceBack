<?php

namespace Legacy\Api;
use Legacy\Helper;
use Bitrix\Main\UserTable;

class Deal {
    public static function GetExecutingDealsInfo() {
        $arFilter = Array('IBLOCK_ID'=> \Legacy\Config::Deal,
            'ACTIVE'=>'Y',
            'PROPERTY_STATUS'=>'EXECUTING',
        );

        $arSelect = [
            'ID',
            'NAME',
            'PROPERTY_COMPANY',
            'PROPERTY_HOURS_COUNT',
            'PROPERTY_ID_CRM',
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
                'deal_id' => $item['ID'],
                'name' => $item['NAME'],
                'company' => $item['COMPANY'],
                'hours_count' => $item['HOURS_COUNT'],
                'id_crm' => $item['ID_CRM'],
            ];
        }

        return Helper::GetResponseApi(200, ['deal' => $arResult[0]]);
    }

    public static function GetCompanyDeal() {
        global $USER;

        $user = User::GetUserInfo($USER->GetID());

        $arFilter = Array('IBLOCK_ID'=> \Legacy\Config::Deal,
            'ACTIVE'=>'Y',
            'PROPERTY_COMPANY'=>$user['company_id'],
        );

        $arSelect = [
            'ID',
            'NAME',
            'PROPERTY_COMPANY',
            'PROPERTY_HOURS_COUNT',
            'PROPERTY_EXTRA_HOURS',
            'PROPERTY_ID_CRM',
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
                'deal_id' => $item['ID'],
                'name' => $item['NAME'],
                'company' => $item['COMPANY'],
                'hours_count' => $item['HOURS_COUNT'] + $item['EXTRA_HOURS'],
                'bookings_count' => Booking::GetCompanyBookingsCount($companyID),
                'id_crm' => $item['ID_CRM'],
            ];
        }

        return Helper::GetResponseApi(200, ['deal' => $arResult[0]]);
    }

    public static function AddExtraHours($arRequest) {
        $id = $arRequest['deal_id'];
        $extra_hours = $arRequest['extra_hours'];

        $arFields = Array(
            'PROPERTY_EXTRA_HOURS' => $extra_hours
        );

        $el = new \CIBlockElement;
        if ($res = $el->Update($id, $arFields)) {
            return Helper::GetResponseApi(200, ['deal_id' => $res]);
        } else {
            return Helper::GetResponseApi(404, [], 'Ошибка добавления:' . $res->LAST_ERROR);
        }
    }

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