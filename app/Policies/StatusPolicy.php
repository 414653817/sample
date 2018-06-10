<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Status;

class StatusPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //微博删除策略(还需要在 AuthServiceProvider 中对授权策略进行配置才能正常使用)
    public function destroy(User $user, Status $status)
    {
        return $user->id === $status->user_id;
    }
}
