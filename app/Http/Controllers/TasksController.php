<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Task;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        if (\Auth::check()) { // 認証済みの場合
            // 認証済みユーザを取得
            $user = \Auth::user();
            // ユーザの投稿の一覧を作成日時の降順で取得
            // （後のChapterで他ユーザの投稿も取得するように変更しますが、現時点ではこのユーザの投稿のみ取得します）
            $tasks = $user->tasks()->orderBy('created_at', 'desc')->paginate(10);

            $data = [
                'user' => $user,
                'tasks' => $tasks,
            ];
        }

        // Welcomeビューでそれらを表示
        return view('welcome', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tasklist = new Task;

        // メッセージ作成ビューを表示
        return view('tasks.create', [
            'tasks' => $tasklist,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'status' => 'required|max:10',   // 追加
            'content' => 'required',
        ]);

         // メッセージを作成
        $tasklist = new Task;
        $tasklist->user_id = Auth::id();
        $tasklist->status = $request->status;
        $tasklist->content = $request->content;
        $tasklist->save();

        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // idの値でメッセージを検索して取得
        $tasklist = Task::findOrFail($id);

        if (\Auth::id() === $tasklist->user_id) {
   
        // メッセージ詳細ビューでそれを表示
        return view('tasks.show', [
            'tasks' => $tasklist,
        ]);
        
        }
        
        // トップページへリダイレクトさせる
        return redirect('/');
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        // idの値でメッセージを検索して取得
        $tasklist = Task::findOrFail($id);

        // メッセージ編集ビューでそれを表示
        return view('tasks.edit', [
            'tasks' => $tasklist,
        ]);
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       // バリデーション
        $request->validate([
            'status' => 'required|max:10',   // 追加
            'content' => 'required',
        ]);
        
        // idの値でメッセージを検索して取得
        $tasklist = Task::findOrFail($id);
        
        if (\Auth::id() === $tasklist->user_id) {
        $tasklist->status = $request->status;
        $tasklist->content = $request->content;
        $tasklist->save();
        }

        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // idの値でメッセージを検索して取得
        $tasklist = Task::findOrFail($id);

        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は、投稿を削除
        if (\Auth::id() === $tasklist->user_id) {
            $tasklist->delete();
        }

        // トップページへリダイレクトさせる
        return redirect('/');
    }
}
