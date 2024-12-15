/**
     * @OA\Post(
     *      path="/{{ $config->prefixes->getRoutePrefixWith('/') }}{{ $config->modelNames->dashedPlural }}",
     *      summary="create{{ $config->modelNames->name }}{{ $config->prefixes->namespace }}",
     *      operationId="{{ $config->schemaName }}::create",
     *      tags={"{{ $config->modelNames->humanPlural }} ({{ $config->prefixes->route }})"},
     *      description="Create {{ $config->modelNames->name }}",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\JsonContent(ref="#/components/schemas/{{ $config->schemaName }}::CreateBody")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  example="success"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/components/schemas/{{ $config->schemaName }}Resource"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *           response=422,
     *           description="Error: Unprocessable Entity",
     *           @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *      ),
     * )
     */