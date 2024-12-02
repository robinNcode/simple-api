<?php
namespace App\Controllers;

use OpenApi\Generator;

class DocsController extends BaseController
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

        return view('swagger_docs/index');
    }
}

