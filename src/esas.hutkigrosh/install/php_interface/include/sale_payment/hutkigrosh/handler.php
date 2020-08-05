<?

namespace Sale\Handlers\PaySystem;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/php_interface/include/sale_payment/hutkigrosh/init.php");

use Bitrix\Main\Error;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Request;
use Bitrix\Sale\Payment;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\PaySystem\ServiceResult;
use CSaleOrder;
use Bitrix\Main\Type\DateTime;
use esas\cmsgate\bitrix\CmsgateServiceHandler;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshAddBill;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshCompletionPage;
use esas\cmsgate\hutkigrosh\RegistryHutkigrosh;
use esas\cmsgate\hutkigrosh\utils\RequestParamsHutkigrosh;
use esas\cmsgate\Registry;
use esas\cmsgate\utils\Logger;
use esas\controllers\hutkigrosh\ControllerHutkigroshNotifyBitrix;
use Exception;
use Throwable;

class HutkigroshHandler extends CmsgateServiceHandler
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
    public function getPaymentIdFromRequest(Request $request)
    {
        $dbOrder = CSaleOrder::GetList(
            array("DATE_UPDATE" => "DESC"),
            array(
                "COMMENTS" => $request->get(RequestParamsHutkigrosh::PURCHASE_ID)
            )
        );
        /** @var TYPE_NAME $arOrder */
        $arOrder = $dbOrder->GetNext();

        $dbPayment = \Bitrix\Sale\PaymentCollection::getList([
            'select' => ['ID'],
            'filter' => [
                '=ORDER_ID' => $arOrder["ID"],
            ]
        ]);
        while ($item = $dbPayment->fetch())
        {
            return $item["ID"];
        }
        return ""; //check
    }

    /**
     * @param Payment $payment
     * @param Request $request
     * @return PaySystem\ServiceResult
     * @throws Exception
     */
    public function processRequest(Payment $payment, Request $request)
    {
        $result = new PaySystem\ServiceResult();

        try {
            $controller = new ControllerHutkigroshNotifyBitrix();
            $billInfoRs = $controller->process();
            if ($billInfoRs != null) {
                $fields = array(
                    "PS_STATUS" => "Y",
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
        } catch (Throwable $e) {
            Logger::getLogger("notify")->error("Exception:", $e);
            $result->addError(new Error($e->getMessage()));
        }

        return $result;
    }

    public function sendResponse(PaySystem\ServiceResult $result, Request $request)
    {
    }
}