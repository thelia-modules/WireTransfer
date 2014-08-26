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
use Thelia\Core\Translation\Translator;
use Thelia\Install\Database;
use Thelia\Log\Tlog;
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
        $router = $this->getContainer()->get('router.wiretransfer');

        $thankYouPageUrl = URL::getInstance()->absoluteUrl(
            $router->generate('wiretransfer.order-placed', ['orderId' => $order->getId()])
        );

        // Redirect to our own route, so that the payment
        return RedirectResponse::create($thankYouPageUrl);
    }

    /**
     * @return boolean true if all parameters have been entered.
     */
    public function isValidPayment()
    {
        // Check that all parameters have been entered.
        $valid =
            WireTransferConfigQuery::read('name', '') != ''
            &&
            WireTransferConfigQuery::read('bic', '') != ''
            &&
            WireTransferConfigQuery::read('iban', '') != ''
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

    /**
     * Return the order payment success page URL
     *
     * @param  int $order_id the order ID
     * @return string the order payment success page URL
     */
    public function getPaymentSuccessPageUrl($order_id)
    {
        $frontOfficeRouter = $this->getContainer()->get('router.front');

        return URL::getInstance()->absoluteUrl(
            $frontOfficeRouter->generate(
                "order.placed",
                array("order_id" => $order_id),
                Router::ABSOLUTE_URL
            )
        );
    }


    /**
     * @param ConnectionInterface $con
     */
    public function postActivation(ConnectionInterface $con = null)
    {
        /* insert the images from image folder if first module activation */
        $module = $this->getModuleModel();
        if (ModuleImageQuery::create()->filterByModule($module)->count() == 0) {
            $this->deployImageFolder($module, sprintf('%s/images', __DIR__), $con);
        }

        $database = new Database($con->getWrappedConnection());

        $database->insertSql(null, array(__DIR__ . "/Config/thelia.sql"));
    }

    public static function getModCode()
    {
        $mod_code = "WireTransfer";

        return ModuleQuery::create()
            ->findOneByCode($mod_code)
            ->getId()
        ;
    }
}
