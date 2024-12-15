@php
    echo "<?php".PHP_EOL;
@endphp


namespace {{ $config->namespaces->apiRequest }};

use {{ $config->namespaces->model }}\{{ $config->modelNames->name }};
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Api\Abstracts\IndexRequest;


class Index{{ $config->modelNames->name }}APIRequest extends IndexRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('index', {{ $config->modelNames->name }}::class);
    }

    /**
     * Default sort field
     * @dev Passed to QueryBuilder::defaultSort
     */
    static public $defaultSort = [ '{!! $defaultSort !!}' ];


    /**
     * Array of allowed sorts in `sort` parameter
     * @dev Passed to QueryBuilder::allowedSorts
     * @var array
     */
    static public $allowedSorts = [
        {!! $allowedSorts !!}
    ];

    /**
     * Array of allowed includes in `include` parameter
     * @dev Passed to QueryBuilder::allowedIncludes
     * @var array
     */
    static public $allowedIncludes = [];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'filter.id' => 'sometimes|string',
        ];
    }

    @if(isset($swaggerParams)){!! $swaggerParams  !!}@endif

}
