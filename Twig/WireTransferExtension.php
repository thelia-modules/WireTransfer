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
 * Exposes the WireTransfer bank details to the front-office theme. Call it on the order
 * confirmation page, e.g. {{ wiretransfer_bank_info(order_id) }}.
 *
 * NOTE: a theme template must not call this directly — a module Twig function is resolved at
 * compile time, so the page breaks when the module is disabled. Prefer a theme_hook() point
 * answered by a ThemeHookInterface implementation.
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
