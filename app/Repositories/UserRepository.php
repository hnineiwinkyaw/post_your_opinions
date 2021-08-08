<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\BaseRepository;
use App\Services\RandomStringGenerator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

class UserRepository extends BaseRepository{

    public function __construct(User $user)
    {
        parent::__construct($user);
    }

}