/**
 * @OA\Schema(
 *     title="{{ $name }} {{ $config->modelNames->human }} Request",
 *     schema="{{ $config->schemaName }}::{{ $name }}",
 *     type="object",
@if(isset($requiredFields))
 *     required={!! $requiredFields !!},
@endif
 * ),
 */