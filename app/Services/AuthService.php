<?php

namespace App\Services;

use App\Config\ApiSecurityConfig;
use App\Models\Users;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Predis\Client;

class AuthService
{
    use ResponseTrait;

    protected ApiSecurityConfig $apiSecurityOptions;
    protected ResponseInterface $response;

    public function __construct(ResponseInterface $response)
    {
        $this->apiSecurityOptions = config(ApiSecurityConfig::class);
        $this->response = $response;
    }

    /**
     * Protect routes with specific exclusions.
     *
     * @param RequestInterface $request
     * @param string $routes Array of routes to protect
     * @param array $options Optional configuration like 'except' for route exclusions
     * @return ResponseInterface|null
     */
    public function routes(RequestInterface $request, string $routes, array $options = []): ?ResponseInterface
    {
        $path = $request->getPath(); // Current request path
        $except = $options['except'] ?? []; // Excluded routes
        //dd($path, $routes, $except);
        // Check if current route is in excluded routes
        if (in_array($path, $except)) {
            return null; // Allow access to excluded routes
        }

        // Protect the specified routes
        if (str_starts_with($path, $routes)) {
            return $this->authorize($request);
        }

        return null; // Allow access if authorized or not in protected routes
    }

    /**
     * Custom authorization logic (e.g., token validation)
     *
     * @param RequestInterface $request
     * @return  ResponseInterface if authorized, false otherwise
     */
    private function authorize(RequestInterface $request): ResponseInterface
    {
        $headers = $request->headers();
        //$token = $headers['Authorization']->getValue();

        // Check if browser is allowed ...
        if ($this->apiSecurityOptions->isCheckUserAgent) {
            $userAgent = $headers['User-Agent']->getValue();

            // Create a regex pattern from the allowed browsers array, joining them with "|"
            $pattern = '/' . implode('|', array_map('preg_quote', $this->apiSecurityOptions->allowedBrowsers)) . '/i';

            // Check if the User-Agent matches any of the allowed browsers
            if (!preg_match($pattern, $userAgent)) {
                return $this->failForbidden('Browser not allowed!');
            }
        }

        // Check if method is allowed ...
        if($this->apiSecurityOptions->isCheckMethod){
            $method = $request->getMethod();
            if(!in_array($method, $this->apiSecurityOptions->allowedMethods)){
                return $this->failForbidden('Method not allowed!');
            }
        }

        if(empty($token)){
            return $this->failUnauthorized('Token is required!');
        }
        else{
            $predis = new Client();
            $users = new Users();

            if(!$predis->exists($token)){
                if($users->validateToken($token)){
                    $predis->set($token, true);
                }
                else{

                }
                return $this->failUnauthorized('Invalid token!');
            }
        }

        return $this->respond('Token authorized successfully!');
    }
}
