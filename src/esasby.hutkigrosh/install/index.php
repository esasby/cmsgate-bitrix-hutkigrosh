<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/esasby.hutkigrosh/install/php_interface/include/sale_payment/hutkigrosh/init.php");
use esas\cmsgate\bitrix\CmsgateCModule;

if(class_exists('esas_hutkigrosh')) return;
class esasby_hutkigrosh extends CmsgateCModule
{
    protected function addFilesToInstallList()
    {
        parent::addFilesToInstallList();
        $this->installFilesList[] = "/tools/sale_ps_hutkigrosh_ajax.php";
    }


}
