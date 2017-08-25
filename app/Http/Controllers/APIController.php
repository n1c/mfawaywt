<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class APIController extends Controller
{
    public function postCommentDelete(Request $request)
    {
        $comment = Comment::findOrFail($request->id);
        $this->authorize('edit', $comment);

        $comment->disable();

        return response()->json([
            'success' => true,
            'comment' => $comment,
        ]);
    }

    public function postCommentEnable(Request $request)
    {
        $comment = Comment::findOrFail($request->id);
        $this->authorize('edit', $comment);

        $comment->enable();

        return response()->json([
            'success' => true,
            'comment' => $comment,
        ]);
    }
}
