<?php

namespace Legacy\Api;
use Legacy\Helper;
use Bitrix\Main\UserTable;
use Legacy\Api\CRM;

class Auth {

    public static function Registration($arRequest) {
        global $USER;

        $login = $arRequest['login'];
        $name = $arRequest['name'];
        $lastname = $arRequest['lastname'];
        $password = $arRequest['password'];
        $confirm_password = $arRequest['confirm_password'];
        $email = $arRequest['email'];
        $group = explode(',', $arRequest['group']);

        $deal_id = $arRequest['dealid'];
        $dealInfo = CRM::GetDealInfo($deal_id);
        $companyInfo = CRM::GetCompanyInfo($deal_id);
        $productsInfo = CRM::GetProductsInfo($deal_id);

        $arCompanyFields = Array(
            'name' => $companyInfo['TITLE'],
            'address' => $companyInfo['ADDRESS'],
            'phone' => $companyInfo['PHONE'],
            'email' => $companyInfo['EMAIL'],
            'id_crm' => $companyInfo['ID']
        );
        $company_id = Company::AddCompany($arCompanyFields);

        $arDealFields = Array(
            'name' => $dealInfo['TITLE'],
            'date_start' => $dealInfo['DATE_START'],
            'date_end' => $dealInfo['DATE_END'],
            'company' => $company_id,
            'hours_count' => $productsInfo[1]["QUANTITY"],
            'id_crm' => $deal_id
        );
        $deal = Deal::AddDeal($arDealFields);

        $arFields = Array(
            "LOGIN" => $login,
            "NAME" => $name,
            "LAST_NAME" => $lastname,
            "PASSWORD" => $password,
            "CONFIRM_PASSWORD" => $confirm_password,
            "EMAIL" => $email,
            "GROUP_ID" => $group,
            "UF_COMPANY" => $company_id,
            "UF_HOURS_COUNT" => $productsInfo[1]["QUANTITY"]
        );

        if ($USER->Add($arFields)) {
            $arAuthResult = $USER->Login($login, $password);
            if ($USER->IsAuthorized()) {
                return Helper::GetResponseApi(200, [
                    'user' => User::GetUserInfo()
                ]);
            }
            else {
                return Helper::GetResponseApi(400, [], $arAuthResult["MESSAGE"]);
            }
        } else {
            return Helper::GetResponseApi(400,[], $USER->LAST_ERROR);
        }
    }

    public static function Login($arRequest) {
        global $USER;

        $login = $arRequest['login'];
        $password = $arRequest['password'];
        $arAuthResult = $USER->Login($login, $password);

        if ($USER->IsAuthorized()) {
            return Helper::GetResponseApi(200, [
                'user' => User::GetUserInfo()
            ]);
        }
        else {
            return Helper::GetResponseApi(400, [], $arAuthResult["MESSAGE"]);
        }
    }

    public static function Logout() {
        global $USER;
        $USER->Logout();
        return Helper::GetResponseApi(200, []);
    }
}