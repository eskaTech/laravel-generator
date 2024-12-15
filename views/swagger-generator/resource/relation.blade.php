     * @OA\Property(
     *     property="{{ $fieldName }}",
     *     description="{{ $description }}",
     *     readOnly={{ $readOnly }},
     *     nullable={{ $nullable }},
@if(isset($resourceItemsName))
     *     type="array",
     *     @OA\Items(ref="#/components/schemas/{{ $resourceItemsName }}Resource")
@endif
@if(isset($resourceName))
     *     type="object",
     *     ref="#/components/schemas/{{ $resourceName }}Resource"
@endif
     * )