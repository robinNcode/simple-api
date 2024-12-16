<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SwaggerCommand extends BaseCommand
{
    protected $group = 'Swagger';
    protected $name = 'swagger:generate';
    protected $description = 'Generates Swagger JSON documentation.';

    public function run(array $params): void
    {
        $outputDir = WRITEPATH . 'swagger';
        $swaggerFile = $outputDir . '/swagger.json';

        if (!is_dir($outputDir)) {
            mkdir($outputDir, 777, true);
        }

        // Run swagger-php to generate the documentation
        exec("php vendor/bin/openapi -o $swaggerFile app");

        CLI::write("Swagger documentation generated at $swaggerFile", 'green');
    }
}
