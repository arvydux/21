<?php

namespace App\Livewire\Concerns;

trait NormalizesMoneyInput
{
    /**
     * Accept prices typed as "1,50" as well as "1.50": turn the comma into a dot,
     * drop spaces and turn empty input into null before validation.
     */
    protected function normalizeMoney(string ...$properties): void
    {
        foreach ($properties as $property) {
            $value = $this->{$property};

            if (is_string($value)) {
                $value = str_replace([' ', ','], ['', '.'], trim($value));
                $this->{$property} = $value === '' ? null : $value;
            }
        }
    }

    /**
     * @return array<string, string>
     */
    protected function moneyMessages(string ...$properties): array
    {
        $messages = [];

        foreach ($properties as $property) {
            $messages["{$property}.required"] = 'Įveskite sumą';
            $messages["{$property}.numeric"] = 'Gali būti tik skaičius, pvz. 0,50';
            $messages["{$property}.min"] = 'Suma negali būti neigiama';
        }

        return $messages;
    }
}
