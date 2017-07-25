<?php

namespace App;

use IteratorAggregate;
use App\Concerns\EloquentProxy;
use Illuminate\Database\Eloquent\Model;
use Core\Contracts\Models\User as CoreUserModelContract;

class User extends Model implements CoreUserModelContract
{
    use EloquentProxy;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'sex',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * User may has many tokens.
     */
    public function tokens()
    {
        return $this->hasMany(Token::class);
    }

    /**
     * Get all tokens from a user.
     *
     * @return IteratorAggregate
     */
    public function getTokens(): IteratorAggregate
    {
        return $this->tokens;
    }
}
