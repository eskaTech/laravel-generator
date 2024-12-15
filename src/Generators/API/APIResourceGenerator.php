<?php

namespace InfyOm\Generator\Generators\API;

use InfyOm\Generator\Generators\BaseGenerator;
use Illuminate\Support\Str;
use InfyOm\Generator\Generators\SwaggerGenerator;

class APIResourceGenerator extends BaseGenerator
{
    private string $fileName;

    public function __construct()
    {
        parent::__construct();

        $this->path = $this->config->paths->apiResource;
        $this->fileName = $this->config->modelNames->name.'Resource.php';
    }

    public function variables(): array
    {
        return [
            'fields' => implode(',' . infy_nl_tab(1, 3), $this->generateResourceFields()),
            'swaggerHeaderDocs' => $this->fillSwaggerHeaderDocs(),
            'swaggerPropertiesDocs' => $this->fillSwaggerPropertiesDocs(),
        ];
    }

    public function generate()
    {
        $templateData = view('laravel-generator::api.resource.resource', $this->variables())->render();

        g_filesystem()->createFile($this->path.$this->fileName, $templateData);

        $this->config->commandComment(infy_nl().'API Resource created: ');
        $this->config->commandInfo($this->fileName);
    }

    protected function generateResourceFields(): array
    {
        $resourceFields = [];

        // Add fields
        foreach ($this->config->fields as $field) {
            $resourceFields[] = "'" . $field->name . "' => \$this->" . $field->name;
        }

        // Add relationships
        $relations = $this->generateRelationsFields();
        $resourceFields = array_merge($resourceFields, $relations);

        return $resourceFields;
    }


    private function generateRelationsFields()
    {
        $relations = $this->config->relations;
        $relationFields = [];

        foreach ($relations as $relation) {
            $relationResourceClassName = $relation->inputs[0] . 'Resource';

            $singularRelationName = (!empty($relation->relationName)) ? $relation->relationName : Str::snake(class_basename($relation->inputs[0]));
            $pluralRelationName = (!empty($relation->relationName)) ? $relation->relationName : Str::snake(Str::plural(class_basename($relation->inputs[0])));


            switch ($relation->type) {
                case '1t1':
                    $fieldLine = "'" . $singularRelationName . "' => \$this->whenLoaded('$singularRelationName', function () {";
                    $fieldLine .= infy_nl_tab(1, 4) . "return new $relationResourceClassName(\$this->$singularRelationName);";
                    $fieldLine .= infy_nl_tab(1, 3) . "})";
                    $relationFields[] = $fieldLine;
                    break;

                case '1tm':
                case 'mtm':
                case 'hmt':
                    $fieldLine = "'" . $pluralRelationName . "' => \$this->whenLoaded('$pluralRelationName', function () {";
                    $fieldLine .= infy_nl_tab(1, 4) . "return $relationResourceClassName::collection(\$this->$pluralRelationName);";
                    $fieldLine .= infy_nl_tab(1, 3) . "})";
                    $relationFields[] = $fieldLine;
                    break;

                case 'mt1':
                    if (!empty($relation->relationName)) {
                        $singularRelationName = $relation->relationName;
                    } elseif (isset($relation->inputs[1])) {
                        $singularRelationName = Str::snake(str_replace('_id', '', strtolower($relation->inputs[1])));
                    }
                    $fieldLine = "'" . $singularRelationName . "' => \$this->whenLoaded('$singularRelationName', function () {";
                    $fieldLine .= infy_nl_tab(1, 4) . "return new $relationResourceClassName(\$this->$singularRelationName);";
                    $fieldLine .= infy_nl_tab(1, 3) . "})";
                    $relationFields[] = $fieldLine;
                    break;

                default:
                    break;
            }
        }

        return $relationFields;
    }


    protected function fillSwaggerPropertiesDocs(): string
    {
        if (!$this->config->options->swagger) {
            return '';
        }

        return $this->generateSwaggerProperties();
    }


    protected function fillSwaggerHeaderDocs(): string
    {
        if (!$this->config->options->swagger) {
            return '';
        }

        return view('swagger-generator::resource.header', [
        ]);
    }
    public function generateSwaggerProperties(): string
    {
        $properties = [];


        // Fields
        $fields = $this->config->fields;
        foreach ($fields as $field) {
            $field->dbType = explode(',', $field->dbType)[0]; // Remove dbType string after first ',' to keep id
        }
        $fieldTypes = SwaggerGenerator::generateTypes($fields);
        foreach ($fieldTypes as $fieldType) {
            $properties[] = view(
                'swagger-generator::resource.property',
                $fieldType
            )->render();
        }

        // Relations
        $relations = $this->config->relations;
        $relationTypes = SwaggerGenerator::generateRelationsTypes($relations);
        foreach ($relationTypes as $relationType) {
            if (isset($relationType['resourceItemsName'])) {
                $relationType['resourceItemsName'] = $this->config->getSchemaName($relationType['resourceItemsName']);
            } else if (isset($relationType['resourceName'])) {
                $relationType['resourceName'] = $this->config->getSchemaName($relationType['resourceName']);
            }
            $properties[] = view(
                'swagger-generator::resource.relation',
                $relationType
            )->render();
        }


        return (string) view('swagger-generator::resource.proprieties', [
            'properties' => implode(',' . infy_nl() . '     ', $properties),
        ]);
    }

    public function rollback()
    {
        if ($this->rollbackFile($this->path, $this->fileName)) {
            $this->config->commandComment('API Resource file deleted: '.$this->fileName);
        }
    }
}