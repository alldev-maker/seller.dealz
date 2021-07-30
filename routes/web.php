<?php

use \Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['verify' => true]);

// Home
Route::get('/', ['as' => 'home', 'uses' => 'Home\HomeController@index'])->middleware('verified');

// Login
Route::get('/login', ['as' => 'login', 'uses' => 'Auth\LoginController@index']);
Route::post('/login', ['as' => 'login', 'uses' => 'Auth\LoginController@login']);

// Logout
Route::get('/logout', ['as' => 'logout', 'uses' => 'Auth\LoginController@logout']);
Route::post('/logout', ['as' => 'logout', 'uses' => 'Auth\LoginController@logout']);

// Register
Route::group(['prefix' => 'register', 'as' => 'register.'], function () {
    Route::get('/', ['as' => 'index', 'uses' => 'Auth\RegisterController@index']);
    Route::get('/done', ['as' => 'done', 'uses' => 'Auth\RegisterController@done']);
    Route::get('/countries', ['as' => 'countries', 'uses' => 'Geography\CountriesController@list']);

    Route::post('/', ['as' => 'index', 'uses' => 'Auth\RegisterController@create']);
    Route::post('/exists', ['as' => 'exists', 'uses' => 'Admin\UsersController@exists']);
});

// Settings
Route::get('/settings', ['as' => 'settings', 'uses' => 'Admin\SettingsController@index']);
Route::post('/settings', ['as' => 'settings', 'uses' => 'Admin\SettingsController@update']);

// Quizzes Module (Frontend)
Route::group(['prefix' => 'qz', 'as' => 'qz.'], function () {
    Route::get('/', ['as' => 'index', 'uses' => 'Quizzes\FrontendController@index']);

    Route::get('/{id}/{name?}', ['as' => 'form', 'uses' => 'Quizzes\FrontendController@form']);
    Route::get('/{id}/sections/{sid}', ['as' => 'sections', 'uses' => 'Quizzes\FrontendController@get_sections']);
    Route::post('/{id}', ['as' => 'submit', 'uses' => 'Quizzes\FrontendController@create']);
    Route::post('/{id}/upload/videos', ['as' => 'submit', 'uses' => 'Quizzes\FrontendController@upload_videos']);
    Route::post('/{id}/upload/websessions', ['as' => 'submit', 'uses' => 'Quizzes\FrontendController@upload_websessions']);
});

// Geography
Route::group(['prefix' => 'geography', 'as' => 'geography.', 'middleware' => ['auth', 'role:admin,developer']], function () {
    Route::get('/', ['as' => 'index', 'uses' => 'Geography\IndexController@index']);

    Route::group(['prefix' => 'index', 'as' => 'index.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'Geography\IndexController@index']);
    });

    // Countries Management
    Route::group(['prefix' => 'countries', 'as' => 'countries.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'Geography\CountriesController@index']);
        Route::post('/', ['as' => 'index', 'uses' => 'Geography\CountriesController@create']);

        Route::get('/list', ['as' => 'list', 'uses' => 'Geography\CountriesController@list']);
        Route::post('/list', ['as' => 'list', 'uses' => 'Geography\CountriesController@create']);
        Route::delete('/list', ['as' => 'list', 'uses' => 'Geography\CountriesController@deleteMultiple']);

        Route::get('/add', ['as' => 'add', 'uses' => 'Geography\CountriesController@form']);
        Route::post('/add', ['as' => 'add', 'uses' => 'Geography\CountriesController@create']);

        Route::get('/{id}', ['as' => 'edit', 'uses' => 'Geography\CountriesController@form']);
        Route::put('/{id}', ['as' => 'edit', 'uses' => 'Geography\CountriesController@update']);
        Route::delete('/{id}', ['as' => 'edit', 'uses' => 'Geography\CountriesController@delete']);
    });
});

