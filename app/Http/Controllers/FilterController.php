<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function get(Request $request)
    {
        $start = new Carbon($request->input('start') ?: 'midnight first day of this month');
        $end = new Carbon($request->input('end') ?: 'midnight last day of this month');
        $limit = $request->input('limit') ?: 200;
        $voteLimit = $request->input('vote_limit') ?: 50;

        $template = $request->input('output') == 'md' ? 'filter-md' : 'filter';
        $contentType = $request->input('output') == 'md' ? 'text-plain' : 'text-html';

        return response()
            ->view($template, [
                'comments' => Comment::filter($start, $end, $limit, $voteLimit),
                'start' => $start,
                'end' => $end,
                'voteLimit' => $voteLimit,
            ])
            ->header('Content-Type', $contentType);
            ;
    }
}
