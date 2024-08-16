<?php

/**
 * Copyright Elgentos BV. All rights reserved.
 * https://www.elgentos.nl/
 */

declare(strict_types=1);

namespace Elgentos\ZipcodeChecker\Model\HyvaCheckout;

use Elgentos\ZipcodeChecker\Api\Data\CountryFormModifierInterface;
use Elgentos\ZipcodeChecker\Api\Data\FieldListModifierInterface;
use Hyva\Checkout\Model\Form\EntityFormInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class FieldListModifier implements FieldListModifierInterface
{
    /** @var CountryFormModifierInterface[] $countryFormModifiers  */
    protected array $countryFormModifiers = [];

    protected EntityFormInterface $form;

    protected CountryFormModifierInterface $countryFormModifier;

    public function __construct(
        private readonly Session $session,
    ) {
    }

    public function init (
        EntityFormInterface $form,
        array $countryFormModifiers = []
    ): void {
        $this->countryFormModifiers = $countryFormModifiers;
        $this->form                 = $form;

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
        $this->dispatchCountryModifier('build');
    }

    public function getForm(): EntityFormInterface
    {
        return $this->form;
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

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getCountryCode(): string
    {
        $countryCode = $this->form->getField('country_id')->getValue();

        if (! $countryCode) {
            $countryCode = $this->session->getQuote()->getShippingAddress()->getCountryId();
        }

        return strtolower($countryCode);
    }

    protected function dispatchCountryModifier(string $method): void
    {
        $countryModifier = $this->getCountryFormModifier();

        if (! $countryModifier) {
            return;
        }

        if (! method_exists($countryModifier, $method)) {
            return;
        }

        $countryModifier->{$method}($this);
    }
}
