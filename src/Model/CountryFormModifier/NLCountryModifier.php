<?php

namespace Elgentos\ZipcodeChecker\Model\CountryFormModifier;

use Elgentos\ZipcodeChecker\Api\Data\CountryFormModifierInterface;
use Elgentos\ZipcodeChecker\Api\Data\FieldListModifierInterface;

class NLCountryModifier implements CountryFormModifierInterface
{
    public function isDefault(): bool
    {
        return false;
    }

    public function getCountryCodes(): array
    {
        return ['nl'];
    }

    public function build(FieldListModifierInterface $fieldListModifier): void
    {
        $form = $fieldListModifier->getForm();

        $form->removeField($form->getField('search'));

        //$streetRelatives = $form->getField('street')->getRelatives();
        //$form->getField('street')->hide();
        //foreach ($streetRelatives as $relative) {
        //    $relative->show();
        //}

        //$form->getField('street')->show();
        $form->getField('postcode')->show();
        $form->getField('city')->hide();
    }
}
