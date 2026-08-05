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

namespace WireTransfer\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use WireTransfer\Service\WireTransferBankInfoRenderer;

/**
 * Exposes the WireTransfer bank details to the Flexy theme, which has no front-office hook to
 * render them into. Call it on the order confirmation page, e.g.
 * {{ wiretransfer_bank_info(order_id) }}.
 */
final class WireTransferExtension extends AbstractExtension
{
    public function __construct(
        private readonly WireTransferBankInfoRenderer $renderer,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('wiretransfer_bank_info', $this->renderer->bankInfo(...), ['is_safe' => ['html']]),
        ];
    }
}
