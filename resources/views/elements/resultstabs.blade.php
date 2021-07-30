<?php

$routes = [
    [
        'name' => 'Summary',
        'route' => 'quizzes.results.summary'
    ],
    [
        'name' => 'Scores',
        'route' => 'quizzes.results.scores'
    ],
    [
        'name' => 'Eye Tracking',
        'route' => 'quizzes.results.eyetracking'
    ],
    [
        'name' => 'Session',
        'route' => 'quizzes.results.session'
    ],
    [
        'name' => 'Chart',
        'route' => 'quizzes.results.chart'
    ],
    [
        'name' => 'Video',
        'route' => 'quizzes.results.video'
    ],
    [
        'name' => 'Answer Key',
        'route' => 'quizzes.results.answerkey'
    ],
    [
        'name' => 'Timing',
        'route' => 'quizzes.results.timing'
    ],
]

?>
<ul class="nav nav-tabs mb-3 justify-content-center">
    <?php foreach ($routes as $route) : ?>
    <li class="nav-item">
        <a class="nav-link <?php echo Route::current()->getName() ==  $route['route'] ? 'active' : '' ?>"
           href="{{ route($route['route'], ['id' => $result->id])  }}">
            <?php echo $route['name']; ?>
        </a>
    </li>
    <?php endforeach; ?>
</ul>