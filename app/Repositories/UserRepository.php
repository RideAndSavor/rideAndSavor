<?php

namespace App\Repositories;

use App\Contracts\PlatformUserInterface;
use App\Contracts\UserInterface;
use App\DB\Core\Crud;
use App\Http\Requests\TeamInviteDto;
use App\Http\Requests\AuthRequest;
use App\Models\PlatformUser;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserInterface
{

    public function store(string $modelName, array $data)
    {
        $model = app("App\\Models\\{$modelName}");
        return (new Crud($model, $data, null, false, true))->execute();
    }
}