// Roster
Route::group(['prefix' => 'roster', 'as' => 'roster.', 'middleware' => ['auth', 'role:admin,developer']], function () {
    Route::get('/', ['as' => 'index', 'uses' => 'Roster\IndexController@index']);

    Route::group(['prefix' => 'index', 'as' => 'index.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'Roster\IndexController@index']);
    });

    // Test Givers Management
    Route::group(['prefix' => 'testgivers', 'as' => 'testgivers.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'Roster\TestgiversController@index']);
        Route::post('/', ['as' => 'index', 'uses' => 'Roster\TestgiversController@create']);

        Route::get('/list', ['as' => 'list', 'uses' => 'Roster\TestgiversController@list']);
        Route::post('/list', ['as' => 'list', 'uses' => 'Roster\TestgiversController@create']);
        Route::delete('/list', ['as' => 'list', 'uses' => 'Roster\TestgiversController@deleteMultiple']);

        Route::get('/add', ['as' => 'add', 'uses' => 'Roster\TestgiversController@form']);
        Route::post('/add', ['as' => 'add', 'uses' => 'Roster\TestgiversController@create']);

        Route::get('/{id}', ['as' => 'form', 'uses' => 'Roster\TestgiversController@form']);
        Route::put('/{id}', ['as' => 'edit', 'uses' => 'Roster\TestgiversController@update']);
        Route::delete('/{id}', ['as' => 'delete', 'uses' => 'Roster\TestgiversController@delete']);
    });

    // Test Takers Management
    Route::group(['prefix' => 'testtakers', 'as' => 'testtakers.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'Roster\TesttakersController@index']);
        Route::post('/', ['as' => 'index', 'uses' => 'Roster\TesttakersController@create']);

        Route::get('/list', ['as' => 'list', 'uses' => 'Roster\TesttakersController@list']);
        Route::post('/list', ['as' => 'list', 'uses' => 'Roster\TesttakersController@create']);
        Route::delete('/list', ['as' => 'list', 'uses' => 'Roster\TesttakersController@deleteMultiple']);

        Route::get('/add', ['as' => 'add', 'uses' => 'Roster\TesttakersController@form']);
        Route::post('/add', ['as' => 'add', 'uses' => 'Roster\TesttakersController@create']);

        Route::get('/{id}', ['as' => 'form', 'uses' => 'Roster\TesttakersController@form']);
        Route::put('/{id}', ['as' => 'edit', 'uses' => 'Roster\TesttakersController@update']);
        Route::delete('/{id}', ['as' => 'delete', 'uses' => 'Roster\TesttakersController@delete']);
    });
});

// Media Manager Module
Route::group(['prefix' => 'mediamanager', 'as' => 'mediamanager.', 'middleware' => ['auth', 'role:admin,developer,testgiver']], function () {
    Route::get('/', ['as' => 'index', 'uses' => 'MediaManager\IndexController@index']);
    Route::get('/modal/{type?}', ['as' => 'modal', 'uses' => 'MediaManager\IndexController@modal']);
    Route::post('/upload', ['as' => 'upload', 'uses' => 'MediaManager\IndexController@upload']);

    Route::group(['prefix' => 'files', 'as' => 'files.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'MediaManager\IndexController@list']);

        Route::get('/list/{type?}', ['as' => 'list', 'uses' => 'MediaManager\IndexController@list']);

        Route::get('/{id}', ['as' => 'form', 'uses' => 'MediaManager\IndexController@view']);
        Route::delete('/{id}', ['as' => 'delete', 'uses' => 'MediaManager\IndexController@delete']);
    });
});

