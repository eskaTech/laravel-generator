@php
    echo "<?php".PHP_EOL;
@endphp

namespace {{ $config->namespaces->apiResource }};

use Illuminate\Http\Resources\Json\JsonResource;

@if(isset($swaggerHeaderDocs)){!! $swaggerHeaderDocs  !!}@endif

class {{ $config->modelNames->name }}Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            {!! $fields !!}
        ];
    }

    @if(isset($swaggerPropertiesDocs)){!! $swaggerPropertiesDocs  !!}@endif
}
