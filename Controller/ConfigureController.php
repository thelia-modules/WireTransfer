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

declare(strict_types=1);

namespace WireTransfer\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\URL;
use WireTransfer\Form\ConfigurationForm;
use WireTransfer\WireTransfer;

/**
 * Class SetTransferConfig.
 *
 * @author Thelia <info@thelia.net>
 */
class ConfigureController extends BaseAdminController
{
    #[Route('/admin/wiretransfer/configure', name: 'wiretransfer.configure', methods: ['POST'])]
    public function configure(Request $request)
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, 'WireTransfer', AccessManager::UPDATE)) {
            return $response;
        }

        // Initialize the potential exception
        $ex = null;

        // Create the Form from the request
        $configurationForm = $this->createForm(ConfigurationForm::class);

        try {
            // Check the form against constraints violations
            $form = $this->validateForm($configurationForm, 'POST');

            // Get the form field values
            $data = $form->getData();

            foreach ($data as $name => $value) {
                WireTransfer::setConfigValue($name, $value);
            }

            // Log configuration modification
            $this->adminLogAppend(
                'wiretransfer.configuration.message',
                AccessManager::UPDATE,
                'WireTransfer configuration updated'
            );

            // Everything is OK.
            return new RedirectResponse(URL::getInstance()->absoluteUrl('/admin/module/WireTransfer'));
        } catch (FormValidationException $ex) {
            // Form cannot be validated. Create the error message using
            // the BaseAdminController helper method.
            $error_msg = $this->createStandardFormValidationErrorMessage($ex);
        } catch (\Exception $ex) {
            // Any other error
            $error_msg = $ex->getMessage();
        }

        // Setup the Form error context, to make error information available in the template.
        $this->setupFormErrorContext(
            $this->translator->trans('Wire transfer configuration', [], WireTransfer::MESSAGE_DOMAIN),
            $error_msg,
            $configurationForm,
            $ex
        );

        // The configuration screen is rendered through a hook, so redirect back to the module
        // configuration page. That redirect drops the ParserContext form error, hence the flash
        // bag (rendered by the default-twig base template as app.flashes).
        $session = $request->getSession();
        if ($session instanceof FlashBagAwareSessionInterface) {
            $session->getFlashBag()->add('danger', $error_msg);
        }

        return new RedirectResponse(URL::getInstance()->absoluteUrl('/admin/module/WireTransfer'));
    }
}
