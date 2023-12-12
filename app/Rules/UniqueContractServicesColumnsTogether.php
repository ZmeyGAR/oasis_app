<?php

namespace App\Rules;

use App\Models\ContractServices;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;

class UniqueContractServicesColumnsTogether implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */

    protected $table = 'contract_services';
    protected $columns;

    public function __construct(array $columns)
    {
        $columns = Arr::wrap($columns);
        $this->columns = $columns;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $where = [];
        foreach ($this->columns as $field => $value) {
            $where[] = [$field, '=', $value];
        }

        $exist = ContractServices::where($where)->exists();
        if ($exist) $fail(
            __('validation.custom.contract-service.unique-together')
        );
    }
}
