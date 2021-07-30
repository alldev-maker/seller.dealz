
new Vue({
    el: '#component',
    data: {
        name: {
            singular: 'Result',
            plural: 'Results'
        },
        url: {
            path: {
                resource: '/quizzes/results'
            },
        },
        loading: false,
        result: {
            id: ''
        },
        data: {},
        charts: {
            score: {
                element: null,
                data: []
            }
        },
    },
    methods: {
        generateGauge: function (data) {
            let that = this;
            that.charts.score.data = data.scores;
            that.charts.score.data.percentage = Math.round((data.scores.earned / data.scores.total) * 100);
        },
    },
    beforeMount: function () {
        this.result = window.quizmaster.result;
    },
    mounted: function () {
        let that = this;

        that.loading = true;
        axios
            .get(this.url.path.resource + '/' + that.result.id + '/logs/summary')
            .then(function (response) {
                that.data = response.data;
                that.generateGauge(that.data)
                that.loading  = false;
            });
    }
});
