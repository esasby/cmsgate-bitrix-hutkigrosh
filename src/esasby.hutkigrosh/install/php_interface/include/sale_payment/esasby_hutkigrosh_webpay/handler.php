<?

namespace Sale\Handlers\PaySystem;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/php_interface/include/sale_payment/esasby_hutkigrosh/init.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/php_interface/include/sale_payment/esasby_hutkigrosh/handler.php"); // иначе не находи класс

use Bitrix\Main\Error;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Request;
use Bitrix\Sale\Payment;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\PaySystem\ServiceResult;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshAddBill;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshWebpayForm;
use esas\cmsgate\Registry;
use Throwable;

class esasby_hutkigrosh_webpayHandler extends esasby_hutkigroshHandler
{
    /**
     * @param Payment $payment
     * @param Request|null $request
     * @return PaySystem\ServiceResult
     * @throws \Bitrix\Main\LoaderException
     */
    public function initiatePay(Payment $payment, Request $request = null)
    {
        if (Loader::includeModule(Registry::getRegistry()->getModuleDescriptor()->getModuleMachineName())) {
            try {
                $orderWrapper = Registry::getRegistry()->getOrderWrapperByOrderNumber($payment->getOrderId());
                // проверяем, привязан ли к заказу extId, если да,
                // то счет не выставляем, а просто прорисовываем старницу
                if (empty($orderWrapper->getExtId())) {
                    $controller = new ControllerHutkigroshAddBill();
                    $controller->process($orderWrapper);
                }
                $controller = new ControllerHutkigroshWebpayForm();
                $webpayResp = $controller->process($orderWrapper);
                $extraParams['webpayForm'] = $webpayResp->getHtmlForm();
                $this->setExtraParams($extraParams);
                return $this->showTemplate($payment, 'template');
            } catch (Throwable $e) {
                $this->logger->error("Exception:", $e);
                $result = new ServiceResult();
                $result->addError(new Error($e->getMessage()));
                return $result;
            }
        } else {
            $result = new ServiceResult();
            $result->addError(new Error(Loc::getMessage('SALE_HPS_PAYMENTGATE_MODULE_NOT_FOUND')));
            return $result;
        }
    }
}