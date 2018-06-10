<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use App\Models\Status;

class StatusesController extends Controller
{
    //微博创建和删除这2个唯一的动作，需要权限
    public function __construct()
    {
        $this->middleware('auth');
    }

    //创建微博
    public function store(Request $request)
    {
        $this->validate($request, [
            'content' => 'required|max:140'
        ]);

        Auth::user()->statuses()->create([
            'content' => $request['content']
        ]);

        return redirect()->back();
    }

    //删除微博
    //使用的是『隐性路由模型绑定』功能，Laravel 会自动查找并注入对应 ID 的实例对象 $status
    public function destroy(Status $status)
    {
        //做删除授权的检测
        $this->authorize('destroy', $status);
        $status->delete();
        session()->flash('success', '微博已被成功删除！');
        return redirect()->back();
    }

}
