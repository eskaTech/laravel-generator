
    /*
     * SWAGGER DOCUMENTATION
     */


    /**
     * @OA\PathParameter(
     *    parameter="{{ $config->schemaName }}Index::sort",
     *    name="sort",
     *    in="query",
     *    description="Field to sort by, prepend with `-` for descending order and comma separated",
     *    required=false,
     *    @OA\Schema(
     *        type="string",
     *        default="{{ $defaultSort}}",
     *        enum={
     *            {!! $allowedSorts !!}
     *        },
     *    ),
     * ),
     * @OA\PathParameter(
     *   parameter="{{ $config->schemaName }}Index::filter.{{ $config->primaryName }}",
     *   name="filter[{{ $config->primaryName }}]",
     *   in="query",
     *   description="Filter by exact {{ $config->modelNames->human }} {{ $config->primaryName }}, comma separated",
     *   required=false,
     *   @OA\Schema(
     *       type="string",
     *   ),
     * ),
     * @OA\PathParameter(
     *   parameter="{{ $config->schemaName }}Index::include",
     *    name="include",
     *    in="query",
     *    description="Include related resources, comma separated",
     *    required=false,
     *    @OA\Schema(
     *        type="string",
     *        default="",
     *        enum={},
     *    ),
     * ),
     */
    private function _swaggerDocumentation(): void {
        // This is only for Swagger documentation
    }
