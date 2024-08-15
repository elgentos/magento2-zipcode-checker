<?php

/**
 * Copyright Elgentos BV. All rights reserved.
 * https://www.elgentos.nl/
 */

declare(strict_types=1);

namespace Elgentos\ZipcodeChecker\Model\HyvaCheckout;

use Elgentos\ZipcodeChecker\Api\Data\CountryFormModifierInterface;
use Elgentos\ZipcodeChecker\Enum\FormModeEnum;
use Hyva\Checkout\Model\Form\EntityFormInterface;

class FieldListModifier
{
    /** @var CountryFormModifierInterface[] $countryFormModifiers  */
    protected array $countryFormModifiers = [];

    protected EntityFormInterface $form;

    protected CountryFormModifierInterface $countryFormModifier;

    public function init (
        EntityFormInterface $form,
        array $countryFormModifiers = []
    ): void {
        $this->form = $form;
        $this->countryFormModifiers = $countryFormModifiers;

        $this->form->addField(
            $this->form->createField(
                'search',
                'search',
                [
                    'data' => [
                        'is_required' => true,
                        'label' => 'Search your address',
                        'position' => $form->getField('street')->getSortOrder()
                    ]
                ]
            )
        );
    }

    public function boot(): void
    {}

    public function build(): void
    {
        $this->countryFormModifier = $this->getCountryFormModifier();

        if ($this->countryFormModifier->getMode() === FormModeEnum::SearchBasedOnInput) {
            $this->buildSearchInputForm();
            return;
        }

        $this->buildSearchZipcodeForm();
    }

    public function buildSearchInputForm(): void
    {
        $this->form->getField('street')->hide();
        $this->form->getField('postcode')->hide();
        $this->form->getField('city')->hide();
    }

    public function buildSearchZipcodeForm(): void
    {
        $this->form->removeField($this->form->getField('search'));

        $this->form->getField('street')->show();
        $this->form->getField('postcode')->hide();
        $this->form->getField('city')->hide();
    }

    public function getCountryFormModifier(): ?CountryFormModifierInterface
    {
        $result = array_reduce(
            $this->countryFormModifiers,
            function (array $carry, CountryFormModifierInterface $formModifier) {

                if ($formModifier->isDefault()) {
                    $carry['default'] = $formModifier;
                    return $carry;
                }

                if (in_array($this->getCountryCode(), $formModifier->getCountryCodes())) {
                    $carry['specific'] = $formModifier;
                }

                return $carry;
            },
            [
                'default' => null,
                'specific' => null
            ]
        );

        return $result['specific'] ?? $result['default'];
    }

    public function getCountryCode(): string
    {
        return strtolower(
            $this->form->getField('country_id')->getValue()
        );
    }
}
