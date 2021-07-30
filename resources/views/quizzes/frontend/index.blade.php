@extends('layout-frontend')

@section('title', 'Test')

@section('stylesheet', asset('/css/frontend.css'))

@section('content')
<form id="quiz" class="h-100 d-flex justify-content-center align-items-center" name="quiz">
    <div class="border border-dark rounded quiz-area w-90 h-90">
        <div class="container-fluid h-100">
            <div id="slides-area" class="row h-100 slides-area">
                <div class="col-9 slides-panel rounded-left d-flex flex-column p-0">
                    <div class="slides w-100 flex-fill">
                        dfsdfsadf
                    </div>

                    <div class="buttons border-top bg-light p-3 d-flex justify-content-between rounded-bottom">
                        <button type="button" class="btn btn-prev btn-info btn-labeled" disabled>
                            <span class="btn-label"><i class="fas fa-fw fa-arrow-left"></i></span> Prev
                        </button>
                        <button type="button" class="btn btn-next btn-info btn-labeled">
                            Next <span class="btn-label btn-label-right"><i class="fas fa-fw fa-arrow-right"></i></span>
                        </button>
                        <button type="submit" class="btn btn-submit btn-info btn-labeled d-none">
                            Submit <span class="btn-label btn-label-right"><i class="fas fa-fw fa-upload"></i></span>
                        </button>
                    </div>
                </div>
                <div class="col-3 slides-sidebar border-left bg-light p-0 rounded-right">
                    <div id="sidebar-controls" class="sidebar-box p-3 border-bottom text-center">
                        <button type="button" id="timer-toggle" class="btn btn-info btn-sm" data-toggle="collapse" data-target="#sidebar-timer">
                            Hide Timer
                        </button>
                    </div>
                    <div id="sidebar-timer" class="sidebar-box p-3 border-bottom collapse show">
                        <div class="timer text-center text-monospace">
                            <span class="value hr h1"><?php echo str_pad(1, 2,'0', STR_PAD_LEFT); ?><span class="unit small">hr</span></span>
                            <span class="value min h1"><?php echo str_pad(0, 2,'0', STR_PAD_LEFT); ?><span class="unit small">min</span></span>
                            <span class="value sec h1">00<span class="unit small">sec</span></span>
                        </div>
                    </div>
                    <div id="sidebar-slidenav" class="sidebar-box p-3">
                        <h2 class="h4 mt-0 mb-3">Navigation</h2>
                        <div id="navbox-area">
                            <div class="navbox-area">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
