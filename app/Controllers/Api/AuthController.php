<?php namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\Users;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use Predis\Client;

class AuthController extends BaseController
{
    use ResponseTrait;

    private Users $userModel;

    public function __construct()
    {
        $this->userModel = new Users();
    }

    /**
     * Login view...
     * @return string
     * [GET] /login
     */
    public function loginView(): string
    {
        return view('auth/login');
    }

    /**
     * Log the user in...
     * @return ResponseInterface
     * [POST] /login
     */
    public function login(): ResponseInterface
    {
        $credentials = [
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password')
        ];

        if(auth()->loggedIn()){
            return $this->fail('User is already logged in');
        }

        $loginAttempt = auth()->attempt($credentials);
        if(!$loginAttempt->isOK()){
            return $this->fail('Invalid login credentials');
        }
        else{
            $userData = $this->userModel->find(auth()->id());
            $authToken = $userData->generateAccessToken('default');

            return $this->respond([
                'message' => 'Login successful!',
                'user' => $userData,
                'token' => $authToken->raw_token
            ]);
        }
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
     */
    public function register(): ResponseInterface
    {
        $postedData = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'is_service_registration' => $this->request->getPost('is_service_registration') ?? true
        ];

        // Create a new user entity
        $user = new User();
        $userData = $user->fill($postedData);

        if($this->userModel->save($userData)){
            // Generate token
            $token = $userData->generateAccessToken('default');

            $data = [
                'status' => 'success',
                'message' => 'User registered successfully!',
                'access_token' => $token->raw_token
            ];
        }
        else{
            $data = [
                'status' => 'error',
                'message' => 'Failed to register user!'
            ];
        }

        // if is_service_registration true then save user data on redis server
        if($postedData['is_service_registration']){
            $postedData = [
                'status' => 'success',
                'message' => 'User registered successfully in Redis!',
                'access_token' => $data['access_token'],
            ];

            $redis = new Client();
            $redis_status = $redis->set($postedData['email'], json_encode($postedData));

            if(!$redis_status){
                $data = [
                    'status' => 'error',
                    'message' => 'Failed to save user data in Redis!'
                ];
            }
        }

        return $this->respond($data);
    }

    /**
     * Validate the authentication token in the header by communicating with Redis.
     * Blocks the request if the token is invalid, expired, or missing...
     * [GET] /api/v1/auth/validate
     */
    public function validateRedisToken(){
        dd($this->request->getHeaderLine('Authorization'));
    }

}
