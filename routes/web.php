<?php

Route::get('/', [ 'as' => 'welcome', 'uses' => 'WelcomeController@get', ]);

Route::group([ 'prefix' => 'p', ], function () {
    Route::get('{reddit_id}', [ 'as' => 'post', 'uses' => 'PostController@get', ]);
    Route::get('{reddit_id}/{comment_id}', [ 'as' => 'comment', 'uses' => 'CommentController@get', ]);
});

Route::get('r/{subreddit}', [ 'as' => 'subreddit', 'uses' => 'SubRedditController@get', ]);
Route::get('u/{name}', [ 'as' => 'user', 'uses' => 'UserController@get', ]);
Route::get('filter', [ 'as' => 'filter', 'uses' => 'FilterController@get', ]);

Route::get('dashboard', [ 'as' => 'dashboard', 'uses' => 'DashboardController@get', ]);
Route::get('connect', [ 'as' => 'connect', 'uses' => 'ConnectController@get', ]);

Route::group([ 'prefix' => 'api/v1' ], function () {
    Route::post('comment/{id}/delete', [
        'as' => 'apiCommentDelete',
        'uses' => 'APIController@postCommentDelete',
    ]);

    Route::post('comment/{id}/enable', [
        'as' => 'apiCommentEnable',
        'uses' => 'APIController@postCommentEnable',
    ]);
});
