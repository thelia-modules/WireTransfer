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

namespace WireTransfer\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Translation\Translator;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\URL;
use WireTransfer\Form\ConfigurationForm;
use WireTransfer\WireTransfer;

/**
 * Class SetTransferConfig
 * @package WireTransfer\Controller
 * @author Thelia <info@thelia.net>
 */
class ConfigureController extends BaseAdminController
{
    public function configure(Request $request, Translator $translator)
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, 'WireTransfer', AccessManager::UPDATE)) {
            return $response;
        }

        // Initialize the potential exception
        $ex = null;

        // Create the Form from the request
        $configurationForm = new ConfigurationForm($request);

        try {
            // Check the form against constraints violations
            $form = $this->validateForm($configurationForm, "POST");

            // Get the form field values
            $data = $form->getData();

            foreach($data as $name => $value) {
                WireTransfer::setConfigValue($name, $value);
            }

            // Log configuration modification
            $this->adminLogAppend(
                "wiretransfer.configuration.message",
                AccessManager::UPDATE,
                sprintf("WireTransfer configuration updated")
            );

            // Everything is OK.
            return new RedirectResponse(URL::getInstance()->absoluteUrl('/admin/module/WireTransfer'));

        } catch (FormValidationException $ex) {
            // Form cannot be validated. Create the error message using
            // the BaseAdminController helper method.
            $error_msg = $this->createStandardFormValidationErrorMessage($ex);
        }
        catch (\Exception $ex) {
            // Any other error
            $error_msg = $ex->getMessage();
        }

        // At this point, the form has errors, and should be redisplayed. We don not redirect,
        // just redisplay the same template.
        // Setup the Form error context, to make error information available in the template.
        $this->setupFormErrorContext(
            $translator->trans("Wire transfer configuration", [], WireTransfer::MESSAGE_DOMAIN),
            $error_msg,
            $configurationForm,
            $ex
        );

        // Do not redirect at this point, or the error context will be lost.
        // Just redisplay the current template.
        return $this->render('module-configure', array('module_code' => 'WireTransfer'));
    }
}
