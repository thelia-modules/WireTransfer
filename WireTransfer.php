<?php

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*      Copyright (c) OpenStudio */
/*      email : info@thelia.net */
/*      web : http://www.thelia.net */

/*      This program is free software; you can redistribute it and/or modify */
/*      it under the terms of the GNU General Public License as published by */
/*      the Free Software Foundation; either version 3 of the License */

/*      This program is distributed in the hope that it will be useful, */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the */
/*      GNU General Public License for more details. */

/*      You should have received a copy of the GNU General Public License */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>. */

namespace WireTransfer;

use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Thelia\Core\Translation\Translator;
use Thelia\Install\Database;
use Thelia\Log\Tlog;
use Thelia\Model\MessageQuery;
use Thelia\Model\Order;
use Thelia\Module\AbstractPaymentModule;

/**
 * Class WireTransfer.
 */
class WireTransfer extends AbstractPaymentModule
{
    public const MESSAGE_DOMAIN = 'wiretransfer';

    public function pay(Order $order): void
    {
        // Nothing special to do.
    }

    /**
     * @return bool true if all parameters have been entered
     */
    public function isValidPayment(): bool
    {
        // Check that all parameters have been entered.
        $valid =
            self::getConfigValue('name', '') !== ''
            &&
            self::getConfigValue('bic', '') !== ''
            &&
            self::getConfigValue('iban', '') !== ''
        ;

        if (!$valid) {
            Tlog::getInstance()->addError(
                Translator::getInstance()->trans(
                    'Bank information parameters have not been defined.', [], self::MESSAGE_DOMAIN
                )
            );
        }

        return $valid && $this->getCurrentOrderTotalAmount() > 0;
    }

    public function install(ConnectionInterface $con = null): void
    {
        $database = new Database($con->getWrappedConnection());

        // Insert email message
        $database->insertSql(null, [__DIR__.'/Config/setup.sql']);

        /* insert the images from image folder if not already done */
        $moduleModel = $this->getModuleModel();

        if (!$moduleModel->isModuleImageDeployed($con)) {
            $this->deployImageFolder($moduleModel, sprintf('%s/images', __DIR__), $con);
        }
    }

    public function destroy(ConnectionInterface $con = null, $deleteModuleData = false): void
    {
        // Delete our message
        if (null !== $message = MessageQuery::create()->findOneByName('order_confirmation_wiretransfer')) {
            $message->delete($con);
        }

        parent::destroy($con, $deleteModuleData);
    }

    /**
     * if you want, you can manage stock in your module instead of order process.
     * Return false to decrease the stock when order status switch to pay.
     */
    public function manageStockOnCreation(): bool
    {
        return false;
    }

    public static function configureServices(ServicesConfigurator $servicesConfigurator): void
    {
        $servicesConfigurator->load(self::getModuleCode().'\\', __DIR__)
            ->exclude([THELIA_MODULE_DIR.ucfirst(self::getModuleCode()).'/I18n/*'])
            ->autowire(true)
            ->autoconfigure(true);
    }
}
