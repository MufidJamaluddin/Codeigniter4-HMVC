# Codeigniter4-HMVC

[![<ORG_NAME>](https://circleci.com/gh/MufidJamaluddin/Codeigniter4-HMVC.svg?style=svg)](https://circleci.com/gh/MufidJamaluddin/Codeigniter4-HMVC)

This is Hierarchical model–view–controller (HMVC) project starter using CodeIgniter4 framework. By HMVC architecture, I hope we could make scalable web application. 

## Prerequisites

1. PHP 7.2 or above
2. Composer version 1.10 or above
3. intl PHP extension (for formatting currency, number and date/time, see [CodeIgniter4 Docs](https://codeigniter4.github.io/userguide/intro/requirements.html) )
4. xdebug PHP extension (for testing purpose only, optional)
5. php_sqlite3 PHP extension (for testing purpose only, very optional)

## How to use

1. Run ```composer create-project mufidjamaluddin/codeigniter4-hmvc```, rename folder codeigniter4-hmvc, goto folder inside.
2. Configure the app by change the ```env``` file.
3. Run ```php spark serve``` for run the app.

## Make new module

1. Create module folder in app/Modules folder (example: app/Module/YourModule).
2. Create Config, Controllers, Models, and Modules by run ```php spark module:create YourModule```. You can see your page in ```http://localhost:8080/YourModule``` by run ```php spark serve --port=8080``` (in the next stage, use ```http://localhost:8080/YourModule/YourController/YourMethod```.
3. Update or Add Controller file and add your methods and test cases in tests folder
4. Update module routes by run ```php spark route:update``` for create/change all module routes
   OR
   ```php spark route:update -m YourModule``` for create/change only one module.
5. Run ```composer test``` for run your test cases (optional, see [CodeIgniter4 Docs](https://codeigniter4.github.io/userguide/testing/index.html) or [PHPUnit Docs](https://phpunit.readthedocs.io/en/9.1/) )

## Notes

Always run ```php spark route:update``` after create or change module, controller, or controller methods except if you want to configure module route manually.

## Command Prompt

### Command route:update parameter

Parameters:
    '-n' = Set module namespace (default App\Modules)
    '-i' = Set route with /index path without parameter (true/false, default true)
    '-m' = Set route one module name to be create/update (app/Modules/YourModuleName)
    '-f' = Set module folder inside app path (default Modules)


Usage command ```php spark route:update -i false -m YourModule```

### Command module:create parameter

Example ```php spark module:create invoice```

First parameter (invoice) is your new module.


### PHPUnit

You can run all of your test cases by run ```composer test```

### Other Command

You can get all command prompt list by run ```php spark list``` and composer command in composer.json > scripts.

## HMVC Structure

### Default Structure

By default, there is the structure of Codeigniter4-HMVC.

```
app
   \Modules
       \{YourModule}
            \Config
                Routes.php
            \Controllers
                BaseController.php
                {YourController}.php
            \Models
                {YourModel}.php
    \Views
        \template
            {YourTemplate}.php
        \{your view module folder}
            {Your View}.php
    ...
    
    \tests
        \unit
            ...
        \integration
            \Modules
                \{YourModule}
                    {YourController}.php
        ...
```

### Custom Structure

You can structuring your module freely, because CodeIgniter4 use PSR4. 
If you want to change the structure of Routes.php in Config Module folder or the structure of Module Controllers, you must change app/Config/Routes.php in HMVC Routing section and modify RouteUpdate.php in app/Commands folder.

## Contribute

You can contribute for extend CodeIgniter4 capabilities or add command prompt for development use by fork this repository. After that, you can make pull request.
