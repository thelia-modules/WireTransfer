<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/

namespace WireTransfer;

use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Translation\Translator;
use Thelia\Install\Database;
use Thelia\Log\Tlog;
use Thelia\Model\MessageQuery;
use Thelia\Model\ModuleImageQuery;
use Thelia\Model\ModuleQuery;
use Thelia\Model\Order;
use Thelia\Module\BaseModule;
use Thelia\Module\PaymentModuleInterface;
use Thelia\Tools\URL;

/**
 * Class WireTransfer
 * @package WireTransfer
 * author Thelia <info@thelia.net>
 */
class WireTransfer extends BaseModule implements PaymentModuleInterface
{
    const MESSAGE_DOMAIN = 'wiretransfer';

    /**
     * @param Order $order
     */
    public function pay(Order $order)
    {
        // Nothing special to do.
    }

    /**
     * @return boolean true if all parameters have been entered.
     */
    public function isValidPayment()
    {
        // Check that all parameters have been entered.
        $valid =
            $this->getConfigValue('name', '') != ''
            &&
            $this->getConfigValue('bic', '') != ''
            &&
            $this->getConfigValue('iban', '') != ''
        ;

        if (! $valid) {
            Tlog::getInstance()->addError(
                Translator::getInstance()->trans(
                    "Bank information parameters have not been defined.", [], self::MESSAGE_DOMAIN
                )
            );
        }

        return $valid;
    }

    public function install(ConnectionInterface $con = null)
    {
        $database = new Database($con->getWrappedConnection());

        // Insert email message
        $database->insertSql(null, array(__DIR__ . "/Config/setup.sql"));

        /* insert the images from image folder if not already done */
        $moduleModel = $this->getModuleModel();

        if (! $moduleModel->isModuleImageDeployed($con)) {
            $this->deployImageFolder($moduleModel, sprintf('%s/images', __DIR__), $con);
        }
    }

    public function destroy(ConnectionInterface $con = null, $deleteModuleData = false)
    {
        // Delete our message
        if (null !== $message = MessageQuery::create()->findOneByName('order_confirmation_wiretransfer')) {
            $message->delete($con);
        }

        parent::destroy($con, $deleteModuleData);
    }
}