<?php

namespace App\Http\Controllers;

use Core\App as CoreApp;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $app;

    public function __construct(CoreApp $app)
    {
        $this->app = $app;
    }

    public function all()
    {
        return [
            'message' => 'Users fetched.',
            'data' => $this->app->user()->all(),
        ];
    }

    public function read($id)
    {
        return ($user = $this->app->user()->find($id))
            ? [
                'message' => 'User fetched.',
                'data' => $user,
            ]
            : response(['message' => 'Not found.'], 404);
    }

    public function create(Request $request)
    {
        return [
            'message' => 'User has been successfully created.',
            'data' => $this->app->user()->create(
                $request->all()
            ),
        ];
    }

    public function update($id, Request $request)
    {
        return [
            'message' => 'User has been successfully updated.',
            'data' => $this->app->user()->update($id, $request->all()),
        ];
    }

    public function delete($id)
    {
        $this->app->user()->delete($id);

        return [
            'message' => 'User deleted.',
        ];
    }
}
