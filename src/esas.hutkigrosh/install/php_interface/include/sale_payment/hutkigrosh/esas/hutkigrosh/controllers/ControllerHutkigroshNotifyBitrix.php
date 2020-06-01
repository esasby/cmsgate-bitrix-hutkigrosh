<?php

namespace esas\hutkigrosh\controllers;

use CSaleOrder;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshNotify;

class ControllerHutkigroshNotifyBitrix extends ControllerHutkigroshNotify
{
    public function onStatusPayed()
    {
        parent::onStatusPayed();
        CSaleOrder::Update($this->localOrderWrapper->getOrderId(), array("PAYED" => "Y"));
        CSaleOrder::PayOrder($this->localOrderWrapper->getOrderId(), "Y");
    }

}