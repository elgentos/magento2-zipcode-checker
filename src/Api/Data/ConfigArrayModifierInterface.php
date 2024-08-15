<?php

/**
 * Copyright Elgentos BV. All rights reserved.
 * https://www.elgentos.nl/
 */

declare(strict_types=1);

namespace Elgentos\ZipcodeChecker\Api\Data;

use Symfony\Component\Console\Output\OutputInterface;

interface ConfigArrayModifierInterface
{
    public function updateConfigValues(array $configValues, OutputInterface $output): void;
}
