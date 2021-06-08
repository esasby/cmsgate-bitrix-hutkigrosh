<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/esasby.hutkigrosh/install/php_interface/include/sale_payment/esasby_hutkigrosh/init.php");

use esas\cmsgate\bitrix\CmsgatePaysystem;
use esas\cmsgate\bitrix\InstallHelper;
use esas\cmsgate\CmsConnectorBitrix;
use esas\cmsgate\Registry;

if (class_exists('esasby_hutkigrosh')) return;

class esasby_hutkigrosh extends CModule
{
    var $MODULE_PATH;
    var $MODULE_ID = 'esasby.hutkigrosh';
    var $MODULE_VERSION = '';
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_GROUP_RIGHTS = 'Y';
    var $PARTNER_NAME;
    var $PARTNER_URI;
    /**
     * @var \esas\cmsgate\bitrix\CmsgateCModule
     */
    protected $installHelper;

    /**
     * CmsgateCModule constructor.
     */
    public function __construct()
    {
        CModule::IncludeModule("sale");
        $this->installHelper = new InstallHelper($this);
        $this->installHelper->createAndAddMainPaySystem();
        $this->installHelper->addToInstallFilesList("/tools/sale_ps_hutkigrosh_ajax.php");
        $webpayPS = new CmsgatePaysystem();
        $webpayPS
            ->setName("Оплата картой")
            ->setDescription("Онлайн оплата картой Visa, MasterCard, Белкарт")
            ->setType("ORDER")
            ->setActionFile("esasby_webpay");
        $this->installHelper->addToInstallPaySystemsList($webpayPS);

        $this->MODULE_PATH = $_SERVER['DOCUMENT_ROOT'] . '/bitrix' . InstallHelper::MODULE_SUB_PATH . CmsConnectorBitrix::getInstance()->getModuleActionName();
        $this->MODULE_VERSION = Registry::getRegistry()->getModuleDescriptor()->getVersion()->getVersion();
        $this->MODULE_VERSION_DATE = Registry::getRegistry()->getModuleDescriptor()->getVersion()->getDate();
        $this->MODULE_NAME = Registry::getRegistry()->getModuleDescriptor()->getModuleFullName();
        $this->MODULE_DESCRIPTION = Registry::getRegistry()->getModuleDescriptor()->getModuleDescription();
        $this->PARTNER_NAME = Registry::getRegistry()->getModuleDescriptor()->getVendor()->getFullName();
        $this->PARTNER_URI = Registry::getRegistry()->getModuleDescriptor()->getVendor()->getUrl();
    }

    function DoInstall()
    {
        return $this->installHelper->DoInstall();
    }

    function DoUninstall()
    {
        return $this->installHelper->DoUninstall();
    }
}
