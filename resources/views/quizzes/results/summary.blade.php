@extends('layout')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('quizzes.index')  }}">Quizzes Management</a></li>
            <li class="breadcrumb-item"><a href="{{ route('quizzes.results.index')  }}">Results</a></li>
            <li class="breadcrumb-item">ID <?php echo $result->id; ?></li>
            <li class="breadcrumb-item active" aria-current="page">Summary</li>
        </ol>
    </nav>
    <div class="container-fluid px-3">
        <div id="component" class="row">
            <div class="col">
                @component('elements.spinner') @endcomponent
                <div class="content" v-cloak>
                    <h1 class="mt-0 mb-3 text-center">Quiz Results</h1>
                    @component('elements.resultstabs', ['result' => $result]) @endcomponent
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h3 class="h5 card-title text-primary m-0">Overall Test Readiness</h3>
                                    <p class="card-text">A combination of overall scores.</p>
                                        <svg width="100%" height="100%" viewBox="0 0 85 100" >
                                            <rect width="85" height="100" class="svg-fill-gray-200" />
                                            <rect width="85" v-bind:height="data.overall.total" class="svg-fill-primary" v-bind:y="100 - data.overall.total" />
                                            <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-size="28" font-weight="bold">@{{ data.overall.total }}%</text>
                                        </svg>
                                </div>
                            </div>
                            <?php if (false) : ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h3 class="h5 card-title text-primary m-0">Time Awareness</h3>
                                    <p class="card-text">Time spent on last 20% of questions in each section.</p>
                                    <div class="text-center py-4">
                                        <span class="display-4 text-monospace">@{{ data.time_awareness.time_spent.human }}</span>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-3">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h3 class="h5 card-title text-primary m-0">Performance by Level</h3>
                                    <table class="table-grit w-100 mt-3" style="border-collapse: collapse">
                                        <tr v-for="level in data.difficulty.all.levels">
                                            <td class="name pr-2 border-right" style="width: 4.5rem">@{{ level.name }}</td>
                                            <td class="p-0">
                                                <div class="progress progress-grit">
                                                    <div class="progress-bar text-right pr-1"
                                                         role="progressbar"
                                                         v-bind:style="{ width: level.percent + '%' }"
                                                         v-bind:aria-valuenow="level.percent"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100">
                                                        @{{ level.percent }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h3 class="h5 card-title text-primary m-0">Grit</h3>
                                    <div class="d-flex justify-content-center">
                                        <radial-progress-bar v-bind:diameter="250"
                                                             v-bind:start-color="'#50A6FF'"
                                                             v-bind:stop-color="'#50A6FF'"
                                                             v-bind:completed-steps="data.grit.grit.percent"
                                                             v-bind:stroke-linecap="'butt'"
                                                             v-bind:stroke-width="30"
                                                             v-bind:total-steps="100">
                                            <p class="mb-0 h3 font-weight-bold">@{{ data.grit.grit.percent }}%</p>
                                        </radial-progress-bar>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-body p-0 d-flex">
                                    <div class="subject-group w-50 border-right border-white">
                                        <h3 class="h6 p-3 m-0 bg-primary text-white">Best Subject</h3>
                                        <ul class="list-unstyled mb-2">
                                            <li class="px-3 pt-2" v-for="subject in data.general_knowledge.comparison.best">
                                                @{{ subject.name }}
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="subject-group w-50">
                                        <h3 class="h6 p-3 m-0 bg-primary text-white">Subject to Improve</h3>
                                        <ul class="list-unstyled mb-2">
                                            <li class="px-3 pt-2" v-for="subject in data.general_knowledge.comparison.worst">
                                                @{{ subject.name }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h3 class="h5 card-title text-primary m-0">Time Awareness</h3>
                                    <?php if (false) : ?>
                                    <p class="card-text">Number of questions answered.</p>
                                    <?php endif; ?>
                                    <div class="d-flex justify-content-center">
                                        <radial-progress-bar v-bind:diameter="250"
                                                             v-bind:start-color="'#50A6FF'"
                                                             v-bind:stop-color="'#50A6FF'"
                                                             v-bind:completed-steps="data.time_awareness.questions_answered.percent"
                                                             v-bind:stroke-linecap="'butt'"
                                                             v-bind:stroke-width="30"
                                                             v-bind:total-steps="100">
                                            <p class="mb-0 h3 font-weight-bold">@{{ data.time_awareness.questions_answered.percent }}%</p>
                                        </radial-progress-bar>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h3 class="h5 card-title text-primary m-0">Vertical Fluidity</h3>
                                    <div class="d-flex justify-content-center">
                                        <radial-progress-bar v-bind:diameter="250"
                                                             v-bind:start-color="'#50A6FF'"
                                                             v-bind:stop-color="'#50A6FF'"
                                                             v-bind:completed-steps="data.vertical_fluidity.vertical_fluidity.percent"
                                                             v-bind:stroke-linecap="'butt'"
                                                             v-bind:stroke-width="30"
                                                             v-bind:total-steps="100">
                                            <p class="mb-0 h3 font-weight-bold">@{{ data.vertical_fluidity.vertical_fluidity.percent }}%</p>
                                        </radial-progress-bar>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h3 class="h5 card-title text-primary m-0">Performance by Subject</h3>
                                    <apexchart type="radar"
                                               height="350"
                                               v-bind:options="charts.gkradar.options"
                                               v-bind:series="charts.gkradar.series">
                                    </apexchart>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h3 class="h5 card-title text-primary m-0">Test Equanimity</h3>
                                    <div class="d-flex justify-content-center">
                                        <radial-progress-bar v-bind:diameter="250"
                                                             v-bind:start-color="'#50A6FF'"
                                                             v-bind:stop-color="'#50A6FF'"
                                                             v-bind:completed-steps="data.equanimity.points.total"
                                                             v-bind:stroke-linecap="'butt'"
                                                             v-bind:stroke-width="30"
                                                             v-bind:total-steps="100">
                                            <p class="mb-0 h3 font-weight-bold">@{{ data.equanimity.points.total }}%</p>
                                        </radial-progress-bar>
                                    </div>
                                </div>
                            </div>
                            <?php if (false) : ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h3 class="h5 card-title text-primary m-0">Equanimity</h3>
                                    <p class="card-text">Pulse and recovery</p>
                                    <div class="text-center py-4" v-if="data.status.pulse > 0">
                                        <span class="display-4 text-monospace">@{{ data.equanimity.recovery.average.human }}</span>
                                    </div>
                                    <div class="text-center" v-else>
                                        <div class="alert alert-info mb-0 small">Pulse reading in progress...</div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if (false) : ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <apexchart type="line"
                                               height="200"
                                               v-if="data.status.pulse > 0"
                                               v-bind:options="charts.pulse.options"
                                               v-bind:series="charts.pulse.series">
                                    </apexchart>
                                    <div class="text-center" v-else>
                                        <div class="alert alert-info mb-0 small">Pulse reading in progress...</div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-3">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h3 class="h5 card-title text-primary m-0">General Knowledge</h3>
                                    <p class="card-text">How you do on each specific subject.</p>
                                    <div class="" v-for="subject in data.general_knowledge.raw">
                                        <p class="font-weight-bold mt-3 mb-1">@{{ subject.name }}</p>
                                        <table class="table-grit w-100" style="border-collapse: collapse">
                                            <tr v-for="probtype in subject.problem_types">
                                                <td class="p-0 pb-1">
                                                    @{{ probtype.name }}
                                                    <div class="progress">
                                                        <div class="progress-bar text-right pr-1"
                                                             role="progressbar"
                                                             v-bind:style="{ width: probtype.percent + '%' }"
                                                             v-bind:aria-valuenow="probtype.percent"
                                                             aria-valuemin="0"
                                                             aria-valuemax="100">
                                                            @{{ probtype.percent }}%
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>window.quizmaster.result = {id: '{{ $result->id }}'};</script>
    <script src="{{ asset('js/components/quizzes/results/summary.js') }}"></script>
@endsection
