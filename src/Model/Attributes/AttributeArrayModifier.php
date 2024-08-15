<?php

/**
 * Copyright Elgentos BV. All rights reserved.
 * https://www.elgentos.nl/
 */

declare(strict_types=1);

namespace Elgentos\ZipcodeChecker\Model\Attributes;

use Elgentos\ZipcodeChecker\Api\Data\AttributeArrayModifierInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Symfony\Component\Console\Output\OutputInterface;

readonly class AttributeArrayModifier implements AttributeArrayModifierInterface
{
    private const string EAV_ATTRIBUTE_FORM_FIELD_PATH = 'hyva_themes_checkout/component/shipping_address/eav_attribute_form_fields';

    public function __construct(
        private WriterInterface $configWriter,
        private ScopeConfigInterface $scopeConfig,
    ) {
    }

    public function updateAttributeValues(
        array $changes,
        OutputInterface $output
    ): void {
        $value      = $this->scopeConfig->getValue(self::EAV_ATTRIBUTE_FORM_FIELD_PATH);
        $attributes = json_decode($value, true) ?? [];

        if (! $attributes) {
            return;
        }

        foreach ($attributes as $key => $attribute) {
            $change = $this->getAttributeChange($changes, $attribute['attribute_code']);

            $output->writeln(
                sprintf(
                    'Updating attribute: %s',
                    $attribute['attribute_code']
                )
            );

            if (!$change || !isset($change['data'])) {
                continue;
            }

            $attributes[$key] = [
                ...$attribute,
                ...$change['data']
            ];
        }

        $attributes = $this->changeFieldOrder($changes, $attributes);

        $this->configWriter->save(
            self::EAV_ATTRIBUTE_FORM_FIELD_PATH,
            json_encode($attributes)
        );
    }

    public function reorder(array $attributes): array
    {
        $newArray = [];
        $values   = array_values($attributes);

        usort(
            $values,
            function($aAttr, $bAttr) {
                return ((float) $aAttr['sort_order']) >= ((float) $bAttr['sort_order']) ? 1 : -1;
            }
        );

        foreach ($values as $index => $data) {
            $key = $this->getKeyOfAttribute($attributes, $data['attribute_code']);
            $attributes[$key]['sort_order'] = (string) ($index + 1);
            $newArray[$key] = $attributes[$key];
        }

        return $newArray;
    }

    public function getAttributeChange(array $changes, string $code): ?array
    {
        return array_reduce(
            $changes,
            function ($carry, $change) use ($code) {
                return ($change['code'] === $code) ? $change : $carry;
            },
            null,
        );
    }

    public function getAttributeByCode(array $attributes, string $code): ?array
    {
        return array_reduce(
            array_values($attributes),
            function (?array $carry, array $attribute) use ($code, $attributes) {
                return ($attribute['attribute_code'] === $code) ? $attribute : $carry;
            },
            null
        );
    }

    public function getKeyOfAttribute(array $attributes, string $code): ?string
    {
        return array_reduce(
            array_keys($attributes),
            function (?string $carry, string $key) use ($code, $attributes) {
                return ($attributes[$key]['attribute_code'] === $code) ? $key : $carry;
            },
            null
        );
    }

    public function changeFieldOrder(array $changes, array $attributes): array
    {
        foreach ($changes as $change) {

            if (!isset($change['after'])) {
                continue;
            }

            $target = $this->getAttributeByCode($attributes, $change['after']);
            $key = $this->getKeyOfAttribute($attributes, $change['code']);

            $attributes[$key]['sort_order'] = $target['sort_order'] + 0.01;
        }

        return $this->reorder($attributes);
    }
}
