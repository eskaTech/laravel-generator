@php
    echo "<?php".PHP_EOL;
@endphp

namespace {{ $config->namespaces->apiController }};

use App\Http\Resources\v1\Collections\IndexResourceCollection;
use {{ $config->namespaces->apiRequest }}\Create{{ $config->modelNames->name }}APIRequest;
use {{ $config->namespaces->apiRequest }}\Update{{ $config->modelNames->name }}APIRequest;
use {{ $config->namespaces->apiRequest }}\Index{{ $config->modelNames->name }}APIRequest;
use {{ $config->namespaces->apiRequest }}\Show{{ $config->modelNames->name }}APIRequest;
use {{ $config->namespaces->apiRequest }}\Destroy{{ $config->modelNames->name }}APIRequest;
use {{ $config->namespaces->model }}\{{ $config->modelNames->name }};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use {{ $config->namespaces->app }}\Http\Controllers\AppBaseController;
use {{ $config->namespaces->apiResource }}\{{ $config->modelNames->name }}Resource;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Queries\Filters\FuzzyFilter;

{!! $docController !!}
class {{ $config->modelNames->name }}APIController extends AppBaseController
{
    {!! $docIndex !!}
    public function index(Index{{ $config->modelNames->name }}APIRequest $request): JsonResponse
    {
        $data = QueryBuilder::for({{ $config->modelNames->name }}::class)
            ->allowedFilters(
                [
                    AllowedFilter::custom(
                        'search',
                        new FuzzyFilter(
                            {!! $fuzzyFields !!}
                        ),
                    ),
                    AllowedFilter::exact('{{ $config->primaryName }}', '{{ $config->tableName }}.{{ $config->primaryName }}'),
                ]
            )
            ->select('{{ $config->tableName }}.*')
            ->defaultSort(Index{{ $config->modelNames->name }}APIRequest::$defaultSort)
            ->allowedSorts(Index{{ $config->modelNames->name }}APIRequest::$allowedSorts)
            ->allowedIncludes(Index{{ $config->modelNames->name }}APIRequest::$allowedIncludes)
            ->jsonPaginate();

        $collection = new IndexResourceCollection($data, {{ $config->modelNames->name }}Resource::class);

        return $collection->toResponse($request);
    }

    {!! $docStore !!}
    public function store(Create{{ $config->modelNames->name }}APIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var {{ $config->modelNames->name }} ${{ $config->modelNames->camel }} */
        ${{ $config->modelNames->camel }} = {{ $config->modelNames->name }}::create($input);

@if($config->options->localized)
        return $this->sendResponse(
            new {{ $config->modelNames->name }}Resource(${{ $config->modelNames->camel }}),
            __('messages.saved', ['model' => __('models/{{ $config->modelNames->camelPlural }}.singular')])
        );
@else
        return $this->sendResponse(new {{ $config->modelNames->name }}Resource(${{ $config->modelNames->camel }}), '{{ $config->modelNames->human }} saved successfully');
@endif
    }

    {!! $docShow !!}
    public function show({{ $config->modelNames->name }} ${{ $config->modelNames->camel }}, Show{{ $config->modelNames->name }}APIRequest $request): JsonResponse
    {
        if (empty(${{ $config->modelNames->camel }})) {
@if($config->options->localized)
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/{{ $config->modelNames->camelPlural }}.singular')])
            );
@else
            return $this->sendError('{{ $config->modelNames->human }} not found');
@endif
        }

@if($config->options->localized)
        return $this->sendResponse(
            new {{ $config->modelNames->name }}Resource(${{ $config->modelNames->camel }}),
            __('messages.retrieved', ['model' => __('models/{{ $config->modelNames->camelPlural }}.singular')])
        );
@else
        return $this->sendResponse(new {{ $config->modelNames->name }}Resource(${{ $config->modelNames->camel }}), '{{ $config->modelNames->human }} retrieved successfully');
@endif
    }

    {!! $docUpdate !!}
    public function update({{ $config->modelNames->name }} ${{ $config->modelNames->camel }}, Update{{ $config->modelNames->name }}APIRequest $request): JsonResponse
    {
       if (empty(${{ $config->modelNames->camel }})) {
@if($config->options->localized)
        return $this->sendError(
            __('messages.not_found', ['model' => __('models/{{ $config->modelNames->camelPlural }}.singular')])
        );
@else
            return $this->sendError('{{ $config->modelNames->human }} not found');
@endif
        }

        ${{ $config->modelNames->camel }}->fill($request->all());
        ${{ $config->modelNames->camel }}->save();

@if($config->options->localized)
        return $this->sendResponse(
            new {{ $config->modelNames->name }}Resource(${{ $config->modelNames->camel }}),
            __('messages.updated', ['model' => __('models/{{ $config->modelNames->camelPlural }}.singular')])
        );
@else
        return $this->sendResponse(new {{ $config->modelNames->name }}Resource(${{ $config->modelNames->camel }}), '{{ $config->modelNames->name }} updated successfully');
@endif
    }

    {!! $docDestroy !!}
    public function destroy({{ $config->modelNames->name }} ${{ $config->modelNames->camel }}, Destroy{{ $config->modelNames->name }}APIRequest $request): JsonResponse
    {
        if (empty(${{ $config->modelNames->camel }})) {
@if($config->options->localized)
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/{{ $config->modelNames->camelPlural }}.singular')])
            );
@else
            return $this->sendError('{{ $config->modelNames->human }} not found');
@endif
        }

        ${{ $config->modelNames->camel }}->delete();

@if($config->options->localized)
        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/{{ $config->modelNames->camelPlural }}.singular')])
        );
@else
        return $this->sendSuccess('{{ $config->modelNames->human }} deleted successfully');
@endif
    }
}
