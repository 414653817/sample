<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Notifications\ResetPassword;
use Auth;

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

    //user模型中定制消息通知类
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    //在用户模型中，指明一个用户拥有多条微博
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    //获取用户(关注)微信信息
    public function feed()
    {
        //return $this->statuses()->orderBy('created_at', 'desc');
        $user_ids = Auth::user()->followings->pluck('id')->toArray();
        array_push($user_ids, Auth::user()->id);
        return Status::whereIn('user_id', $user_ids)->with('user')->orderBy('created_at', 'desc');
    }


    //获取粉丝(获取用户user_id下的所有粉丝)
    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }
    //获取用户关注人(获取粉丝的所有关注人)
    public function followings()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }

    //关注
    public function follow($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->sync($user_ids, false);
    }

    //取消关注
    public function unfollow($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }

    //判断当前登录的用户 A 是否关注了用户 B
    public function isFollowing($user_id)
    {
        return $this->followings->contains($user_id);
    }
}
