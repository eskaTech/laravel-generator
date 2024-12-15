<?php

namespace InfyOm\Generator\Generators;

use InfyOm\Generator\Common\GeneratorField;
use Illuminate\Support\Str;

class SwaggerGenerator
{
    public static function generateTypes(array $inputFields): array
    {
        $fieldTypes = [];

        /** @var GeneratorField $field */
        foreach ($inputFields as $field) {
            $fieldData = self::getFieldType($field->dbType);

            if (empty($fieldData['fieldType'])) {
                continue;
            }

            $fieldTypes[] = [
                'fieldName'   => $field->name,
                'type'        => $fieldData['fieldType'],
                'format'      => $fieldData['fieldFormat'],
                'nullable'    => !$field->isNotNull ? 'true' : 'false',
                'readOnly'    => !$field->isFillable ? 'true' : 'false',
                'description' => (!empty($field->description)) ? $field->description : '',
            ];
        }

        return $fieldTypes;
    }

    public static function getFieldType($type): array
    {
        $fieldType = null;
        $fieldFormat = null;
        switch (strtolower($type)) {
            case 'increments':
            case 'integer':
            case 'unsignedinteger':
            case 'smallinteger':
            case 'long':
            case 'biginteger':
            case 'unsignedbiginteger':
                $fieldType = 'integer';
                $fieldFormat = 'int32';
                break;
            case 'double':
            case 'float':
            case 'real':
            case 'decimal':
                $fieldType = 'number';
                $fieldFormat = 'number';
                break;
            case 'boolean':
                $fieldType = 'boolean';
                break;
            case 'string':
            case 'char':
            case 'text':
            case 'mediumtext':
            case 'longtext':
            case 'enum':
                $fieldType = 'string';
                break;
            case 'byte':
                $fieldType = 'string';
                $fieldFormat = 'byte';
                break;
            case 'binary':
                $fieldType = 'string';
                $fieldFormat = 'binary';
                break;
            case 'password':
                $fieldType = 'string';
                $fieldFormat = 'password';
                break;
            case 'date':
                $fieldType = 'string';
                $fieldFormat = 'date';
                break;
            case 'datetime':
            case 'timestamp':
                $fieldType = 'string';
                $fieldFormat = 'date-time';
                break;
        }

        return ['fieldType' => $fieldType, 'fieldFormat' => $fieldFormat];
    }

    public static function generateRelationsTypes(array $relations): array
    {
        $relationTypes = [];

        $relationTypes = array_merge($relationTypes, self::generateToOneRelationTypes($relations));
        $relationTypes = array_merge($relationTypes, self::generateToManyRelationTypes($relations));

        return $relationTypes;
    }

    public static function generateToOneRelationTypes(array $relations): array
    {
        $relationTypes = [];

        foreach ($relations as $relation) {
            if (in_array($relation->type, ['1t1', 'mt1'])) {
                $relationName = $relation->relationName ?? Str::snake(class_basename($relation->inputs[0]));
                if (!empty($relation->relationName)) {
                    $relationName = $relation->relationName;
                } elseif (isset($relation->inputs[1])) {
                    $relationName = Str::snake(str_replace('_id', '', strtolower($relation->inputs[1])));
                }
                $relationTypes[] = [
                    'fieldName' => $relationName,
                    'resourceName' => $relation->inputs[0],
                    'readOnly' => 'true',
                    'nullable' => 'false',
                    'description' => 'The relation with ' . $relation->inputs[0] . ' resource, if included.'
                ];
            }
        }

        return $relationTypes;
    }

    public static function generateToManyRelationTypes(array $relations): array
    {
        $relationTypes = [];

        foreach ($relations as $relation) {
            if (in_array($relation->type, ['1tm', 'mtm', 'hmt'])) {
                $relationName = $relation->relationName ?? Str::snake(Str::plural(class_basename($relation->inputs[0])));
                $relationTypes[] = [
                    'fieldName' => $relationName,
                    'resourceItemsName' => $relation->inputs[0],
                    'readOnly' => 'true',
                    'nullable' => 'false',
                    'description' => 'The relation with all ' . $relation->inputs[0] . ' resources, if included.'
                ];
            }
        }

        return $relationTypes;
    }
}