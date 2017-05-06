# Laravel - Repository Generator

[![Packagist](https://img.shields.io/badge/packagist-v0.01-blue.svg)](https://packagist.org/packages/ozanakman/laravel-repository-generator) [![StyleCI](https://styleci.io/repos/90446716/shield?branch=master)](https://styleci.io/repos/90446716)

## About
Repository Generator is a Laravel package that aims to generate repository and interfaces file for repository pattern. It gives you developing speed by automated operations. You can use this package for both ongoing and new projects.

- Highly customizable with simple config
- Overriding option
- Easy to improve

## Installation
You can install the package via Composer:
``` bash
composer require ozanakman/laravel-repository-generator
```

Next, you must install the service provider to `config/app.php`:
```php
'providers' => [
    // ...
    OzanAkman\RepositoryGenerator\RepositoryGeneratorServiceProvider::class,,
];
```

Then, if you want to customize folder names, namespaces, etc... You need to publish config with command:
``` bash
php artisan vendor:publish --provider="OzanAkman\RepositoryGenerator\RepositoryGeneratorServiceProvider" --tag="config"
```

Now you can edit `config/repository-generator.php`

## Usage
You can simply use `repositories:generate` command by terminal:
``` bash
php artisan repositories:generate
```

## Contributing
 
Thank you for considering contributing to the Repository Generator! The contribution guide can be found in the CONTRIBUTING.md
