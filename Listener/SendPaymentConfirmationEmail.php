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

namespace WireTransfer\Listener;

use WireTransfer\WireTransfer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Action\BaseAction;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Mailer\MailerFactory;
use Thelia\Core\Template\ParserInterface;
use Thelia\Model\ConfigQuery;
use Thelia\Model\MessageQuery;
/**
 * Class SendEMail
 * @package IciRelais\Listener
 * @author Thelia <info@thelia.net>
 */
class SendPaymentConfirmationEmail extends BaseAction implements EventSubscriberInterface
{

    /**
     * @var MailerFactory
     */
    protected $mailer;
    /**
     * @var ParserInterface
     */
    protected $parser;

    public function __construct(ParserInterface $parser,MailerFactory $mailer)
    {
        $this->parser = $parser;
        $this->mailer = $mailer;
    }

    /**
     * @return \Thelia\Mailer\MailerFactory
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    /*
     * @params OrderEvent $order
     *
     * Checks if order delivery module is icirelais and if order new status is sent, send an email to the customer.
     */
    public function sendConfirmationEmail(OrderEvent $event)
    {
        if ($event->getOrder()->getPaymentModuleId() === WireTransfer::getModuleId()) {

            if ($event->getOrder()->isPaid()) {
                $contact_email = ConfigQuery::getStoreEmail();

                if ($contact_email) {
                    $message = MessageQuery::create()
                        ->filterByName('order_confirmation_wiretransfer')
                        ->findOne();

                    if (false === $message) {
                        throw new \Exception("Failed to load message 'order_confirmation_wiretransfer'.");
                    }

                    $order = $event->getOrder();
                    $customer = $order->getCustomer();

                    $this->parser->assign('order_id', $order->getId());
                    $this->parser->assign('order_ref', $order->getRef());

                    $message
                        ->setLocale($order->getLang()->getLocale());

                    $instance = \Swift_Message::newInstance()
                        ->addTo($customer->getEmail(), $customer->getFirstname()." ".$customer->getLastname())
                        ->addFrom($contact_email, ConfigQuery::getStoreName())
                    ;

                    // Build subject and body
                    $message->buildMessage($this->parser, $instance);

                    $this->getMailer()->send($instance);
                }
            }
        }

    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            TheliaEvents::ORDER_UPDATE_STATUS => array("sendConfirmationEmail", 128)
        );
    }

}
