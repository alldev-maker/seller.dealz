require('apexcharts');
require('vue-apexcharts');

import VueApexCharts from 'vue-apexcharts'

Vue.component('apexchart', VueApexCharts);

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
            gkradar : {
                series: [
                    {
                        name: '',
                        data: [],
                    }
                ],
                options: {
                    chart: {
                        type: 'radar'
                    },
                    title: {
                        text: undefined,
                    },
                    xaxis: {
                        categories: []
                    },
                    yaxis: {
                        show: false,
                        min: 0,
                        max: 100,
                        tickAmount: 5,
                        labels: {
                            show: false,
                        }
                    },
                },
            },
            pulse: {
                series: [
                    {
                        name: '',
                        data: [],
                    }
                ],
                options: {
                    chart: {
                        type: 'line',
                        toolbar: {
                            show: false,
                        },
                        sparkline: {
                            enabled: true,
                        }
                    },
                    title: {
                        text: undefined,
                    },
                    xaxis: {
                        labels: {
                            format: 'HH:mm:ss',
                            datetimeFormatter: {
                                hour: 'HH:mm:ss'
                            },
                        },
                        categories: []
                    },
                    yaxis: {
                        tickAmount: 5
                    },
                },
            },
            questions_complete: {
                
            }
        },
        chart1: {
            element: null,
            data: []
        },
        chart2: {
            element: null,
            data: []
        },
    },
    methods: {
        generateChart: function () {
            let that = this;

            that.charts.gkradar.series[0].data = that.data.performance.type.data;
            that.charts.gkradar.options.xaxis.categories = that.data.performance.type.labels;

            if (that.data.status.pulse > 0) {
                that.charts.pulse.series[0].data = that.data.equanimity.chart.values;
                that.charts.pulse.options.xaxis.categories = that.data.equanimity.chart.labels;
            } else {
                that.charts.pulse.series[0].data = [];
                that.charts.pulse.options.xaxis.categories = [];
            }

        },
        generateGauge: function (data) {
            let that = this;
            that.chart2.data = data.scores;
            that.chart2.data.percentage = Math.round((data.scores.earned / data.scores.total) * 100);
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

                that.generateChart();

                that.loading  = false;
            });
    }
});
