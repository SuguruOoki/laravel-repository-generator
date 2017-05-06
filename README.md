# Laravel - Repository Generator

[![Packagist](https://img.shields.io/badge/packagist-v0.01-blue.svg)](https://packagist.org/packages/ozanakman/laravel-repository-generator) [![StyleCI](https://styleci.io/repos/90446716/shield?branch=master)](https://styleci.io/repos/90446716)

## About
Repository Generator is a Laravel package that aims to generate repository and interface files for repository pattern. It gives you developing speed by automated operations. You can use this package for both ongoing and new projects.

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

### Repository file provided by RepositoryGenerator (optional use)

This package contains Repository.php which has similar functions to Eloquent. You can basically do something like below when you extend class from Repository.php
``` php
<?php

$magic = $repository->select('id', 'name')
            ->where('name', 'The Flash')
            ->get(); // or ->first();
```

Built-in active() scope
``` php
<?php

$magic = $repository->active()
            ->get();
            
// You can change active column name from config/repository-generator.php
```

**Available Methods** <br>
All listed methods have same usage as Eloquent

| Method        | Usage                                                     
| ------------- | ----------------------------------------------------------
| **select**    | $repo->select('column1,'column2')                         
|               | $repo->select(['column1, 'column2'])                      
| **active**    | $repo->active()->get();                                   
|               | active() is equal to $repo->where('active_column', 1);   
| **where**     | $repo->where('color', 'red')->first();
|               | $repo->where('level', '>',  10)->first();
| **whereIn**   | $repo->whereIn('role', ['moderator', 'admin'])->get();
| **orWhere**   | $repo->orWhere('column', 'value')->first();
| **with**      | $repo->with('relation')->get();
| **count**     | $repo->where('type', 'follower')->count();
| **find**      | $repo->find($id);
| **value**     | $repo->where('id', $id)->value('name');
| **get**       | $repo->get();
| **paginate**  | $repo->paginate(20);
| **create**    | $repo->create(['name' => 'Ozan', 'role' => 'admin']);
| **update**    | $repo->update(['role' => 'moderator'], $id);
| **delete**    | $repo->where('posts', 0)->delete();
|               | $repo->delete($id);
| **destroy**   | $repo->destroy($id);


## Contributing
 
Thank you for considering contributing to the Repository Generator! The contribution guide can be found in the CONTRIBUTING.md
