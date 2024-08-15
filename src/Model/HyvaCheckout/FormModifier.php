<?php

/**
 * Copyright Elgentos BV. All rights reserved.
 * https://www.elgentos.nl/
 */

declare(strict_types=1);

namespace Elgentos\ZipcodeChecker\Model\HyvaCheckout;

use Elgentos\ZipcodeChecker\Api\Data\CountryFormModifierInterface;
use Hyva\Checkout\Model\Form\EntityFormInterface;
use Hyva\Checkout\Model\Form\EntityFormModifierInterface;

class FormModifier implements EntityFormModifierInterface
{
    /**
     * @param CountryFormModifierInterface[] $countryFormModifiers
     */
    public function __construct(
        public FieldListModifier $fieldListModifier,
        public array $countryFormModifiers = []
    ){
    }

    public function apply(EntityFormInterface $form): EntityFormInterface
    {
        $form->registerModificationListener(
            'elgentos_zipcode_checker_shipping_address_form_init',
            'form:init',
            function (EntityFormInterface $form) {
                $this->fieldListModifier->init(
                    $form,
                    $this->countryFormModifiers
                );
            },
        );

        $form->registerModificationListener(
            'elgentos_zipcode_checker_shipping_address_form_boot',
            'form:boot',
            [$this->fieldListModifier, 'boot']
        );

        $form->registerModificationListener(
            'elgentos_zipcode_checker_shipping_address_form_build',
            'form:build',
            [$this->fieldListModifier, 'build']
        );

        return $form;
    }
}
