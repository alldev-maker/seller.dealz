const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.copy('node_modules/tinymce/skins', 'public/js/tinymce/skins');

mix
    .js('resources/js/app.js', 'public/js')
    .js('resources/js/login.js', 'public/js')
    .js('resources/js/tinymce/tinymce.js', 'public/js/tinymce')
    .js('resources/js/webgazer/webgazer.js', 'public/js/webgazer')
    .js('resources/js/frontend.js', 'public/js')
    .js('resources/js/frontend-test.js', 'public/js')
    .js('resources/js/guest.js', 'public/js')

    .js('resources/js/components/auth/register.js', 'public/js/components/auth')

    .js('resources/js/components/admin/roles/index.js', 'public/js/components/admin/roles')
    .js('resources/js/components/admin/roles/form.js', 'public/js/components/admin/roles')

    .js('resources/js/components/admin/users/index.js', 'public/js/components/admin/users')
    .js('resources/js/components/admin/users/form.js', 'public/js/components/admin/users')

    .js('resources/js/components/admin/settings/index.js', 'public/js/components/admin/settings')

    .js('resources/js/components/roster/testgivers/index.js', 'public/js/components/roster/testgivers')
    .js('resources/js/components/roster/testgivers/form.js', 'public/js/components/roster/testgivers')
    
    .js('resources/js/components/roster/testtakers/index.js', 'public/js/components/roster/testtakers')
    .js('resources/js/components/roster/testtakers/form.js', 'public/js/components/roster/testtakers')

    .js('resources/js/components/quizzes/quizzes/index.js', 'public/js/components/quizzes/quizzes')
    .js('resources/js/components/quizzes/quizzes/form.js', 'public/js/components/quizzes/quizzes')
    .js('resources/js/components/quizzes/quizzes/view.js', 'public/js/components/quizzes/quizzes')
    .js('resources/js/components/quizzes/quizzes/questionnaire.js', 'public/js/components/quizzes/quizzes')
    .js('resources/js/components/quizzes/quizzes/invitations.js', 'public/js/components/quizzes/quizzes')
    .js('resources/js/components/quizzes/quizzes/settings.js', 'public/js/components/quizzes/quizzes')
    .js('resources/js/components/quizzes/quizzes/testtaker.js', 'public/js/components/quizzes/quizzes')

    .js('resources/js/components/quizzes/results/index.js', 'public/js/components/quizzes/results')
    .js('resources/js/components/quizzes/results/summary.js', 'public/js/components/quizzes/results')
    .js('resources/js/components/quizzes/results/chart.js', 'public/js/components/quizzes/results')
    .js('resources/js/components/quizzes/results/answerkey.js', 'public/js/components/quizzes/results')
    .js('resources/js/components/quizzes/results/session.js', 'public/js/components/quizzes/results')
    .js('resources/js/components/quizzes/results/video.js', 'public/js/components/quizzes/results')
    .js('resources/js/components/quizzes/results/eyetracking.js', 'public/js/components/quizzes/results')
    .js('resources/js/components/quizzes/results/timing.js', 'public/js/components/quizzes/results')
    .js('resources/js/components/quizzes/results/score.js', 'public/js/components/quizzes/results')

    .js('resources/js/components/quizzes/types/sections/index.js', 'public/js/components/quizzes/types/sections')
    .js('resources/js/components/quizzes/types/sections/form.js', 'public/js/components/quizzes/types/sections')
    .js('resources/js/components/quizzes/types/problems/index.js', 'public/js/components/quizzes/types/problems')
    .js('resources/js/components/quizzes/types/problems/form.js', 'public/js/components/quizzes/types/problems')

    .js('resources/js/components/mediamanager/index.js', 'public/js/components/mediamanager')

    .sass('resources/sass/admin.scss', 'public/css')
    .sass('resources/sass/frontend.scss', 'public/css')
    .sass('resources/sass/frontend-tinymce.scss', 'public/css')
    .copy('resources/images', 'public/images');
