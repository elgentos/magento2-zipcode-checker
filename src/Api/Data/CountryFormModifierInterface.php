<?php

namespace Elgentos\ZipcodeChecker\Api\Data;

interface CountryFormModifierInterface
{
    public function isDefault(): bool;

    public function getCountryCodes(): array;

    public function build(FieldListModifierInterface $fieldListModifier): void;
}
