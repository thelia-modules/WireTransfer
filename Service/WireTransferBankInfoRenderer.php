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

namespace WireTransfer\Service;

use Thelia\Core\Translation\Translator;
use Thelia\Model\OrderQuery;
use WireTransfer\WireTransfer;

/**
 * Builds the "bank account details" markup shown on the order confirmation page (Thelia 3
 * Flexy theme), replacing the Thelia 2 `order-placed.additional-payment-info` Smarty hook.
 * Kept out of the Twig extension so the extension stays a thin integration layer.
 */
final readonly class WireTransferBankInfoRenderer
{
    public function bankInfo(int|string|null $orderId): string
    {
        if (null === $orderId) {
            return '';
        }

        $order = OrderQuery::create()->findPk((int) $orderId);
        if (null === $order || $order->getPaymentModuleId() !== WireTransfer::getModuleId()) {
            return '';
        }

        $name = htmlspecialchars((string) WireTransfer::getConfigValue('name'), ENT_QUOTES);
        $iban = htmlspecialchars((string) WireTransfer::getConfigValue('iban'), ENT_QUOTES);
        $bic = htmlspecialchars((string) WireTransfer::getConfigValue('bic'), ENT_QUOTES);
        $message = (string) WireTransfer::getConfigValue('message');

        $translator = Translator::getInstance();
        $intro = htmlspecialchars($translator->trans('You may now do a transfer to this bank account: ', [], 'wiretransfer.fo.default'), ENT_QUOTES);
        $holderLabel = htmlspecialchars($translator->trans('Account holder name', [], 'wiretransfer.fo.default'), ENT_QUOTES);
        $ibanLabel = htmlspecialchars($translator->trans('IBAN', [], 'wiretransfer.fo.default'), ENT_QUOTES);
        $bicLabel = htmlspecialchars($translator->trans('BIC code', [], 'wiretransfer.fo.default'), ENT_QUOTES);

        // The merchant-configured message is trusted admin content (rendered raw, as in T2).
        $messageBlock = '' !== $message ? '<div class="wire-transfer-message">'.$message.'</div>' : '';

        return <<<HTML
<div class="wire-transfer-info">
    <p>{$intro}</p>
    <dl>
        <dt>{$holderLabel} :</dt><dd>{$name}</dd>
        <dt>{$ibanLabel} :</dt><dd>{$iban}</dd>
        <dt>{$bicLabel} :</dt><dd>{$bic}</dd>
    </dl>
    {$messageBlock}
</div>
HTML;
    }
}
