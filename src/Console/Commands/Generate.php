<?php

namespace OzanAkman\RepositoryGenerator\Console\Commands;

use Illuminate\Console\Command;
use OzanAkman\RepositoryGenerator\Exceptions\FileException;
use OzanAkman\RepositoryGenerator\Exceptions\StubException;

class Generate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repositories:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Quickly generating repository and interfaces from existing model files';

    /**
     * Overriding existing files.
     *
     * @var bool
     */
    protected $override = false;

    /**
     * Execute the console command.
     *
     * @throws FileException
     * @return void
     */
    public function handle()
    {
        // Check repository folder permissions.
        $this->checkRepositoryPermissions();

        // Create repository folder if it's necessary.
        $this->createFolder(config('repository-generator.repository_directory'));

        // Check interface folder permissions.
        $this->checkInterfacePermissions();

        // Create interface folder if it's necessary.
        $this->createFolder(config('repository-generator.interface_directory'));

        // Get all model file names.
        $models = $this->getModels();

        // Check model files.
        if (count($models) === 0) {
            $this->noModelsMessage();
        }

        // Get existing repository file names.
        $existingRepositoryFiles = glob($this->repositoryPath('*.php'));

        // Remove main repository file name from array
        $existingRepositoryFiles = array_diff(
            $existingRepositoryFiles,
            [$this->repositoryPath(config('repository-generator.main_repository_file'))]
        );

        // Ask for overriding, If there are files in repository directory.
        if (count($existingRepositoryFiles) > 0) {
            if ($this->confirm('Do you want to overwrite the existing files? (Yes/No):')) {
                $this->override = true;
            }
        }

        // Get existing interface file names.
        $existingInterfaceFiles = glob($this->interfacePath('*.php'));

        // Remove main interface file from array
        $existingInterfaceFiles = array_diff(
            $existingInterfaceFiles,
            [$this->repositoryPath(config('repository-generator.main_interface_file'))]
        );

        // Ask for overriding, If there are files in interface repository.
        // It could be already asked while checking repository files.
        // If so, we won't show this confirm question again.
        if (count($existingInterfaceFiles) > 0 && ! $this->override) {
            if ($this->confirm('Do you want to overwrite the existing files? (Yes/No):')) {
                $this->override = true;
            }
        }

        // Get stub file templates.
        $repositoryStub = $this->getStub('Repository');
        $interfaceStub = $this->getStub('Interface');

        // Repository stub values those should be changed by command.
        $repositoryStubValues = [
            '__USE_STATEMENT_FOR_REPOSITORY__',
            '__REPOSITORY_NAMESPACE__',
            '__MAIN_REPOSITORY__',
            '__REPOSITORY__',
            '__MODEL_NAMESPACE_',
            '__MODEL__',
            '__INTERFACE_NAMESPACE_',
            '__INTERFACE__',
        ];

        // Interface stub values those should be changed by command.
        $interfaceStubValues = [
            '__USE_STATEMENT_FOR_INTERFACE__',
            '__INTERFACE_NAMESPACE__',
            '__MAIN_INTERFACE__',
            '__INTERFACE__',
        ];

        foreach ($models as $model) {

            // Add suffixes
            $repository = suffix($model, 'Repository');
            $interface = suffix($model, 'RepositoryInterface');

            // Current repository file name
            $repositoryFile = $this->repositoryPath($repository.'.php');

            // Check main repository file's path to add use
            $useStatementForRepository = false;
            if (dirname($repositoryFile) !== dirname(config('repository-generator.main_repository_file'))
            ) {
                $mainRepository = config('repository-generator.main_repository_class');
                $useStatementForRepository = 'use '.$mainRepository.';';
            }

            // Fillable repository values for generating real files
            $repositoryValues = [
                $useStatementForRepository ? $useStatementForRepository : '',
                config('repository-generator.repository_namespace'),
                str_replace('.php', '', config('repository-generator.main_repository_file')),
                $repository,
                config('repository-generator.model_namespace'),
                $model,
                config('repository-generator.interface_namespace'),
                $interface,
            ];

            // Generate body of the repository file
            $repositoryContent = str_replace(
                $repositoryStubValues,
                $repositoryValues,
                $repositoryStub);

            if (in_array($repositoryFile, $existingRepositoryFiles)) {
                if ($this->override) {
                    $this->writeFile($repositoryFile, $repositoryContent);
                    $this->info('Overridden repository file: '.$repository);
                }
            } else {
                $this->writeFile($repositoryFile, $repositoryContent);
                $this->info('Created repository file: '.$repository);
            }

            // Current interface file name
            $interfaceFile = $this->interfacePath($interface.'.php');

            // Check main repository file's path to add use
            $useStatementForInterface = false;
            if (dirname($interfaceFile) !== dirname(config('repository-generator.main_interface_file'))
            ) {
                $mainInterface = config('repository-generator.main_interface_class');
                $useStatementForInterface = 'use '.$mainInterface.';';
            }

            // Fillable interface values for generating real files
            $interfaceValues = [
                $useStatementForInterface ? $useStatementForInterface : '',
                config('repository-generator.interface_namespace'),
                str_replace('.php', '', config('repository-generator.main_interface_file')),
                $interface,
            ];

            // Generate body of the interface file
            $interfaceContent = str_replace(
                $interfaceStubValues,
                $interfaceValues,
                $interfaceStub);

            if (in_array($interfaceFile, $existingInterfaceFiles)) {
                if ($this->override) {
                    $this->writeFile($interfaceFile, $interfaceContent);
                    $this->info('Overridden interface file: '.$interface);
                }
            } else {
                $this->writeFile($interfaceFile, $interfaceContent);
                $this->info('Created interface file: '.$interface);
            }
        }
    }

    /**
     * Get all model names from models directory.
     *
     * @return array|mixed
     */
    private function getModels()
    {
        $modelDirectory = config('repository-generator.model_directory');
        $models = glob($modelDirectory.'*');
        $models = str_replace([$modelDirectory, '.php'], '', $models);

        return $models;
    }

    /**
     * Get stub content.
     *
     * @param $file
     * @return bool|string
     * @throws StubException
     */
    private function getStub($file)
    {
        $stub = __DIR__.'/../Stubs/'.$file.'.stub';

        if (file_exists($stub)) {
            return file_get_contents($stub);
        }

        throw StubException::fileNotFound($file);
    }

    /**
     * Get repository path.
     *
     * @param null $path
     * @return string
     */
    private function repositoryPath($path = null)
    {
        return config('repository-generator.repository_directory').$path;
    }

    /**
     * Get interface path.
     *
     * @param null $path
     * @return string
     */
    private function interfacePath($path = null)
    {
        return config('repository-generator.interface_directory').$path;
    }

    /**
     * Get parent path of repository of interface folder.
     *
     * @param string $child
     * @return string
     */
    private function parentPath($child = 'repository')
    {
        $childPath = $child.'Path';
        $childPath = $this->$childPath();

        return dirname($childPath);
    }

    /**
     * Generate/override a file.
     *
     * @param $file
     * @param $content
     */
    private function writeFile($file, $content)
    {
        file_put_contents($file, $content);
    }

    /**
     * Check repository folder permissions.
     *
     * @throws FileException
     */
    private function checkRepositoryPermissions()
    {
        // Get full path of repository directory.
        $repositoryPath = $this->repositoryPath();

        // Get parent directory of repository path.
        $repositoryParentPath = $this->parentPath('repository');

        // Check parent of repository directory is writable.
        if (! file_exists($repositoryPath) && ! is_writable($repositoryParentPath)) {
            throw FileException::notWritableDirectory($repositoryParentPath);
        }

        // Check repository directory permissions.
        if (file_exists($repositoryPath) && ! is_writable($repositoryPath)) {
            throw FileException::notWritableDirectory($repositoryPath);
        }
    }

    /**
     * Check interface folder permissions.
     *
     * @throws FileException
     */
    private function checkInterfacePermissions()
    {
        // Get full path of interface directory.
        $interfacePath = $this->interfacePath();

        // Get parent directory of interface path.
        $interfaceParentPath = $this->parentPath('interface');

        // Check parent of interface directory is writable.
        if (! file_exists($interfacePath) && ! is_writable($interfaceParentPath)) {
            throw FileException::notWritableDirectory($interfaceParentPath);
        }

        // Check repository directory permissions.
        if (file_exists($interfacePath) && ! is_writable($interfacePath)) {
            throw FileException::notWritableDirectory($interfacePath);
        }
    }

    private function createFolder($folder)
    {
        if (! file_exists($folder)) {
            mkdir($folder);
        }
    }

    /**
     * Show message and stop script, If there are no model files to work.
     */
    private function noModelsMessage()
    {
        $this->warn('Repository generator has stopped!');
        $this->line(
            'There are no model files to use in directory: "'
            .config('repository-generator.model_directory')
            .'"'
        );
        exit;
    }
}
