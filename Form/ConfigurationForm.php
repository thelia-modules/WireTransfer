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

namespace WireTransfer\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Iban;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use WireTransfer\Constraints\BIC;
use WireTransfer\WireTransfer;

/**
 * Class ConfigurationForm
 * @package WireTransfer\Form
 * @author Thelia <info@thelia.net>
 */
class ConfigurationForm extends BaseForm
{
    protected function trans($str, $params = [])
    {
        return Translator::getInstance()->trans($str, $params, WireTransfer::MESSAGE_DOMAIN);
    }

    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                'name',
                TextType::class,
                array(
                    'constraints' => array(new NotBlank()),
                    'required'    => true,
                    'label'       => Translator::getInstance()->trans("Account holder name", [], WireTransfer::MESSAGE_DOMAIN),
                    'data'        => WireTransfer::getConfigValue('name', ''),
                    'label_attr' => array(
                        'for' => 'namefield'
                    )
                )
            )
            ->add(
                'iban',
                TextType::class,
                array(
                    'constraints' => array(new NotBlank(), new Iban()),
                    'required'    => true,
                    'label'       => Translator::getInstance()->trans("IBAN (International Bank Account Number)", [], WireTransfer::MESSAGE_DOMAIN),
                    'data'        => WireTransfer::getConfigValue('iban', ''),
                    'label_attr' => array(
                        'for' => 'ibanfield'
                    )
                )
            )
            ->add(
                'bic',
                TextType::class,
                array(
                    'constraints' => array(new NotBlank(), new BIC()),
                    'required'    => true,
                    'label'       => Translator::getInstance()->trans("BIC (Bank Identifier Code)", [], WireTransfer::MESSAGE_DOMAIN),
                    'data'        => WireTransfer::getConfigValue('bic', ''),
                    'label_attr' => array(
                        'for' => 'bicfield'
                    )
                )
            )
        ;
    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public static function getName()
    {
        return "configurewiretransfer";
    }

}
