<ul class="nav nav-tabs mb-3 justify-content-center">
    <li class="nav-item">
        <a class="nav-link <?php echo Route::current()->getName() == 'quizzes.quizzes.edit' ? 'active' : '' ?>" href="{{ route('quizzes.quizzes.edit', ['id' => $quiz->id])  }}">About</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo Route::current()->getName() == 'quizzes.quizzes.questionnaire' ? 'active' : '' ?>" href="{{ route('quizzes.quizzes.questionnaire', ['id' => $quiz->id])  }}">Questionnaire</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo Route::current()->getName() == 'quizzes.quizzes.settings' ? 'active' : '' ?>" href="{{ route('quizzes.quizzes.settings', ['id' => $quiz->id])  }}">Settings</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo Route::current()->getName() == 'quizzes.quizzes.invitations.index' ? 'active' : '' ?>" href="{{ route('quizzes.quizzes.invitations.index', ['id' => $quiz->id])  }}">Invitations</a>
    </li>
</ul>