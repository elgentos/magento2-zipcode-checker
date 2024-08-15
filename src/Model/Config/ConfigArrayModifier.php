<?php

/**
 * Copyright Elgentos BV. All rights reserved.
 * https://www.elgentos.nl/
 */

declare(strict_types=1);

namespace Elgentos\ZipcodeChecker\Model\Config;

use Elgentos\ZipcodeChecker\Api\Data\ConfigArrayModifierInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Symfony\Component\Console\Output\OutputInterface;

readonly class ConfigArrayModifier implements ConfigArrayModifierInterface
{
    public function __construct(
        private WriterInterface $configWriter
    ) {
    }

    public function updateConfigValues(
        array $configValues,
        OutputInterface $output
    ): void {
        foreach ($configValues as $change) {
            $this->configWriter->save(
                $change['path'],
                $change['value']
            );

            $output->writeln(
                sprintf(
                    'Updating config value: %s',
                    $change['path']
                )
            );
        }
    }
}
