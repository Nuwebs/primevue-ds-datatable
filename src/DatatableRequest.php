<?php

namespace Nuwebs\PrimevueDatatable;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class DatatableRequest extends FormRequest
{
    /**
     * This FormRequest is made for general DTs purposes, so you should
     * handle the authorization inside the controller. Or you could extend
     * this class and overwrite this method.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'dt_params' => json_decode($this->query('dt_params'), true)
        ]);
    }

    public function rules(): array
    {
        return [
            'dt_params.columns' => 'required|array',
            'dt_params.columns.*' => 'string',
            'dt_params.first' => 'required|integer|min:0',
            'dt_params.rows' => 'required|integer|min:1',
            'dt_params.page' => 'required|integer|min:0',
            'dt_params.sortField' => 'nullable|string',
            'dt_params.sortDesc' => 'boolean',
            'dt_params.filters' => 'nullable|array',
            'dt_params.filters.*.value' => 'nullable',
            'dt_params.filters.*.matchMode' => Rule::in(FilterMatchMode::getAllValues()),
        ];
    }
}