// Quizzes Module (Administration)
Route::group(['prefix' => 'quizzes', 'as' => 'quizzes.', 'middleware' => ['auth', 'role:admin,test-giver,test-taker']], function () {
    Route::get('/', ['as' => 'index', 'uses' => 'Quizzes\IndexController@index']);

    Route::group(['prefix' => 'index', 'as' => 'index.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'Quizzes\IndexController@index']);
    });

    // Quizzes Management
    Route::group(['prefix' => 'testtaker', 'as' => 'testtaker.', 'middleware' => ['role:test-taker']], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'Quizzes\InvitationsController@list']);
    });

    // Quizzes Management
    Route::group(['prefix' => 'quizzes', 'as' => 'quizzes.', 'middleware' => ['role:admin,test-giver']], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'Quizzes\QuizzesController@index']);
        Route::post('/', ['as' => 'index', 'uses' => 'Quizzes\QuizzesController@create']);

        Route::get('/list', ['as' => 'list', 'uses' => 'Quizzes\QuizzesController@list']);
        Route::post('/list', ['as' => 'list', 'uses' => 'Quizzes\QuizzesController@create']);
        Route::delete('/list', ['as' => 'list', 'uses' => 'Quizzes\QuizzesController@deleteMultiple']);

        Route::get('/exists', ['as' => 'exists', 'uses' => 'Quizzes\QuizzesController@exists']);
        Route::post('/exists', ['as' => 'exists', 'uses' => 'Quizzes\QuizzesController@exists']);

        Route::get('/add', ['as' => 'add', 'uses' => 'Quizzes\QuizzesController@form']);
        Route::post('/add', ['as' => 'add', 'uses' => 'Quizzes\QuizzesController@create']);

        Route::group(['prefix' => '{id}'], function () {
            Route::get('/', ['as' => 'view', 'uses' => 'Quizzes\QuizzesController@view']);
            Route::put('/', ['as' => 'edit', 'uses' => 'Quizzes\QuizzesController@update']);
            Route::delete('/', ['as' => 'delete', 'uses' => 'Quizzes\QuizzesController@delete']);

            Route::get('/json', ['as' => 'json', 'uses' => 'Quizzes\QuizzesController@show']);
            Route::get('/edit', ['as' => 'edit', 'uses' => 'Quizzes\QuizzesController@form']);
            Route::get('/questionnaire', ['as' => 'questionnaire', 'uses' => 'Quizzes\QuizzesController@questionnaire']);
            Route::get('/settings', ['as' => 'settings', 'uses' => 'Quizzes\QuizzesController@settings']);
            Route::get('/depcheck', ['as' => 'depcheck', 'uses' => 'Quizzes\QuizzesController@depcheck']);

            Route::group(['prefix' => 'invitations', 'as' => 'invitations.'], function () {
                Route::get('/', ['as' => 'index', 'uses' => 'Quizzes\QuizzesController@invitations']);
                Route::get('/list', ['as' => 'list', 'uses' => 'Quizzes\Quizzes\InvitationsController@list']);
                Route::post('/', ['as' => 'index', 'uses' => 'Quizzes\Quizzes\InvitationsController@create']);
                Route::post('/list', ['as' => 'list', 'uses' => 'Quizzes\Quizzes\InvitationsController@create']);
                Route::delete('/list', ['as' => 'list', 'uses' => 'Quizzes\Quizzes\InvitationsController@deleteMultiple']);

                Route::group(['prefix' => '{iid}'], function () {
                    Route::get('', ['as' => 'view', 'uses' => 'Quizzes\Quizzes\InvitationsController@show']);
                    Route::put('/', ['as' => 'edit', 'uses' => 'Quizzes\Quizzes\InvitationsController@update']);
                    Route::delete('/', ['as' => 'delete', 'uses' => 'Quizzes\Quizzes\InvitationsController@delete']);
                });
            });

            Route::group(['prefix' => 'sections', 'as' => 'sections.'], function () {
                Route::get('/', ['as' => 'index', 'uses' => 'Quizzes\Quizzes\SectionsController@list']);
                Route::post('/', ['as' => 'index', 'uses' => 'Quizzes\Quizzes\SectionsController@create']);
                Route::put('/', ['as' => 'index', 'uses' => 'Quizzes\Quizzes\SectionsController@updateMultiple']);

                Route::group(['prefix' => '{sid}'], function () {
                    Route::get('', ['as' => 'view', 'uses' => 'Quizzes\Quizzes\SectionsController@show']);
                    Route::put('/', ['as' => 'edit', 'uses' => 'Quizzes\Quizzes\SectionsController@update']);
                    Route::delete('/', ['as' => 'delete', 'uses' => 'Quizzes\Quizzes\SectionsController@delete']);

                    Route::group(['prefix' => 'passages', 'as' => 'passages.'], function () {
                        Route::get('/', ['as' => 'index', 'uses' => 'Quizzes\Quizzes\PassagesController@list']);
                        Route::post('/', ['as' => 'index', 'uses' => 'Quizzes\Quizzes\PassagesController@create']);
                        Route::put('/', ['as' => 'index', 'uses' => 'Quizzes\Quizzes\PassagesController@updateMultiple']);

                        Route::get('/list', ['as' => 'list', 'uses' => 'Quizzes\Quizzes\PassagesController@list']);

                        Route::group(['prefix' => '{pid}'], function () {
                            Route::get('/', ['as' => 'view', 'uses' => 'Quizzes\Quizzes\PassagesController@show']);
                            Route::put('/', ['as' => 'edit', 'uses' => 'Quizzes\Quizzes\PassagesController@update']);
                            Route::delete('/', ['as' => 'delete', 'uses' => 'Quizzes\Quizzes\PassagesController@delete']);

                            Route::group(['prefix' => 'questions', 'as' => 'questions.'], function () {
                                Route::get('/', ['as' => 'index', 'uses' => 'Quizzes\Quizzes\QuestionsController@list']);
                                Route::post('/', ['as' => 'index', 'uses' => 'Quizzes\Quizzes\QuestionsController@create']);
                                Route::put('/', ['as' => 'index', 'uses' => 'Quizzes\Quizzes\QuestionsController@updateMultiple']);

                                Route::get('/list', ['as' => 'list', 'uses' => 'Quizzes\Quizzes\QuestionsController@list']);

                                Route::group(['prefix' => '{qid}'], function () {
                                    Route::get('/', ['as' => 'view', 'uses' => 'Quizzes\Quizzes\QuestionsController@show']);
                                    Route::put('/', ['as' => 'edit', 'uses' => 'Quizzes\Quizzes\QuestionsController@update']);
                                    Route::delete('/', ['as' => 'delete', 'uses' => 'Quizzes\Quizzes\QuestionsController@delete']);
                                });
                            });
                        });
                    });
                });
            });
        });

    });

    // Results Management
    Route::group(['prefix' => 'results', 'as' => 'results.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'Quizzes\ResultsController@index']);
        Route::post('/', ['as' => 'index', 'uses' => 'Quizzes\ResultsController@create']);

        Route::get('/list', ['as' => 'list', 'uses' => 'Quizzes\ResultsController@list']);
        Route::delete('/list', ['as' => 'list', 'uses' => 'Quizzes\ResultsController@deleteMultiple']);

        Route::get('/exists', ['as' => 'exists', 'uses' => 'Quizzes\ResultsController@exists']);
        Route::post('/exists', ['as' => 'exists', 'uses' => 'Quizzes\ResultsController@exists']);

        Route::group(['prefix' => '{id}'], function () {
            Route::get('/', ['as' => 'summary', 'uses' => 'Quizzes\ResultsController@summary']);
            Route::get('/eyetracking', ['as' => 'eyetracking', 'uses' => 'Quizzes\ResultsController@eyetracking']);
            Route::get('/tracking', ['as' => 'tracking', 'uses' => 'Quizzes\ResultsController@tracking']);
            Route::get('/session', ['as' => 'session', 'uses' => 'Quizzes\ResultsController@session']);
            Route::get('/chart', ['as' => 'chart', 'uses' => 'Quizzes\ResultsController@chart']);
            Route::get('/video', ['as' => 'video', 'uses' => 'Quizzes\ResultsController@video']);
            Route::get('/answerkey', ['as' => 'answerkey', 'uses' => 'Quizzes\ResultsController@answerkey']);
            Route::get('/timing', ['as' => 'timing', 'uses' => 'Quizzes\ResultsController@timing']);
            Route::get('/scores', ['as' => 'scores', 'uses' => 'Quizzes\ResultsController@scores']);
            Route::get('/download', ['as' => 'download', 'uses' => 'Quizzes\ResultsController@download']);

            Route::put('/', ['as' => 'update', 'uses' => 'Quizzes\ResultsController@update']);

            Route::delete('/', ['as' => 'delete', 'uses' => 'Quizzes\ResultsController@delete']);

            Route::get('/json', ['as' => 'json', 'uses' => 'Quizzes\ResultsController@show']);

            Route::group(['prefix' => 'logs', 'as' => 'logs.'], function () {
                Route::get('/chart', ['as' => 'chart', 'uses' => 'Quizzes\Logs\ChartController@list']);
                Route::get('/summary', ['as' => 'summary', 'uses' => 'Quizzes\Logs\ChartController@summary']);
                Route::get('/pulse', ['as' => 'pulse', 'uses' => 'Quizzes\Logs\PulsesController@list']);
                Route::get('/blinks', ['as' => 'blinks', 'uses' => 'Quizzes\Logs\BlinksController@list']);
                Route::get('/dilation', ['as' => 'dilation', 'uses' => 'Quizzes\Logs\DilationsController@list']);
                Route::get('/emotions', ['as' => 'emotions.index', 'uses' => 'Quizzes\Logs\EmotionsController@list']);
                Route::get('/emotions/{code}', ['as' => 'emotions.code', 'uses' => 'Quizzes\Logs\EmotionsController@list']);
                Route::get('/slouch', ['as' => 'dilation', 'uses' => 'Quizzes\Logs\SlouchesController@list']);
                Route::get('/session', ['as' => 'session', 'uses' => 'Quizzes\Logs\SessionsController@list']);
            });
        });
    });

    // Types Management
    Route::group(['prefix' => 'types', 'as' => 'types.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'Quizzes\Types\IndexController@index']);

        Route::group(['prefix' => 'sections', 'as' => 'sections.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'Quizzes\Types\SectionsController@index']);
            Route::post('/', ['as' => 'index', 'uses' => 'Quizzes\Types\SectionsController@create']);

            Route::get('/list', ['as' => 'list', 'uses' => 'Quizzes\Types\SectionsController@list']);
            Route::post('/list', ['as' => 'list', 'uses' => 'Quizzes\Types\SectionsController@create']);
            Route::delete('/list', ['as' => 'list', 'uses' => 'Quizzes\Types\SectionsController@deleteMultiple']);

            Route::get('/add', ['as' => 'add', 'uses' => 'Quizzes\Types\SectionsController@form']);
            Route::post('/add', ['as' => 'add', 'uses' => 'Quizzes\Types\SectionsController@create']);

            Route::get('/{id}', ['as' => 'edit', 'uses' => 'Quizzes\Types\SectionsController@form']);
            Route::put('/{id}', ['as' => 'edit', 'uses' => 'Quizzes\Types\SectionsController@update']);
            Route::delete('/{id}', ['as' => 'edit', 'uses' => 'Quizzes\Types\SectionsController@delete']);
        });
        Route::group(['prefix' => 'problems', 'as' => 'problems.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'Quizzes\Types\ProblemsController@index']);
            Route::post('/', ['as' => 'index', 'uses' => 'Quizzes\Types\ProblemsController@create']);

            Route::get('/list', ['as' => 'list', 'uses' => 'Quizzes\Types\ProblemsController@list']);
            Route::post('/list', ['as' => 'list', 'uses' => 'Quizzes\Types\ProblemsController@create']);
            Route::delete('/list', ['as' => 'list', 'uses' => 'Quizzes\Types\ProblemsController@deleteMultiple']);

            Route::get('/add', ['as' => 'add', 'uses' => 'Quizzes\Types\ProblemsController@form']);
            Route::post('/add', ['as' => 'add', 'uses' => 'Quizzes\Types\ProblemsController@create']);

            Route::get('/{id}', ['as' => 'edit', 'uses' => 'Quizzes\Types\ProblemsController@form']);
            Route::put('/{id}', ['as' => 'edit', 'uses' => 'Quizzes\Types\ProblemsController@update']);
            Route::delete('/{id}', ['as' => 'edit', 'uses' => 'Quizzes\Types\ProblemsController@delete']);
        });
        Route::group(['prefix' => 'scorings', 'as' => 'scorings.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'Quizzes\Types\ScoringsController@index']);
            Route::post('/', ['as' => 'index', 'uses' => 'Quizzes\Types\ScoringsController@create']);

            Route::get('/list', ['as' => 'list', 'uses' => 'Quizzes\Types\ScoringsController@list']);
            Route::post('/list', ['as' => 'list', 'uses' => 'Quizzes\Types\ScoringsController@create']);
            Route::delete('/list', ['as' => 'list', 'uses' => 'Quizzes\Types\ScoringsController@deleteMultiple']);

            Route::get('/add', ['as' => 'add', 'uses' => 'Quizzes\Types\ScoringsController@form']);
            Route::post('/add', ['as' => 'add', 'uses' => 'Quizzes\Types\ScoringsController@create']);

            Route::get('/{id}', ['as' => 'edit', 'uses' => 'Quizzes\Types\ScoringsController@form']);
            Route::put('/{id}', ['as' => 'edit', 'uses' => 'Quizzes\Types\ScoringsController@update']);
            Route::delete('/{id}', ['as' => 'edit', 'uses' => 'Quizzes\Types\ScoringsController@delete']);
        });
    });

});

