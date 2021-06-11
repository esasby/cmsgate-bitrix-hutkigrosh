<?php
/**
 * Используется встроенный механизм локализации bitrix, т.к. при создании ModuleDescriptor нельзя использовать
 * Registry->getTranslator (возникает бесконечная рекурсия)
 */
use esas\cmsgate\view\admin\AdminViewFields;

$MESS['hutkigrosh_payment_method_name'] = "Прием платежей через ЕРИП (ХуткiГрош)";
$MESS['hutkigrosh_payment_method_description'] = "Выставление пользовательских счетов в ЕРИП";