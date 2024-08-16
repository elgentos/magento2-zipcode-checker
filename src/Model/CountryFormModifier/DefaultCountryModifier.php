<?php

namespace Elgentos\ZipcodeChecker\Model\CountryFormModifier;

use Elgentos\ZipcodeChecker\Api\Data\CountryFormModifierInterface;
use Elgentos\ZipcodeChecker\Api\Data\FieldListModifierInterface;

class DefaultCountryModifier implements CountryFormModifierInterface
{
    public function isDefault(): bool
    {
        return true;
    }

    public function getCountryCodes(): array
    {
        return [];
    }

    public function build(FieldListModifierInterface $fieldListModifier): void
    {
        $form = $fieldListModifier->getForm();

        $form->getField('street')->hide();
        $form->getField('postcode')->hide();
        $form->getField('city')->hide();
    }
}
