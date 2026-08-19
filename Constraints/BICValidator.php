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


declare(strict_types=1);

namespace WireTransfer\Constraints;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Thelia\Core\Translation\Translator;

/**
 * Class BICValidator
 * @package WireTransfer\Constraints
 * @author Thelia <info@thelia.net>
 */
class BICValidator extends ConstraintValidator {
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof BIC) {
            throw new UnexpectedTypeException($constraint, BIC::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $teststring = preg_replace('/\s+/', '', $value);

        // A BIC is 8 or 11 characters: 6 letters + 2 alphanumerics, then an optional 3-char branch code.
        // The pattern is anchored (^...$) so partial matches embedded in a longer string are rejected.
        if(!preg_match('/^[a-zA-Z]{6}[a-zA-Z0-9]{2}([a-zA-Z0-9]{3})?$/', $teststring)) {
            $this->context->addViolation(
                Translator::getInstance()->trans(
                    $constraint->message
                ),
                array(
                    '{{ value }}' => $value
                )
            );
        }
    }

} 