// Admin Module
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth', 'role:admin,developer']], function () {
    Route::get('/', ['as' => 'index', 'uses' => 'Admin\IndexController@index']);

    Route::group(['prefix' => 'index', 'as' => 'index.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'Admin\IndexController@index']);
    });

    // Roles Management
    Route::group(['prefix' => 'roles', 'as' => 'roles.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'Admin\RolesController@index']);
        Route::post('/', ['as' => 'index', 'uses' => 'Admin\RolesController@create']);

        Route::get('/list', ['as' => 'list', 'uses' => 'Admin\RolesController@list']);
        Route::post('/list', ['as' => 'list', 'uses' => 'Admin\RolesController@create']);
        Route::delete('/list', ['as' => 'list', 'uses' => 'Admin\RolesController@deleteMultiple']);

        Route::get('/add', ['as' => 'add', 'uses' => 'Admin\RolesController@form']);
        Route::post('/add', ['as' => 'add', 'uses' => 'Admin\RolesController@create']);

        Route::get('/{id}', ['as' => 'edit', 'uses' => 'Admin\RolesController@form']);
        Route::put('/{id}', ['as' => 'edit', 'uses' => 'Admin\RolesController@update']);
        Route::delete('/{id}', ['as' => 'edit', 'uses' => 'Admin\RolesController@delete']);
    });

    // Users Management
    Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'Admin\UsersController@index']);
        Route::post('/', ['as' => 'index', 'uses' => 'Admin\UsersController@create']);

        Route::get('/list', ['as' => 'list', 'uses' => 'Admin\UsersController@list']);
        Route::post('/list', ['as' => 'list', 'uses' => 'Admin\UsersController@create']);
        Route::delete('/list', ['as' => 'list', 'uses' => 'Admin\UsersController@deleteMultiple']);

        Route::get('/exists', ['as' => 'exists', 'uses' => 'Admin\UsersController@exists']);
        Route::post('/exists', ['as' => 'exists', 'uses' => 'Admin\UsersController@exists']);

        Route::get('/add', ['as' => 'add', 'uses' => 'Admin\UsersController@form']);
        Route::post('/add', ['as' => 'add', 'uses' => 'Admin\UsersController@create']);

        Route::get('/{id}', ['as' => 'edit', 'uses' => 'Admin\UsersController@form']);
        Route::put('/{id}', ['as' => 'edit', 'uses' => 'Admin\UsersController@update']);
        Route::delete('/{id}', ['as' => 'delete', 'uses' => 'Admin\UsersController@delete']);
    });
});
