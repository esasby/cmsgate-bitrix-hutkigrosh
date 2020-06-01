<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use esas\cmsgate\Registry;
use esas\cmsgate\view\admin\ConfigFormBitrix;

require_once('init.php');
Loc::loadMessages(__FILE__);

// $arPSCorrespondence - старый формат описания настроек, $data - новый
$data = Registry::getRegistry()->getConfigForm()->generate();
$description = ConfigFormBitrix::generateModuleDescription();

