@php
    echo "<?php".PHP_EOL;
@endphp

namespace {{ $config->namespaces->apiRequest }};

use {{ $config->namespaces->model }}\{{ $config->modelNames->name }};
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

@if(isset($updateSwaggerHeaderDocs)){!! $updateSwaggerHeaderDocs  !!}@endif

class Update{{ $config->modelNames->name }}APIRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('edit', $this->{{ $config->modelNames->camel }});
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            {!! $updateRules !!}
        ];
        {!! $uniqueRules !!}
        return $rules;
    }

    @if(isset($updateSwaggerPropertiesDocs)){!! $updateSwaggerPropertiesDocs  !!}@endif
}
