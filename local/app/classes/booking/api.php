<?php

namespace Legacy\Api;
use Legacy\Helper;
use Legacy\Api\User;
use Bitrix\Main\UserTable;

class Booking {
    public static function GetTimeIntervals() {
        $arFilter = Array('IBLOCK_ID'=> \Legacy\Config::Intervals,
            'ACTIVE'=>'Y'
        );

        $arSelect = [
            'ID',
            'NAME',
            'PROPERTY_TIME_START',
            'PROPERTY_TIME_END'
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
                'time_start' => $item['TIME_START'],
                'time_end' => $item['TIME_END']
            ];
        }
        return Helper::GetResponseApi(200, ['time_intervals' => $arResult]);
    }

    public static function GetBookingByPeriod($arRequest) {
        $roomID = $arRequest['room_id'];
        $date_start = self::formatDate($arRequest['date_start']);
        $date_end = self::formatDate($arRequest['date_end']);

        $arFilter = Array('IBLOCK_ID'=> \Legacy\Config::Booking,
            'ACTIVE' => 'Y',
            'PROPERTY_ROOM' => $roomID,
            '>=PROPERTY_DATE' => $date_start,
            '<=PROPERTY_DATE' => $date_end
        );

        $arSelect = [
            'ID',
            'NAME',
            'PROPERTY_USER',
            'PROPERTY_ROOM',
            'PROPERTY_DATE',
            'PROPERTY_TIME_START',
            'PROPERTY_TIME_END'
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
                'user' => $item['USER'],
                'room' => $item['ROOM'],
                'date' => $item['DATE'],
                'time_start' => $item['TIME_START'],
                'time_end' => $item['TIME_END']
            ];
        }

        return Helper::GetResponseApi(200, ['bookings' => $arResult]);
    }

    static function formatDate($date) {
        $date = explode(".", $date);

        return $date[2] . "-" . $date[1] . "-" . $date[0];
    }

    public static function AddBooking($arRequest){
        global $USER;

        $name = $arRequest['name'];
        $room = $arRequest['room_id'];
        $date = $arRequest['date'];
        $time_start = $arRequest['time_start'];
        $time_end = $arRequest['time_end'];

        $arLoadProperties = Array(
            'NAME' => $name,
            'IBLOCK_ID' => \Legacy\Config::Booking,
            'ACTIVE' =>"Y",
            'PROPERTY_VALUES' => [
                'USER' => $USER->GetID(),
                'ROOM' => $room,
                'DATE' => $date,
                'TIME_START' => $time_start,
                'TIME_END' => $time_end,
            ]
        );

        $el = new \CIBlockElement;
        if($res = $el->Add($arLoadProperties)){
            return Helper::GetResponseApi(200, ['booking_id' => $res]);
        } else{
            return Helper::GetResponseApi(404, [], 'Ошибка добавления:' . $res->LAST_ERROR);
        }
    }

    public static function DeleteBooking($arRequest) {
        $id = $arRequest['booking_id'];

        $arFields = Array(
            'ACTIVE' => "N"
        );

        $el = new \CIBlockElement;
        if ($res = $el->Update($id, $arFields)) {
            return Helper::GetResponseApi(200, ['booking_id' => $res]);
        } else {
            return Helper::GetResponseApi(404, [], 'Ошибка добавления:' . $res->LAST_ERROR);
        }
    }
}