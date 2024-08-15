<?php

/**
 * Copyright Elgentos BV. All rights reserved.
 * https://www.elgentos.nl/
 */

declare(strict_types=1);

namespace Elgentos\ZipcodeChecker\Console\Command;

use Elgentos\ZipcodeChecker\Api\Data\AttributeArrayModifierInterface;
use Elgentos\ZipcodeChecker\Model\Config\ConfigArrayModifier;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupDutchFields extends Command
{
    private const string COMMAND_NAME = 'elgentos:checkout:setup-dutch-fields';

    protected array $configChanges = [
        [
            'path' => 'hyva_themes_checkout/developer/address_form/use_street_renderer',
            'value' => '1'
        ],
        [
            'path' => 'hyva_themes_checkout/address_form/street/field_label_0',
            'value' => 'Street'
        ],
        [
            'path' => 'hyva_themes_checkout/address_form/street/field_label_1',
            'value' => 'Housenumber'
        ]
    ];

    protected array $attributeChanges = [
        [
            'code' => 'lastname',
            'data' => [
                'length' => "1" // 1 => 50%
            ]
        ],
        [
            'code' => 'street.0',
            'data' => [
                'length' => "3" // 1 => 100%
            ]
        ],
        [
            'code' => 'street.1',
            'data' => [
                'required' => "1",
                'length' => "3" // 1 => 100%
            ]
        ],
        [
            'code' => 'city',
            'data' => [
                'required' => "1"
            ]
        ],
        [
            'code' => 'region',
            'after' => 'city'
        ],
        [
            'code' => 'country_id',
            'after' => 'region'
        ]
    ];

    public function __construct(
        private readonly AttributeArrayModifierInterface $attributeArrayModifier,
        private readonly ConfigArrayModifier $configArrayModifier
    ){
        parent::__construct(self::COMMAND_NAME);
    }

    protected function configure(): void
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription('Change default setting of Hyva to Dutch form setup.');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->configArrayModifier->updateConfigValues(
            $this->configChanges,
            $output
        );

        $this->attributeArrayModifier->updateAttributeValues(
            $this->attributeChanges,
            $output
        );

        return 0;
    }
}
