Swagger is a widely used API documentation and testing tool that seamlessly integrates with popular web frameworks like Laravel, Spring Boot, CodeIgniter, and ExpressJS. In this article, we will focus on integrating Swagger with CodeIgniter.
### Installing Dependencies:
```php
composer require zircote/swagger-php doctrine/annotations
```

#### Downloading the SwaggerUI `.zip` or SwaggerUI GitHub repo:

- Download link: [SwaggerUI latest releases](https://github.com/swagger-api/swagger-ui/releases)
  Download the Swagger UI release that best fits your requirements.

### Writing the Controller:
To generate the swagger.json file for Swagger UI we will have to create a controller. Name the controller as your choice we are giving it `SwaggerDocGenerator.php`. In the controller, we will have to use `OpenApi\Generator` from `zircote/swagger-php` to convert all the `@OA` syntax into a JSON.

```php
<?php
namespace App\Controllers;

use OpenApi\Generator;

class SwaggerDocGenerator extends BaseController
{
    /**
     * Generate OpenAPI documentation for the API ...
     * @return string
     */
    public function generate(): string
    {
        // Specify the path where your API controllers are located
        $openapi = Generator::scan([APPPATH . 'Controllers']);

        $swaggerContent = $openapi->toJson();

        // Save the generated OpenAPI content to a file
        $filePath = FCPATH . 'swagger_ui/swagger.json';
        file_put_contents($filePath, $swaggerContent);

        return $swaggerContent;
    }
    
    /**
     * Render the SwaggerUI ...
     * @return string
     */
    public function index()
    {
        return view('swagger_docs/index');
    }
}

?>
```
### Creating Routes:
By creating a route on `Config/Routes.php` we will be able to generate the expected `sawgger.json` file.

```php
// Create API documentation ...
$routes->get('api/v1/docs/generate', 'SwaggerDocGenerator::generate');
$routes->get('api/v1/docs/ui', 'SwaggerDocGenerator::index');
```

### Rendering Swagger UI:

#### There are many ways to render your swagger.json file into SwaggerUI:
- Importing the swagger.json file into the POSTMAN.
- Using the SwaggerUI with your own views to render the SwaggerUI.
- Setting up the environment into your frontend application to render the SwaggerUI by requesting on backend-API for the swagger.json file.

In this, we will see the first two ways. We will talk about the 3rd way in another article.

#### Importing the swagger.json file into the POSTMAN:
- Copy the swagger.json.
- Open the POSTMAN.
- Click on the `Import` button in the top-left corner.
- Paste the copied swagger.json.


#### Using the SwaggerUI with your own views to render the SwaggerUI:
- Create a .php file on your views folder `app/Views/swagger_docs/index.php` to render the SwaggerUI.
- Extract the downloaded SwaggerUI `.zip` file into the `public/swagger_ui/` folder.

```php
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Swagger UI</title>
    <link rel="stylesheet" type="text/css" href="<?= base_url('swagger_ui/swagger-ui.css') ?>" />
    <link rel="stylesheet" type="text/css" href="<?= base_url('swagger_ui/index.css') ?>" />
    <link rel="icon" type="image/png" href="<?= base_url('swagger_ui/favicon-32x32.png') ?>" sizes="32x32" />
    <link rel="icon" type="image/png" href="<?= base_url('swagger_ui/favicon-16x16.png') ?>" sizes="16x16" />
  </head>

  <body>
    <div id="swagger-ui"></div>
    <script src="<?= base_url('swagger_ui/swagger-ui-bundle.js') ?>" charset="UTF-8"> </script>
    <script src="<?= base_url('swagger_ui/swagger-ui-standalone-preset.js') ?>" charset="UTF-8"> </script>
    <script src="<?= base_url('swagger_ui/swagger-initializer.js') ?>" charset="UTF-8"> </script>
  </body>
</html>

```
Now, we have to update the url of `public\swagger_ui\swagger-initializer.js` with the following code.

```js
url: "http://your-local-server/swagger_ui/swagger.json"
```

### Note:
- Make sure to run the `php spark serve` command to run the Codeigniter4 application.
- Every time you make changes in the OpenAPI documentation syntax, you will have to run the `http://localhost:8080/api/v1/docs/generate` URL to generate the updated `swagger.json` file.
- Make sure to update the `swagger-initializer.js` file with the updated URL of the `swagger.json` file.

### Conclusion:
In this article, we explored integrating Swagger with CodeIgniter 4, generating the `swagger.json` file, rendering it in Swagger UI, and importing it into Postman. We also demonstrated how to render Swagger UI within custom views. However, manually generating the `swagger.json` file and updating the URL in the `swagger-initializer.js` file is not ideal.

In the next article, I will demonstrate automating this process using custom CLI commands and aim to develop an open-source package for this purpose. Feel free to share your suggestions or queries in the comments section.