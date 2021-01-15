<?

namespace Sale\Handlers\PaySystem;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/php_interface/include/sale_payment/esasby_hutkigrosh/init.php");

use Bitrix\Main\Error;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Request;
use Bitrix\Main\Type\DateTime;
use Bitrix\Sale\Payment;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\PaySystem\ServiceResult;
use esas\cmsgate\bitrix\CmsgateServiceHandler;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshAddBill;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshCompletionPage;
use esas\cmsgate\hutkigrosh\protocol\HutkigroshBillInfoRs;
use esas\cmsgate\hutkigrosh\RegistryHutkigrosh;
use esas\cmsgate\Registry;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshNotifyBitrix;
use esas\cmsgate\utils\CMSGateException;
use Exception;
use Throwable;

class esasby_hutkigroshHandler extends CmsgateServiceHandler
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
//    $order = Order::load($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"]);
                $configWrapper = RegistryHutkigrosh::getRegistry()->getConfigWrapper();
                $orderWrapper = Registry::getRegistry()->getOrderWrapperByOrderNumber($payment->getOrderId());
                // проверяем, привязан ли к заказу extId, если да,
                // то счет не выставляем, а просто прорисовываем старницу
                if (empty($orderWrapper->getExtId())) {
                    $controller = new ControllerHutkigroshAddBill();
                    $controller->process($orderWrapper);
                }
                $controller = new ControllerHutkigroshCompletionPage();
                $completionPanel = $controller->process($orderWrapper->getOrderId());
                $extraParams['completionPanel'] = $completionPanel;
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

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPaymentIdFromRequestSafe(Request $request)
    {
        $controller = new ControllerHutkigroshNotifyBitrix();
        $billInfoRs = $controller->process();
        CMSGateException::throwIfNull($billInfoRs, "Hutkigrosh get bill rs is null");
        $_SESSION["bill_info_rs"] = $billInfoRs; // для корректной работы processRequest

        $orderWrapper = Registry::getRegistry()->getOrderWrapperByOrderNumberOrId($billInfoRs->getOrderId());
        if ($orderWrapper != null) {
            return $orderWrapper->getOrderId();
        }
        throw new CMSGateException("Can not find payments for order[" . $billInfoRs->getOrderId() . "]");
    }

    /**
     * @param Payment $payment
     * @param Request $request
     * @return PaySystem\ServiceResult
     * @throws Exception
     */
    public function processRequestSafe(Payment $payment, Request $request)
    {
        $result = new PaySystem\ServiceResult();

        /** @var HutkigroshBillInfoRs $billInfoRs */
        $billInfoRs = $_SESSION["bill_info_rs"];
        CMSGateException::throwIfNull($billInfoRs, "Epos invoice is not loaded");
        if ($billInfoRs != null) {
            $fields = array(
                "PS_STATUS" => $billInfoRs->isStatusPayed() ? "Y" : "N",
                "PS_STATUS_CODE" => $billInfoRs->getResponseCode(),
                "PS_STATUS_DESCRIPTION" => $billInfoRs->getResponseMessage(),
                "PS_STATUS_MESSAGE" => "",
                "PS_SUM" => $billInfoRs->getAmount()->getValue(),
                "PS_CURRENCY" => $billInfoRs->getAmount()->getCurrency(),
                "PS_RESPONSE_DATE" => new DateTime(),
            );
            $result->setPsData($fields);
        }
        $result->setOperationType(PaySystem\ServiceResult::MONEY_COMING);
        return $result;
    }

    public function sendResponse(PaySystem\ServiceResult $result, Request $request)
    {
    }
}