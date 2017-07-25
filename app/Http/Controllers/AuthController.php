<?php

namespace App\Http\Controllers;

use Core\App as CoreApp;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $app;

    public function __construct(CoreApp $app)
    {
        $this->app = $app;
    }

    /**
     * Handle authentication request.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function login(Request $request)
    {
        return [
            'message' => 'Successfully login.',
            'data' => $this->app->auth()->authenticate(
                $request->input('email'),
                $request->input('password')
            ),
        ];
    }

    /**
     * Handle deauthentication request.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function logout(Request $request)
    {
        $this->app->auth()->logout(
            $request->bearerToken()
        );

        return [
            'message' => 'Successfully logout.',
        ];
    }

    public function forgot(Request $request)
    {
        $user = $this->app->auth()->forgot(
            $request->input('email')
        );

        return [
            'message' => "Email has been sent to {$user->email}.",
            'data' => $user,
        ];
    }

    /**
     * Return authenticated user.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function user(Request $request)
    {
        return [
            'message' => 'Authenticated.',
            'user' => $request->user(),
        ];
    }

    /**
     * Register as customer.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function customerRegistration(Request $request)
    {
        return [
            'message' => 'Successfully registered.',
            'data' => $this->app->auth()->registerCustomer(
                $request->all() // TODO: Should use `Request::only($attributes)`
            ),
        ];
    }
}
