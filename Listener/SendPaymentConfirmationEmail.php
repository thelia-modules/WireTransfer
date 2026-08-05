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

declare(strict_types=1);

namespace WireTransfer\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Action\BaseAction;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Mailer\MailerFactory;
use Thelia\Model\ConfigQuery;
use WireTransfer\WireTransfer;

/**
 * Sends the wire-transfer confirmation email once the order is marked as paid.
 *
 * Registered by auto-discovery only (`WireTransfer::configureServices()`). It must NOT also be
 * declared in Config/config.xml: two service ids for one subscriber class means two listeners,
 * and the customer gets the email twice.
 */
class SendPaymentConfirmationEmail extends BaseAction implements EventSubscriberInterface
{
    public function __construct(
        private readonly MailerFactory $mailer,
    ) {
    }

    public function getMailer(): MailerFactory
    {
        return $this->mailer;
    }

    /**
     * Notifies the customer once an order paid by wire transfer reaches the paid status.
     */
    public function sendConfirmationEmail(OrderEvent $event): void
    {
        $order = $event->getOrder();

        if ($order->getPaymentModuleId() !== WireTransfer::getModuleId() || !$order->isPaid()) {
            return;
        }

        // No store email configured means the mailer has no sender to work with.
        if (!ConfigQuery::getStoreEmail()) {
            return;
        }

        $this->getMailer()->sendEmailToCustomer(
            'order_confirmation_wiretransfer',
            $order->getCustomer(),
            [
                'order_id' => $order->getId(),
                'order_ref' => $order->getRef(),
            ]
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TheliaEvents::ORDER_UPDATE_STATUS => ['sendConfirmationEmail', 128],
        ];
    }
}
