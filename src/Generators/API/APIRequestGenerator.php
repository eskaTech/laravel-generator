<?php

namespace InfyOm\Generator\Generators\API;

use InfyOm\Generator\Generators\BaseGenerator;
use InfyOm\Generator\Generators\ModelGenerator;
use Illuminate\Support\Str;
use InfyOm\Generator\Generators\SwaggerGenerator;

class APIRequestGenerator extends BaseGenerator
{
    /**
     * Fields not included in the generator rules by default.
     */
    protected array $excluded_fields = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected string $createFileName;
    protected string $updateFileName;
    protected string $indexFileName;
    protected string $destroyFileName;
    protected string $showFileName;

    public function __construct()
    {
        parent::__construct();

        $this->path = $this->config->paths->apiRequest . $this->config->modelNames->name . '/';
        $this->createFileName = 'Create'.$this->config->modelNames->name.'APIRequest.php';
        $this->updateFileName = 'Update'.$this->config->modelNames->name.'APIRequest.php';
        $this->indexFileName = 'Index'.$this->config->modelNames->name.'APIRequest.php';
        $this->destroyFileName = 'Destroy'.$this->config->modelNames->name.'APIRequest.php';
        $this->showFileName = 'Show'.$this->config->modelNames->name.'APIRequest.php';
    }

    public function generate()
    {
        $this->generateCreateRequest();
        $this->generateUpdateRequest();
        $this->generateIndexRequest();
        $this->generateDestroyRequest();
        $this->generateShowRequest();
    }

    public function variables(): array
    {
        $rules = implode(',' . infy_nl_tab(1, 3), $this->generateRules());
        return [
            'createRules' => $rules,
            'updateRules' => str_replace('required', 'sometimes', $rules),
            'createSwaggerPropertiesDocs' => $this->generateSwagger(),
            'updateSwaggerPropertiesDocs' => $this->generateSwagger(),
            'createSwaggerHeaderDocs' => $this->generateSwaggerHeaderDocs('CreateBody', true),
            'updateSwaggerHeaderDocs' => $this->generateSwaggerHeaderDocs('UpdateBody', false),
        ];
    }

    protected function generateCreateRequest()
    {
        $templateData = view('laravel-generator::api.request.create', $this->variables())->render();

        g_filesystem()->createFile($this->path.$this->createFileName, $templateData);

        $this->config->commandComment(infy_nl().'Create Request created: ');
        $this->config->commandInfo($this->createFileName);
    }

    protected function generateUpdateRequest()
    {
        $modelGenerator = app(ModelGenerator::class);
        $rules = $modelGenerator->generateUniqueRules();

        $templateData = view('laravel-generator::api.request.update', [
            'uniqueRules' => $rules,
            ...$this->variables(),
        ])->render();

        g_filesystem()->createFile($this->path.$this->updateFileName, $templateData);

        $this->config->commandComment(infy_nl().'Update Request created: ');
        $this->config->commandInfo($this->updateFileName);
    }

    protected function getDefaultSort(): string
    {
        $defaultSort = '';
        $fields = $this->config->fields;
        foreach ($fields as $field) {
            if ($field->name === 'created_at') {
                $defaultSort = '-created_at';
                break;
            }
            if ($field->htmlType === 'number' || $field->htmlType === 'date') {
                $defaultSort = '-' . $field->name;
                continue;
            }
        }

        return $defaultSort;
    }

    protected function getAllowedSorts(): array
    {
        $allowedSorts = [];
        $fields = $this->config->fields;
        foreach ($fields as $field) {
            $allowedSorts[] = $field->name;
            $allowedSorts[] = '-' . $field->name;
        }

        return $allowedSorts;
    }


    protected function generateIndexRequest()
    {
        $defaultSort = $this->getDefaultSort();
        $allowedSorts = $this->getAllowedSorts();
        $templateData = view('laravel-generator::api.request.index', [
            'defaultSort' => $defaultSort,
            'allowedSorts' => "'" . implode("'," . infy_nl_tab(1, 2) . "'", $allowedSorts) . "'",
            'swaggerParams' => $this->generateIndexSwagger($defaultSort, $allowedSorts),
            ...$this->variables(),
        ])->render();

        g_filesystem()->createFile($this->path.$this->indexFileName, $templateData);

        $this->config->commandComment(infy_nl().'Index Request created: ');
        $this->config->commandInfo($this->indexFileName);
    }

    protected function generateDestroyRequest()
    {
        $templateData = view('laravel-generator::api.request.destroy', [
            ...$this->variables(),
        ])->render();

        g_filesystem()->createFile($this->path.$this->destroyFileName, $templateData);

        $this->config->commandComment(infy_nl().'Destroy Request created: ');
        $this->config->commandInfo($this->destroyFileName);
    }

    protected function generateShowRequest()
    {
        $templateData = view('laravel-generator::api.request.show', [
            ...$this->variables(),
        ])->render();

        g_filesystem()->createFile($this->path.$this->showFileName, $templateData);

        $this->config->commandComment(infy_nl().'Show Request created: ');
        $this->config->commandInfo($this->showFileName);
    }

