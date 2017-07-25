<?php

namespace App;

use App\Concerns\EloquentProxy;
use Illuminate\Database\Eloquent\Model;
use Core\Contracts\Models\User as CoreUserModelContract;
use Core\Contracts\Models\Token as CoreTokenModelContract;

class Token extends Model implements CoreTokenModelContract
{
    use EloquentProxy;

    /**
     * Token belongs to a User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get user that belongs to this token.
     *
     * @return CoreUserModelContract
     */
    public function getUser(): CoreUserModelContract
    {
        return $this->user;
    }
}
