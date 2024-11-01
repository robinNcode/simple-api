<?php

namespace App\Services;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;

class AuthService
{
    use ResponseTrait;
    /**
     * Protect routes with specific exclusions.
     *
     * @param array $routes Array of routes to protect
     * @param array $options Optional configuration like 'except' for route exclusions
     */
    public function routes(RequestInterface $request, array $routes, array $options = []): ?ResponseInterface
    {
        $path = $request->getPath(); // Current request path
        $except = $options['except'] ?? []; // Excluded routes

        // Check if current route is in excluded routes
        if (in_array($path, $except)) {
            return null; // Allow access to excluded routes
        }

        // Protect the specified routes
        if (in_array($path, $routes) && !$this->authorize($request)) {
            // If unauthorized, respond with a 401 Unauthorized
            return $this->failUnauthorized('Request is Unauthorized!');
        }

        return null; // Allow access if authorized or not in protected routes
    }

    /**
     * Custom authorization logic (e.g., token validation)
     *
     * @param RequestInterface $request
     * @return bool True if authorized, false otherwise
     */
    private function authorize(RequestInterface $request): bool
    {
        $authHeader = $request->getHeader('Authorization');

        // Replace this with actual token validation logic
        return $authHeader && $authHeader->getValue() === 'valid-token-example';
    }
}