    public function rollback()
    {
        if ($this->rollbackFile($this->path, $this->createFileName)) {
            $this->config->commandComment('Create API Request file deleted: '.$this->createFileName);
        }

        if ($this->rollbackFile($this->path, $this->updateFileName)) {
            $this->config->commandComment('Update API Request file deleted: '.$this->updateFileName);
        }

        if ($this->rollbackFile($this->path, $this->indexFileName)) {
            $this->config->commandComment('Index API Request file deleted: '.$this->indexFileName);
        }

        if ($this->rollbackFile($this->path, $this->destroyFileName)) {
            $this->config->commandComment('Destroy API Request file deleted: '.$this->destroyFileName);
        }

        if ($this->rollbackFile($this->path, $this->showFileName)) {
            $this->config->commandComment('Show API Request file deleted: '.$this->showFileName);
        }
    }

    public function generateRules(): array
    {
        $dont_require_fields = config('laravel_generator.options.hidden_fields', [])
            + config('laravel_generator.options.excluded_fields', $this->excluded_fields);

        $rules = [];

        foreach ($this->config->fields as $field) {
            if (!$field->isPrimary && !in_array($field->name, $dont_require_fields)) {
                if ($field->isNotNull && empty($field->validations)) {
                    $field->validations = 'required';
                }

                /**
                 * Generate some sane defaults based on the field type if we
                 * are generating from a database table.
                 */
                if ($this->config->getOption('fromTable')) {
                    $rule = empty($field->validations) ? [] : explode('|', $field->validations);

                    if (!$field->isNotNull) {
                        $rule[] = 'nullable';
                    }

                    switch ($field->dbType) {
                        case 'integer':
                            $rule[] = 'integer';
                            break;
                        case 'boolean':
                            $rule[] = 'boolean';
                            break;
                        case 'float':
                        case 'double':
                        case 'decimal':
                            $rule[] = 'numeric';
                            break;
                        case 'string':
                        case 'text':
                            $rule[] = 'string';

                            // Enforce a maximum string length if possible.
                            if ((int) $field->fieldDetails->getLength() > 0) {
                                $rule[] = 'max:' . $field->fieldDetails->getLength();
                            }
                            break;
                    }

                    $field->validations = implode('|', array_unique($rule));
                }
            }

            if (!empty($field->validations)) {
                if (Str::contains($field->validations, 'unique:')) {
                    $rule = explode('|', $field->validations);
                    // move unique rule to last
                    usort($rule, function ($record) {
                        return (Str::contains($record, 'unique:')) ? 1 : 0;
                    });
                    $field->validations = implode('|', array_unique($rule));
                }
                $rule = "'" . $field->name . "' => '" . $field->validations . "'";
                $rules[] = $rule;
            }
        }

        return $rules;
    }

    protected function generateRequiredFields(): array
    {
        $requiredFields = [];

        if (isset($this->config->fields) && !empty($this->config->fields)) {
            foreach ($this->config->fields as $field) {
                if (!empty($field->validations)) {
                    if (Str::contains($field->validations, 'required')) {
                        $requiredFields[] = '"' . $field->name . '"';
                    }
                }
            }
        }

        return $requiredFields;
    }
    
    protected function generateSwaggerHeaderDocs($name, bool $withRequired = true): string
    {
        if (!$this->config->options->swagger) {
            return '';
        }

        $requiredFields = $withRequired ? $this->generateRequiredFields() : [];

        $requiredFields = '{' . implode(',', $requiredFields) . '}';

        return view('swagger-generator::request.header', [
            'requiredFields' => $requiredFields,
            'name' => $name,
        ]);
    }


    public function generateSwagger(): string
    {
        if (!$this->config->options->swagger) {
            return '';
        }

        $dont_require_fields = config('laravel_generator.options.hidden_fields', [])
            + config('laravel_generator.options.excluded_fields', $this->excluded_fields);

        $properties = [];

        // Fields
        $fields = [];
        foreach ($this->config->fields as $field) {
            if ($field->isPrimary || in_array($field->name, $dont_require_fields)) {
                continue;
            }
            $field->dbType = explode(',', $field->dbType)[0]; // Remove dbType string after first ',' to keep id
            $fields[] = $field;
        }
        $fieldTypes = SwaggerGenerator::generateTypes($fields);
        foreach ($fieldTypes as $fieldType) {

            $properties[] = view(
                'swagger-generator::request.property',
                $fieldType
            )->render();
        }

        return view('swagger-generator::request.proprieties', [
            'properties' => implode(',' . infy_nl() . '     ', $properties),
        ]);
    }

    public function generateIndexSwagger(string $defaultSort, array $allowedSorts): string
    {
        if (!$this->config->options->swagger) {
            return '';
        }

        return view('swagger-generator::request.index', [
            'defaultSort' => $defaultSort,
            'allowedSorts' => '"' . implode('",' . infy_nl_tab(1, 1) . ' *            "', $allowedSorts) . '"',
        ]);

    }
}