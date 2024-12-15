/**
     * @OA\Put(
     *      path="/{{ $config->prefixes->getRoutePrefixWith('/') }}{{ $config->modelNames->dashedPlural }}/{id}",
     *      summary="update{{ $config->modelNames->name }}{{ $config->prefixes->namespace }}",
     *      operationId="{{ $config->schemaName }}::update",
     *      tags={"{{ $config->modelNames->humanPlural }} ({{ $config->prefixes->route }})"},
     *      description="Update {{ $config->modelNames->name }}",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of {{ $config->modelNames->name }}",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\JsonContent(ref="#/components/schemas/{{ $config->schemaName }}::UpdateBody")
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
     *                  ref="#/components/schemas/{{ $config->schemaName}}Resource"
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