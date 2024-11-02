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
     * Custom authorization logic (e.g., token validation)
     *
     * @param RequestInterface $request
     * @return  ResponseInterface|null if authorized, false otherwise
     */
    public function authorize(RequestInterface $request): ?ResponseInterface
    {
        $headers = $request->headers();
        $current_path = $request->getPath();
        if(in_array($current_path, $this->apiSecurityOptions->defaultExclusions)){
            return null;
        }

        // Check if browser is allowed ...
        if ($this->apiSecurityOptions->isCheckUserAgent) {
            $userAgent = $headers['User-Agent']->getValue();
            $allowedBrowsers = $this->apiSecurityOptions->allowedBrowsers;

            // Loop through each allowed browser to see if it matches the user-agent
            $isAllowed = false;
            foreach ($allowedBrowsers as $browser) {
                d($browser, $userAgent);
                if (stripos($userAgent, $browser) !== false) {
                    $isAllowed = true;
                    break;
                }
            }

            if(!$isAllowed){
                return $this->failUnauthorized('Browser is not Allowed!');
            }
        }

        // Check if method is allowed ...
        if($this->apiSecurityOptions->isCheckMethod){
            $method = $request->getMethod();
            if(!in_array($method, $this->apiSecurityOptions->allowedMethods)){
                return $this->failForbidden('Method not allowed!');
            }
        }


        $token = $headers['Authorization']->getValue();
        if(empty($token)){
            return $this->failForbidden('Token is required!');
        }
        else{
            $tokenData = $this->validateToken($request);
            if(!$tokenData){
                return $this->failUnauthorized('Invalid token!');
            }
        }

        return $this->respond('Token authorized successfully!');
    }

    /**
     * Validates the provided token for an incoming request.
     *
     * @param RequestInterface $request
     * @return bool|array Returns the token data if valid, otherwise false.
     */
    public function validateToken(RequestInterface $request): bool|array
    {
        $token = $request->getHeaderLine('Authorization');
        $predis = new Client();

        if (!$token) {
            return false; // No token provided
        }

        // Check if the token exists in Redis
        $tokenData = $predis->get("Bearer:{$token}");

        if (!$tokenData) {
            return false; // Token not found or expired
        }

        // Decode and return the token data
        return json_decode($tokenData, true);
    }
}
