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

namespace WireTransfer\Hook\Theme;

use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Core\Hook\Theme\ThemeHookInterface;
use WireTransfer\Listener\RememberPlacedOrder;
use WireTransfer\Service\WireTransferBankInfoRenderer;

/**
 * Shows the bank details on the order confirmation page of a Twig theme (Flexy's
 * order-placed.bottom point), for the order this session has just placed by wire transfer.
 */
final readonly class BankDetailsThemeHook implements ThemeHookInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private WireTransferBankInfoRenderer $renderer,
    ) {
    }

    public function supports(string $hookName): bool
    {
        return 'order-placed.bottom' === $hookName;
    }

    public function render(string $hookName, array $parameters): string
    {
        $request = $this->requestStack->getMainRequest();

        if (null === $request || !$request->hasSession()) {
            return '';
        }

        $orderId = $request->getSession()->get(RememberPlacedOrder::SESSION_KEY);

        return \is_int($orderId) ? $this->renderer->bankInfo($orderId) : '';
    }
}
