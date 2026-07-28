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

namespace WireTransfer\Hook\Back;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Form\TheliaFormFactory;
use Thelia\Core\Hook\BaseHook;
use Thelia\Core\Template\Parser\ParserResolver;
use WireTransfer\Form\ConfigurationForm;
use WireTransfer\WireTransfer;

/**
 * Renders the WireTransfer configuration screen in the Thelia 3 default-twig back-office.
 * The Thelia 2 rendering stays in WireTransfer\Hook\HookManager (Smarty).
 */
class ConfigurationHook extends BaseHook
{
    public function __construct(
        private readonly TheliaFormFactory $formFactory,
        ?EventDispatcherInterface $dispatcher = null,
        ?ParserResolver $parserResolver = null,
    ) {
        parent::__construct($dispatcher, $parserResolver);
    }

    public static function getSubscribedHooks(): array
    {
        if (!WireTransfer::isThelia3()) {
            return []; // Thelia 2 uses WireTransfer\Hook\HookManager (config.xml) instead.
        }

        // Distinct method name from HookManager::onModuleConfigure so the two classes do not
        // overwrite each other's module_hook row (keyed by module + hook + method).
        return [
            'module.configuration' => [
                ['type' => 'back', 'method' => 'onModuleConfiguration'],
            ],
        ];
    }

    public function onModuleConfiguration(HookRenderEvent $event): void
    {
        if ('WireTransfer' !== $event->getArgument('modulecode')) {
            return;
        }

        $event->add(
            $this->render('WireTransfer/module-configuration.html.twig', [
                'config_form' => $this->formFactory
                    ->createForm(ConfigurationForm::getName(), FormType::class)
                    ->createView()
                    ->getView(),
            ])
        );
    }
}
