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

namespace WireTransfer\Loop;

use Thelia\Core\Template\Element\ArraySearchLoopInterface;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Model\Base\OrderQuery;
use WireTransfer\WireTransfer;

/**
 * Class GetBankInformation
 * @package WireTransfer\Loop
 * @author Thelia <info@thelia.net>
 */
class GetBankInformation extends BaseLoop implements ArraySearchLoopInterface
{
    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        $order = OrderQuery::create()->findPk($this->getOrderId());

        if ($order !== null || $order->getPaymentModuleId() === WireTransfer::getModuleId() ) {

            $loopResultRow = new LoopResultRow();

            $loopResultRow
                ->set("BIC"                , WireTransfer::getConfigValue('bic'))
                ->set("IBAN"               , WireTransfer::getConfigValue('iban'))
                ->set("ACCOUNT_HOLDER_NAME", WireTransfer::getConfigValue('name'))
            ;

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }

    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntTypeArgument("order_id", null, true, false)
        );
    }

    /**
     * this method returns an array
     *
     * @return array
     */
    public function buildArray()
    {
        // Return an array containing one element, so that parseResults() will be called one time.
        return [ 'one-element' ];
    }
}
