<?php

namespace Legacy\Api;
use Legacy\Helper;
use Legacy\Api\User;
use Bitrix\Main\UserTable;

class CRM {
    public static function GetDealInfo($dealID) {
        $res = Helper::CurlCRM("crm.deal.get",
            array(
                "id" => $dealID
            )
        )["result"];


        return $res;
    }

    public static function GetCompanyInfo($dealID) {
        $dealInfo = self::GetDealInfo($dealID);
        $companyID = $dealInfo["COMPANY_ID"];


        $res = Helper::CurlCRM("crm.company.get",
            array(
                "id" => $companyID
            )
        )["result"];

        return $res;
    }

    public static function GetProductsInfo($dealID) {
        $res = Helper::CurlCRM("crm.deal.productrows.get",
            array(
                "id" => $dealID
            )
        )["result"];

        return $res;
    }

    public static function CreateInvoice($dealID) {
        $dealID = 13627;

        $dealInfo = self::GetDealInfo($dealID);
        $companyID = $dealInfo["COMPANY_ID"];

        $res = Helper::CurlCRM("crm.invoice.add",
            array(
                "fields" => array(
                    "UF_DEAL_ID" => $dealID,
                    "UF_COMPANY_ID" => $companyID,
                    "UF_MYCOMPANY_ID" => 292,
                    "PAY_SYSTEM_ID" => 2,
                    "PERSON_TYPE_ID" => 3,
                    "STATUS_ID" => "N",
//                    "PRICE" => ,
                    "CURRENCY" => "RUB",
                    "RESPONSIBLE_ID" => 865,
                    "ORDER_TOPIC" => "Сделка #" . $dealID,
                    "PRODUCT_ROWS" => [
                        array(
                            "ID" => 0,
                            "PRODUCT_ID" => 0,
                            "QUANTITY" => 3,
                            "PRICE" => 1,
                            "PRODUCT_NAME" => "тест_добавлениеСчета",
                            "MEASURE_CODE" => 11,
                        )
                    ]
                )
            )
        )["result"];

        return Helper::GetResponseApi(200, [
            'invoiceID' => $res
        ]);
    }

    public static function GetInvoiceInfo($invoiceID) {
        $invoiceID = 1493;

        $res = Helper::CurlCRM("crm.invoice.get",
            array(
                "id" => $invoiceID
            )
        )["result"];

//        return $res;
        return Helper::GetResponseApi(200, [
            'invoiceInfo' => $res
        ]);
    }

    public static function GetInvoiceLink($invoiceID) {
        $invoiceID = 1493;

        $res = Helper::CurlCRM("crm.invoice.getexternallink",
            array(
                "id" => $invoiceID
            )
        )["result"];

//        return $res;
        return Helper::GetResponseApi(200, [
            'invoiceLink' => $res
        ]);
    }

    public static function CreateAct($invoiceID) {
        $invoiceID = 1501;

        $res = Helper::CurlCRM("crm.documentgenerator.document.add",
            array(
                "templateId" => 2,
                "entityTypeId" => 5,
                "entityId" => $invoiceID,
            )
        )["result"];

//        return $res;
        return Helper::GetResponseApi(200, [
            'actInfo' => $res
        ]);
    }

    // удалить
    public static function GetActInfo($actID) {
        $actID = 369;

        $res = Helper::CurlCRM("crm.documentgenerator.document.get",
            array(
                "id" => $actID
            )
        )["result"];

//        return $res;
        return Helper::GetResponseApi(200, [
            'actInfo' => $res
        ]);
    }
}