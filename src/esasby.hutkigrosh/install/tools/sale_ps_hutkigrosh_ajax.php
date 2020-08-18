<?php
//подключаем только служебную часть пролога (для работы с CModule и CSalePaySystemAction), без визуальной части, чтобы не было вывода ненужного html
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshAlfaclick;
use esas\cmsgate\utils\Logger;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/php_interface/include/sale_payment/hutkigrosh/init.php");

if (!CModule::IncludeModule("sale")) return;

try {
    $controller = new ControllerHutkigroshAlfaclick();
    $controller->process();
} catch (Throwable $e) {
    Logger::getLogger("alfaclick")->error("Exception: ", $e);
}
