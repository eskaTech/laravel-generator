/**
     * @OA\Get(
     *      path="/{{ $config->prefixes->getRoutePrefixWith('/') }}{{ $config->modelNames->dashedPlural }}",
     *      summary="get{{ $config->modelNames->name }}List{{ $config->prefixes->namespace }}",
     *      operationId="{{ $config->schemaName }}::index",
     *      tags={"{{ $config->modelNames->humanPlural }} ({{ $config->prefixes->route }})"},
     *      description="Get all {{ $config->modelNames->plural }}",
     *      @OA\Parameter(
     *         name="filter[search]",
     *         in="query",
     *         description="Search for {{ $config->modelNames->humanPlural }}",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         ),
     *      ),
     *      @OA\Parameter(ref="#/components/parameters/IndexRequest::page.number"),
     *      @OA\Parameter(ref="#/components/parameters/IndexRequest::page.size"),
     *      @OA\Parameter(ref="#/components/parameters/{{ $config->schemaName }}Index::sort"),
     *      @OA\Parameter(ref="#/components/parameters/{{ $config->schemaName }}Index::include"),
     *      @OA\Parameter(ref="#/components/parameters/{{ $config->schemaName }}Index::filter.id"),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(@OA\Property(
     *                      type="array",
     *                      property="data",
     *                      @OA\Items(ref="#/components/schemas/{{ $config->schemaName }}Resource")
     *                   )),
     *                  @OA\Schema(ref="#/components/schemas/Abstract::IndexResourceCollection"),
     *             },
     *          )
     *      ),
     *      @OA\Response(
     *           response=422,
     *           description="Error: Unprocessable Entity",
     *           @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *      ),
     * )
     */