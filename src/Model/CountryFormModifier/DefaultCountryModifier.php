<?php

namespace Elgentos\ZipcodeChecker\Model\CountryFormModifier;

use Elgentos\ZipcodeChecker\Api\Data\CountryFormModifierInterface;
use Elgentos\ZipcodeChecker\Enum\FormModeEnum;

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

    public function getMode(): FormModeEnum
    {
        return FormModeEnum::SearchBasedOnInput;
    }
}
