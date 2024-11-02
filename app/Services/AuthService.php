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
     * @return  ResponseInterface if authorized, false otherwise
     */
    public function authorize(RequestInterface $request): ResponseInterface
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
                    // Token is invalid
                    $else = 1;
                }
                return $this->failUnauthorized('Invalid token!');
            }
        }

        return $this->respond('Token authorized successfully!');
    }
}
