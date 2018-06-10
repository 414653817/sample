<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * get user avatar
     *
     */
    public function gravatar($size = '100')
    {
       $hash = md5(strtolower(trim($this->attribute['email'])));
       return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    //用户模型创建之前监听,生成令牌
    //boot方法会在用户模型类完成初始化之后进行加载
    public static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            $user->activation_token = str_random(30);
        });
    }
}
