<?php namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\Users;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use Random\RandomException;
use Redis;
use RedisException;
use ReflectionException;

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="Operations about authentication"
 * )
 * 
 * @OA\GET(
 *     path="/api/v1/login",
 *     tags={"Auth"},
 *     summary="Authentication Controller"
 * )
 */
class AuthController extends BaseController
{
    use ResponseTrait;

    private Users $userModel;
    private Redis $redis;

    public function __construct()
    {
        $this->userModel = new Users();
        $this->redis = new Redis();
    }

    public function loginView(): string
    {
        return view('auth/login');
    }

    /**
     * @OA\POST (
     *     path="/api/v1/login",
     *     tags={"Auth"},
     *     summary="Login Request",
     *     @OA\RequestBody(
     *          request = "application/json",
     *          required=true,
     *          description="User login credentials",
     *          @OA\JsonContent(
     *              required={"email", "password"},
     *              @OA\Property(property="email", type="string", format="email", example="dummyemali@gmail.com"),
     *              @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *    ),
     *    @OA\Response(
     *     response=200,
     *     description="Login successful",
     *     @OA\JsonContent(
     *       @OA\Property(property="token", type="string", example="Bearer:token...."),
     *       @OA\Property(property="expires_in", type="integer", example="3600")
     *    )
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Invalid login credentials"
     *  ),
     *  @OA\Response(
     *     response=401,
     *     description="User already logged in"
     * ),
     * @OA\Response(
     *     response=500,
     *     description="Internal Server error"
     * ),
     * @OA\Response(
     *     response=404,
     *     description="User not found"
     * )
     * )
     */
    public function login(): ResponseInterface
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        // Validate user credentials
        $user = $this->userModel->where('email', $email)->first();

        if ($this->isUserLoggedIn()) {
            return $this->fail('User already logged in!');
        }

        if ($user && ($password == $user['password'])) {
            // Generate token
            $token = bin2hex(random_bytes(64));
            $expiry = 3600; // 1 hour in seconds

            // Save token to Redis with an expiry, using setex to expire the key in seconds
            $this->redis->setex("Bearer:{$token}", $expiry, json_encode([
                'user_id' => $user['id'],
                'email' => $user['email']
            ]));


            // Return the token to the client
            return $this->respond([
                'status' => ResponseInterface::HTTP_OK,
                'token' => $token,
                'expires_in' => $expiry
            ]);
        }

        // Unauthorized response for invalid credentials
        return $this->failUnauthorized("Invalid login credentials!");
    }

    /**
     * Check if the user is already logged in with a valid token.
     * @return bool|string Returns the token if valid, otherwise false.
     */
    public function isUserLoggedIn(): bool|string
    {
        // Check Redis for an existing token for the given user ID
        $headers = $this->request->headers();
        $token = $headers['Authorization']->getValue();

        if ($token) {
            // Optionally, you can add extra checks here for the token's validity
            // For instance, you could decode the token or check its expiration.

            return $token; // Token is valid
        }

        return false; // No valid token found
    }

    /**
     * Log the user out...
     * @return ResponseInterface
     * [GET] /logout
     */
    public function loggedOut()
    {
        return $this->fail('User not logged in. Please log in');
    }

    /**
     * Register a new user view...
     * @return string
     * [GET] /register
     */
    public function registerView()
    {
        return view('auth/register');
    }

    /**
     * Register a new user...
     * @return ResponseInterface
     * [POST] /register
     * @throws ReflectionException
     */
    public function register(): ResponseInterface
    {
        $postedData = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password')
        ];

        if ($this->userModel->insert($postedData)) {
            $data = [
                'status' => ResponseInterface::HTTP_OK,
                'status_type' => 'success',
                'message' => 'User registered successfully!',
            ];
        } else {
            $data = [
                'status' => ResponseInterface::HTTP_NOT_FOUND,
                'status_type' => 'error',
                'message' => 'Failed to register user!'
            ];
        }

        return $this->respond($data);
    }

}
