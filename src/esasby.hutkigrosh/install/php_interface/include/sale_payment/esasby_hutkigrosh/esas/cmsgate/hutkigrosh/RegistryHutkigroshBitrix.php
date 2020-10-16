<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 01.10.2018
 * Time: 12:05
 */

namespace esas\cmsgate\hutkigrosh;

use Bitrix\Main\Localization\Loc;
use esas\cmsgate\CmsConnectorBitrix;
use esas\cmsgate\descriptors\ModuleDescriptor;
use esas\cmsgate\descriptors\VendorDescriptor;
use esas\cmsgate\descriptors\VersionDescriptor;
use esas\cmsgate\hutkigrosh\view\client\CompletionPanelHutkigroshBitrix;
use esas\cmsgate\Registry;
use esas\cmsgate\view\admin\AdminViewFields;
use CMain;
use COption;
use esas\cmsgate\view\admin\ConfigFormBitrix;
Loc::loadMessages(__FILE__);

class RegistryHutkigroshBitrix extends RegistryHutkigrosh
{
    public function __construct()
    {
        $this->cmsConnector = new CmsConnectorBitrix();
        $this->paysystemConnector = new PaysystemConnectorHutkigrosh();
    }


    /**
     * Переопделение для упрощения типизации
     * @return RegistryHutkigroshBitrix
     */
    public static function getRegistry()
    {
        return parent::getRegistry();
    }

    /**
     * @throws \Exception
     */
    public function createConfigForm()
    {
        $managedFields = $this->getManagedFieldsFactory()->getManagedFieldsExcept(AdminViewFields::CONFIG_FORM_COMMON,
            [
                ConfigFieldsHutkigrosh::shopName(),
                ConfigFieldsHutkigrosh::paymentMethodName(),
                ConfigFieldsHutkigrosh::paymentMethodDetails()
            ]);
        $configForm = new ConfigFormBitrix(
            AdminViewFields::CONFIG_FORM_COMMON,
            $managedFields);
        return $configForm;
    }


    function getUrlAlfaclick($orderId)
    {
        return
            "/bitrix/tools/sale_ps_hutkigrosh_ajax.php";
    }

    function getUrlWebpay($orderId)
    {
        global $APPLICATION;
        return (CMain::IsHTTPS() ? "https" : "http")
            . "://"
            . ((defined("SITE_SERVER_NAME") && strlen(SITE_SERVER_NAME) > 0) ? SITE_SERVER_NAME : COption::GetOptionString("main", "server_name", "")) . $APPLICATION->GetCurUri();
    }

    public function createModuleDescriptor()
    {
        return new ModuleDescriptor(
            "esasby.hutkigrosh", // код должен совпадать с кодом решения в маркете
            new VersionDescriptor("3.14.0", "2020-10-16"),
            Loc::getMessage(AdminViewFields::ADMIN_PAYMENT_METHOD_NAME),
            "https://bitbucket.org/esasby/cmsgate-bitrix-hutkigrosh/src/master/",
            VendorDescriptor::esas(),
            Loc::getMessage(AdminViewFields::ADMIN_PAYMENT_METHOD_DESCRIPTION)
        );
    }

    public function getCompletionPanel($orderWrapper)
    {
        return new CompletionPanelHutkigroshBitrix($orderWrapper);
    }


}