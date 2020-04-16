# Codeigniter4-HMVC

This is Hierarchical model–view–controller (HMVC) project starter using CodeIgniter4 framework. By HMVC architecture, I hope we could make scalable web application. 

## How to use

1. Clone github.com/MufidJamaluddin/Codeigniter4-HMVC.git
2. Install dependency by run ```composer install```

## Make new module

1. Create module folder in app/Modules folder (example: app/Module/YourModule).
2. Create Config, Controllers, and Models folder in your module path (example: see existing Admin and Land module)
3. Create new Controller file and add your methods
4. Update module routes by run ```php spark route:update```

## Notes

Always run ```php spark route:update``` after create or change module, controller, or controller methods except if you want to configure module route manually.

## Command Prompt List

You can get all command prompt list by run ```php spark list```

## HMVC Structure

### Default Structure

By default, there is the structure of Codeigniter4-HMVC.

```
app
   \Modules
       \YourModule
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
```

### Custom Structure

You can structuring your module freely, because CodeIgniter4 use PSR4. 
If you want to change the structure of Routes.php in Config Module folder or the structure of Module Controllers, you must change app/Config/Routes.php in HMVC Routing section and modify RouteUpdate.php in app/Commands folder.

## Contribute

You can contribute for extend CodeIgniter4 capabilities or add command prompt for development use by fork this repository. After that, you can make pull request.