<?php

namespace WireTransfer\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Thelia\Model\ModuleConfigQuery;
use WireTransfer\WireTransfer;

class ConfigListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'module.config' => [
                'onModuleConfig', 128
                ],
        ];
    }

    public function onModuleConfig(GenericEvent $event): void
    {
        $subject = $event->getSubject();

        if ($subject !== "HealthStatus") {
            throw new \RuntimeException('Event subject does not match expected value');
        }

        $configModule = ModuleConfigQuery::create()
            ->filterByModuleId(WireTransfer::getModuleId())
            ->filterByName(['iban', 'bic', 'name'])
            ->find();

        $moduleConfig = [];
        $moduleConfig['module'] = WireTransfer::getModuleCode();
        $configsCompleted = true;

        if ($configModule->count() === 0) {
            $configsCompleted = false;
        }

        foreach ($configModule as $config) {
            $moduleConfig[$config->getName()] = $config->getValue();
            if ($config->getValue() === null) {
                $configsCompleted = false;
            }
        }

        $moduleConfig['completed'] = $configsCompleted;

        $event->setArgument('wiretransfer.module.config', $moduleConfig);
    }
}
