<?php

declare(strict_types=1);

/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*************************************************************************************/

namespace WireTransfer\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use WireTransfer\WireTransfer;

/**
 * Keeps, in the buyer's session, the order they just placed by wire transfer, so the order
 * confirmation page can show the bank details to transfer to. Any other placement forgets it:
 * the confirmation page of a later order paid by another means must not show them.
 */
final readonly class RememberPlacedOrder implements EventSubscriberInterface
{
    public const SESSION_KEY = 'wiretransfer.placed_order_id';

    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    public function rememberPlacedOrder(OrderEvent $event): void
    {
        $request = $this->requestStack->getMainRequest();

        if (null === $request || !$request->hasSession()) {
            return;
        }

        $order = $event->getOrder();

        if ($order->getPaymentModuleId() === WireTransfer::getModuleId()) {
            $request->getSession()->set(self::SESSION_KEY, $order->getId());

            return;
        }

        $request->getSession()->remove(self::SESSION_KEY);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TheliaEvents::ORDER_BEFORE_PAYMENT => ['rememberPlacedOrder', 64],
        ];
    }
}
