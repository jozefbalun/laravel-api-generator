<?php

namespace Mitul\Generator\Generators\Common;

use Config;
use Mitul\Generator\CommandData;
use Mitul\Generator\Generators\GeneratorProvider;
use Mitul\Generator\Utils\GeneratorUtils;

class RequestGenerator implements GeneratorProvider
{
    /** @var  CommandData */
    private $commandData;

    /** @var string */
    private $path;

    /** @var boolean generate separate requests or one */
    private $separate_request;

    public function __construct($commandData)
    {
        $this->commandData = $commandData;
        $this->path = Config::get('generator.path_request', app_path('Http/Requests/'));
        $this->separate_request = Config::get('generator.separate_request', false);
    }

    public function generate()
    {
        if($separate_request) {
            $this->generateCreateRequest();
            $this->generateUpdateRequest();
        } else {
            $this->generateRequest();
        }
    }

    private function generateCreateRequest()
    {
        $templateData = $this->commandData->templatesHelper->getTemplate('CreateRequest', 'scaffold/requests');

        $templateData = GeneratorUtils::fillTemplate($this->commandData->dynamicVars, $templateData);

        $fileName = 'Create'.$this->commandData->modelName.'Request.php';

        $path = $this->path.$fileName;

        $this->commandData->fileHelper->writeFile($path, $templateData);
        $this->commandData->commandObj->comment("\nCreate Request created: ");
        $this->commandData->commandObj->info($fileName);
    }

    private function generateUpdateRequest()
    {
        $templateData = $this->commandData->templatesHelper->getTemplate('UpdateRequest', 'scaffold/requests');

        $templateData = GeneratorUtils::fillTemplate($this->commandData->dynamicVars, $templateData);

        $fileName = 'Update'.$this->commandData->modelName.'Request.php';

        $path = $this->path.$fileName;

        $this->commandData->fileHelper->writeFile($path, $templateData);
        $this->commandData->commandObj->comment("\nUpdate Request created: ");
        $this->commandData->commandObj->info($fileName);
    }

    private function generateRequest()
    {
        $templateData = $this->commandData->templatesHelper->getTemplate('Request', 'scaffold/requests');

        $templateData = GeneratorUtils::fillTemplate($this->commandData->dynamicVars, $templateData);

        $fileName = $this->commandData->modelName.'Request.php';

        $path = $this->path.$fileName;

        $this->commandData->fileHelper->writeFile($path, $templateData);
        $this->commandData->commandObj->comment("\nRequest created: ");
        $this->commandData->commandObj->info($fileName);
    }
}
