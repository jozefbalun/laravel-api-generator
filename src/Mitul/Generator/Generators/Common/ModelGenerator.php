<?php

namespace Mitul\Generator\Generators\Common;

use Config;
use Mitul\Generator\CommandData;
use Mitul\Generator\Generators\GeneratorProvider;
use Mitul\Generator\Utils\GeneratorUtils;
use Illuminate\Support\Str;

class ModelGenerator implements GeneratorProvider
{
    /** @var  CommandData */
    private $commandData;

    /** @var string */
    private $path;

    public function __construct($commandData)
    {
        $this->commandData = $commandData;
        $this->path = Config::get('generator.path_model', app_path('Models/'));
    }

    public function generate()
    {
        $templateName = 'Model';

        $templateData = $this->commandData->templatesHelper->getTemplate($templateName, 'common');

        $templateData = $this->fillTemplate($templateData);

        $fileName = $this->commandData->modelName.'.php';

        if (!file_exists($this->path)) {
            mkdir($this->path, 0755, true);
        }

        $path = $this->path.$fileName;

        $this->commandData->fileHelper->writeFile($path, $templateData);
        $this->commandData->commandObj->comment("\nModel created: ");
        $this->commandData->commandObj->info($fileName);
    }

    private function fillTemplate($templateData)
    {
        if (!$this->commandData->useSoftDelete) {
            $templateData = str_replace('$SOFT_DELETE_IMPORT$', '', $templateData);
            $templateData = str_replace('$SOFT_DELETE$', '', $templateData);
            $templateData = str_replace('$SOFT_DELETE_DATES$', '', $templateData);
        }

        $templateData = GeneratorUtils::fillTemplate($this->commandData->dynamicVars, $templateData);

        $fillables = [];

        foreach ($this->commandData->inputFields as $field) {
            $fillables[] = '"'.Str::lower($field['fieldName']).'"';
        }

        $templateData = str_replace('$FIELDS$', implode(",\n\t\t", $fillables), $templateData);

        $templateData = str_replace('$RULES$', implode(",\n\t\t", $this->generateRules()), $templateData);

        $templateData = str_replace('$CAST$', implode(",\n\t\t", $this->generateCasts()), $templateData);

        return $templateData;
    }

    private function generateRules()
    {
        $rules = [];

        foreach ($this->commandData->inputFields as $field) {
            if (!empty($field['validations'])) {
                $rule = '"'.$field['fieldName'].'" => "'.$field['validations'].'"';
                $rules[] = $rule;
            }
        }

        return $rules;
    }

    public function generateCasts()
    {
        $casts = [];

        foreach ($this->commandData->inputFields as $field) {
            switch ($field['fieldType']) {
                case 'integer':
                    $rule = '"'.Str::lower($field['fieldName']).'" => "integer"';
                    break;
                case 'double':
                    $rule = '"'.Str::lower($field['fieldName']).'" => "double"';
                    break;
                case 'float':
                    $rule = '"'.Str::lower($field['fieldName']).'" => "float"';
                    break;
                case 'boolean':
                    $rule = '"'.Str::lower($field['fieldName']).'" => "boolean"';
                    break;
                case 'string':
                case 'char':
                case 'text':
                    $rule = '"'.Str::lower($field['fieldName']).'" => "string"';
                    break;
                default:
                    $rule = '';
                    break;
            }

            if (!empty($rule)) {
                $casts[] = $rule;
            }
        }

        return $casts;
    }
}
