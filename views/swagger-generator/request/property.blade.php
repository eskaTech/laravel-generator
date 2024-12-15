     * @OA\Property(
     *     property="{{ $fieldName }}",
     *     description="{{ $description }}",
     *     nullable={{ $nullable }},
     *     type="{{ $type }}",
@if($format)
     *     format="{{ $format }}"
@endif
     * )