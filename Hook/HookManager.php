<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

declare(strict_types=1);

namespace WireTransfer\Hook;

use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use WireTransfer\WireTransfer;

/**
 * Thelia 2 Smarty hooks. On Thelia 3 the back-office configuration is rendered by
 * WireTransfer\Hook\Back\ConfigurationHook (default-twig) and the order-placed bank
 * information by the theme through wiretransfer_bank_info(), so both methods no-op there.
 */
class HookManager extends BaseHook
{
    public function onModuleConfigure(HookRenderEvent $event): void
    {
        if (WireTransfer::isThelia3()) {
            return;
        }

        $event->add($this->render('module_configuration.html'));
    }

    public function onAdditionalPaymentInfo(HookRenderEvent $event): void
    {
        if (WireTransfer::isThelia3()) {
            return;
        }

        $event->add($this->render('order-placed.additional-payment-info.html', [
            'placed_order_id' => $event->getArgument('placed_order_id'),
        ]));
    }
}
