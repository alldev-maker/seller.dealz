require('chart.js');
require('chartjs-plugin-crosshair');

//require('apexcharts');
//require('vue-apexcharts');

//import VueApexCharts from 'vue-apexcharts'

//Vue.component('apexchart', VueApexCharts);

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
        chart: {
            element: null,
            data: [],
            series: [],
            options: null,
        },
        result: {
            id: '',
        },
    },
    methods: {
        getChartData: function (data) {
            let that = this;

            let customTooltips = function (tooltip) {
                // Tooltip Element
                let tooltipEl = document.getElementById('chartjs-tooltip');

                if (!tooltipEl) {
                    tooltipEl = document.createElement('div');
                    tooltipEl.id = 'chartjs-tooltip';
                    tooltipEl.innerHTML = '<table style="width: 100%" class=""></table>';
                    this._chart.canvas.parentNode.appendChild(tooltipEl);
                }

                // Hide if no tooltip
                if (tooltip.opacity === 0) {
                    tooltipEl.style.opacity = 0;
                    return;
                }

                // Set caret Position
                tooltipEl.classList.remove('above', 'below', 'no-transform');
                if (tooltip.yAlign) {
                    tooltipEl.classList.add(tooltip.yAlign);
                } else {
                    tooltipEl.classList.add('no-transform');
                }

                // Set Text
                if (tooltip.body) {
                    let emotionsIndex = [6, 7, 8, 9, 10, 11];

                    let titleLines = tooltip.title || [];

                    let innerHtml = '<thead>';

                    titleLines.forEach(function (title) {
                        innerHtml += '<tr><th style="text-align: center">' + title.replace('1970-01-01 ', '') + '</th></tr>';
                    });
                    innerHtml += '</thead><tbody>';

                    tooltip.dataPoints.forEach(function (item, i) {
                        if (item.datasetIndex === 0) { // Slide
                            let type = config.data.datasets[item.datasetIndex].data[item.index].type;

                            if (type !== '') {
                                innerHtml += '<tr><td style="padding-top: 1rem; padding-bottom: 0;"><strong>Slide</strong></td></tr>';
                                innerHtml += '<tr><td style="padding-bottom: 0">Type: ' + type + '</tr>';
                                switch (type) {
                                    case 'Form':
                                        break;
                                    case 'Passage':
                                        innerHtml += '<tr><td style="padding-bottom: 0">Title: ' + config.data.datasets[item.datasetIndex].data[item.index].title + '</tr>';
                                        break;
                                    case 'Question':
                                    default:
                                        innerHtml += '<tr><td style="padding-bottom: 0">Question: ' + config.data.datasets[item.datasetIndex].data[item.index].title + '</tr>';
                                }
                            }

                        } else if (item.datasetIndex === 1) { // Answer
                            //innerHtml += '<tr><td style="padding-bottom: 0"><strong>Answers</strong></td></tr>';

                            //let s = config.data.datasets[item.datasetIndex].data[item.index].s;
                            //let sn = config.data.datasets[item.datasetIndex].data[item.index].sn;
                            //sn = sn.trim() !== '' ? sn.trim() : ('Section ID ' + s);

                            //let words_read = 'Words Read: ' + config.data.datasets[item.datasetIndex].data[item.index].sws;
                            //let percentage = 'Percentage: ' + config.data.datasets[item.datasetIndex].data[item.index].swp;

                            //if (config.data.datasets[item.datasetIndex].data[item.index].swt > 0) {
                            //innerHtml += '<tr><td>Name: ' + sn + '<br>' + words_read + '<br>' + percentage + '</td></tr>';
                            //} else {
                            //innerHtml += '<tr><td>Name: ' + sn + '</td></tr>';
                            //}

                            innerHtml += '<tr><td style="padding-top: 1rem; padding-bottom: 0;"><strong>Question</strong></td></tr>';

                            let question = config.data.datasets[item.datasetIndex].data[item.index].q;
                            let answer = config.data.datasets[item.datasetIndex].data[item.index].a;

                            //words_read = 'Words Read: ' + config.data.datasets[item.datasetIndex].data[item.index].qws;
                            //percentage = 'Percentage: ' + config.data.datasets[item.datasetIndex].data[item.index].qwp;

                            let tBlock = '<span style="color: #228b22;">&#10003;</span> ';
                            let fBlock = '<span style="color: #df4145">&#10008;</span> ';

                            let correct = config.data.datasets[item.datasetIndex].data[item.index].c ? tBlock : fBlock;

                            //innerHtml += '<tr><td>Q: ' + question + "<br>A: " + correct + answer + '<br>' + words_read + '<br>' + percentage + '</td></tr>';
                            innerHtml += '<tr><td style="padding-top: 0; padding-bottom: 0;">Question: ' + question + '</td></tr>';
                            innerHtml += '<tr><td style="padding-top: 0; padding-bottom: 0;">Answer: ' + correct + answer + '</td></tr>';

                        } else if (item.datasetIndex === 2) {
                            innerHtml += '<tr><td style="padding-top: 1rem; padding-bottom: 0;"><strong>Biometric Data</strong></td></tr>';

                            let colors = tooltip.labelColors[i];
                            let style = 'background:' + colors.borderColor;
                            style += '; border-color:' + colors.borderColor;
                            style += '; border-width: 2px';
                            let span = '<span class="chartjs-tooltip-key" style="' + style + '"></span>';

                            let label = config.data.datasets[item.datasetIndex].label;
                            let value = item.yLabel;

                            innerHtml += '<tr><td>' + span + label + ': ' + value + '</td></tr>';
                        } else if (item.datasetIndex === 3) {  // Blinks


                            let value = config.data.datasets[item.datasetIndex].data[item.index].r;
                            let dur = config.data.datasets[item.datasetIndex].data[item.index].d;

                            if (value > 1) {
                                let colors = tooltip.labelColors[i];

                                let style = 'background:' + colors.backgroundColor;
                                style += '; border-color:' + colors.backgroundColor;
                                style += '; border-width: 2px';
                                let span = '<span class="chartjs-tooltip-key" style="' + style + '"></span>';

                                innerHtml += '<tr><td>' + span + 'Blink Duration' + ': ' + (dur) + ' ms</td></tr>';
                            }
                        } else if (item.datasetIndex === 12) {  // Slouch

                            let value = config.data.datasets[item.datasetIndex].data[item.index].y;

                            if (value > 1) {
                                let colors = tooltip.labelColors[i];

                                let style = 'background:' + colors.backgroundColor;
                                style += '; border-color:' + colors.backgroundColor;
                                style += '; border-width: 2px';
                                let span = '<span class="chartjs-tooltip-key" style="' + style + '"></span>';

                                innerHtml += '<tr><td>' + span + 'Slouch' + ': Yes</td></tr>';
                            }
                        } else if (emotionsIndex.indexOf(item.datasetIndex) >= 0) {  // Emotions

                            let value = config.data.datasets[item.datasetIndex].data[item.index].y;

                            if (value > 1) {
                                let colors = tooltip.labelColors[i];

                                let style = 'background:' + colors.backgroundColor;
                                style += '; border-color:' + colors.backgroundColor;
                                style += '; border-width: 2px';
                                let span = '<span class="chartjs-tooltip-key" style="' + style + '"></span>';

                                innerHtml += '<tr><td>' + span + 'Face - ' + config.data.datasets[item.datasetIndex].label + ' : ' + value + '</td></tr>';
                            }
                        } else {

                            let colors = tooltip.labelColors[i];
                            let style = 'background:' + colors.borderColor;
                            style += '; border-color:' + colors.borderColor;
                            style += '; border-width: 2px';
                            let span = '<span class="chartjs-tooltip-key" style="' + style + '"></span>';

                            let label = config.data.datasets[item.datasetIndex].label;
                            let value = item.yLabel;

                            innerHtml += '<tr><td>' + span + label + ': ' + value + '</td></tr>';

                        }
                    });

                    innerHtml += '</tbody>';

                    let tableRoot = tooltipEl.querySelector('table');
                    tableRoot.innerHTML = innerHtml;
                }

                let positionY = this._chart.canvas.offsetTop;
                let positionX = this._chart.canvas.offsetLeft;

                let halfw = window.innerWidth / 2;

                // Display, position, and set styles for font
                tooltipEl.style.opacity = 1;

                if (tooltip.caretX > halfw) {
                    tooltipEl.style.left = (positionX + tooltip.caretX - (tooltipEl.offsetWidth / 2)) + 'px';
                } else {
                    tooltipEl.style.left = (positionX + tooltip.caretX + (tooltipEl.offsetWidth / 2)) + 'px';
                }

                tooltipEl.style.top = ((positionY + tooltip.caretY) - (tooltipEl.offsetHeight / 2)) + 'px';

                tooltipEl.style.fontFamily = tooltip._bodyFontFamily;
                tooltipEl.style.fontSize = tooltip.bodyFontSize + 'px';
                tooltipEl.style.fontStyle = tooltip._bodyFontStyle;
                tooltipEl.style.padding = tooltip.yPadding + 'px ' + tooltip.xPadding + 'px';
            };

            that.chart.data = data;

            let config = {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            type: 'scatter',
                            label: 'Slides',
                            lineTension: 0,
                            backgroundColor: '#8CBED6',
                            borderColor: '#8CBED6',
                            fill: false,
                            pointStyle: 'rectRounded',
                            pointRadius: 15,
                            pointHoverRadius: 15,
                            showLine: false,
                            data: data.data.slides.slides,
                            pointBackgroundColor: data.data.slides.bgColors,
                            pointBorderColor: data.data.slides.borderColors,
                            yAxisID: "slides"
                        },
                        {
                            type: 'scatter',
                            label: 'Answers',
                            lineTension: 0,
                            backgroundColor: '#228B22',
                            borderColor: '#228B22',
                            fill: false,
                            pointStyle: 'rect',
                            pointRadius: 15,
                            pointHoverRadius: 15,
                            showLine: false,
                            data: data.data.answers.answers,
                            pointBackgroundColor: data.data.answers.bgColors,
                            pointBorderColor: data.data.answers.borderColors,
                            yAxisID: "answer"
                        },
                        {
                            label: 'Pulse Rate',
                            lineTension: 0,
                            backgroundColor: 'rgba(0,0,0,0)',
                            borderColor: '#ff748c',
                            // borderDash: [10,5],
                            pointRadius: 0,
                            pointStyle: 'rect',
                            fill: false,
                            data: data.data.pulse,
                            yAxisID: "pulse"
                        },
                        {
                            type: 'bubble',
                            label: 'Blinks',
                            lineTension: 0,
                            backgroundColor: 'rgba(191,127,191,0.5)',
                            borderColor: '#bf7fbf',
                            data: data.data.blink,
                            yAxisID: "blinks"
                        },
                        {
                            label: 'Dilation - Width',
                            lineTension: 0,
                            backgroundColor: 'rgba(0,0,0,0)',
                            borderColor: '#964B00',
                            pointRadius: 0,
                            pointStyle: 'rect',
                            fill: false,
                            data: data.data.iris.width,
                            yAxisID: "dilation"
                        },
                        {
                            label: 'Dilation - Height',
                            lineTension: 0,
                            backgroundColor: 'rgba(0,0,0,0)',
                            borderColor: '#964B00',
                            borderDash: [10, 5],
                            pointRadius: 0,
                            pointStyle: 'rect',
                            fill: false,
                            data: data.data.iris.height,
                            yAxisID: "dilation"
                        },
                        {
                            type: 'scatter',
                            label: 'Smile',
                            lineTension: 0,
                            pointBackgroundColor: '#eed202',
                            backgroundColor: '#eed202',
                            borderColor: '#000000',
                            pointStyle: 'rectRot',
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            showLine: false,
                            fill: false,
                            data: data.data.emotions[3],
                            yAxisID: "certainty"
                        },
                        {
                            type: 'scatter',
                            label: 'Frown',
                            lineTension: 0,
                            pointBackgroundColor: '#0000ff',
                            backgroundColor: '#0000ff',
                            borderColor: '#000000',
                            pointStyle: 'rectRot',
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            fill: false,
                            showLine: false,
                            data: data.data.emotions[4],
                            yAxisID: "certainty"
                        },
                        {
                            type: 'scatter',
                            label: 'Raised Eyebrows',
                            lineTension: 0,
                            pointBackgroundColor: '#ffa500',
                            backgroundColor: '#ffa500',
                            //borderDash: [10,5],
                            borderColor: '#000000',
                            pointStyle: 'rectRot',
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            fill: false,
                            showLine: false,
                            data: data.data.emotions[5],
                            yAxisID: "certainty"
                        },
                        {
                            type: 'scatter',
                            label: 'Pinched Eyebrows',
                            lineTension: 0,
                            pointBackgroundColor: '#ff0000',
                            backgroundColor: '#ff0000',
                            //borderDash: [10,5],
                            borderColor: '#000000',
                            pointStyle: 'rectRot',
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            fill: false,
                            showLine: false,
                            data: data.data.emotions[0],
                            yAxisID: "certainty"
                        },
                        {
                            type: 'scatter',
                            label: 'Eyes Wide',
                            lineTension: 0,
                            pointBackgroundColor: '#cccccc',
                            backgroundColor: '#cccccc',
                            borderColor: '#000000',
                            pointStyle: 'rectRot',
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            fill: false,
                            showLine: false,
                            data: data.data.emotions[2],
                            yAxisID: "certainty"
                        },
                        {
                            type: 'scatter',
                            label: 'Relaxed',
                            lineTension: 0,
                            pointBackgroundColor: '#009900',
                            backgroundColor: '#009900',
                            //borderDash: [10,5],
                            borderColor: '#000000',
                            pointStyle: 'rectRot',
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            showLine: false,
                            data: data.data.emotions[6],
                            yAxisID: "certainty"
                        },
                        {
                            type: 'scatter',
                            label: 'Slouch',
                            lineTension: 0,
                            pointBackgroundColor: '#9bc2cf',
                            backgroundColor: '#9bc2cf',
                            borderColor: '#000000',
                            pointStyle: 'rect',
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            showLine: false,
                            data: data.data.slouch,
                            yAxisID: "slouch"
                        },
                    ]
                },
                options: {
                    bezierCurve: false,
                    legend: {
                        position: 'right',
                        labels: {
                            usePointStyle: true
                        }
                    },
                    plugins: {
                        crosshair: {
                            sync: {
                                enabled: false
                            },
                            callbacks: {
                                afterZoom: function (start, end) {
                                    that.chart.element.data.datasets[0].data = that.chart.data.data.slides.slides;
                                    that.chart.element.data.datasets[0].pointBackgroundColor = that.chart.data.data.slides.bgColors;
                                    that.chart.element.data.datasets[0].pointBorderColor = that.chart.data.data.slides.borderColors;
                                    that.chart.element.data.datasets[1].data = that.chart.data.data.answers.answers;
                                    that.chart.element.data.datasets[1].pointBackgroundColor = that.chart.data.data.answers.bgColors;
                                    that.chart.element.data.datasets[1].pointBorderColor = that.chart.data.data.answers.borderColors;
                                    that.chart.element.data.datasets[2].data = that.chart.data.data.pulse;
                                    that.chart.element.data.datasets[3].data = that.chart.data.data.blink;
                                    that.chart.element.data.datasets[4].data = that.chart.data.data.iris.width;
                                    that.chart.element.data.datasets[5].data = that.chart.data.data.iris.height;
                                    that.chart.element.data.datasets[6].data = that.chart.data.data.emotions[3];
                                    that.chart.element.data.datasets[7].data = that.chart.data.data.emotions[4];
                                    that.chart.element.data.datasets[8].data = that.chart.data.data.emotions[5];
                                    that.chart.element.data.datasets[9].data = that.chart.data.data.emotions[0];
                                    that.chart.element.data.datasets[10].data = that.chart.data.data.emotions[2];
                                    that.chart.element.data.datasets[11].data = that.chart.data.data.emotions[6];
                                    that.chart.element.data.datasets[12].data = that.chart.data.data.slouch;
                                    that.chart.element.update();
                                }
                            }
                        }
                    },
                    tooltips: {
                        enabled: false,
                        mode: 'index',
                        position: 'nearest',
                        intersect: true,
                        tooltipFormat: 'mm:ss',
                        custom: customTooltips
                    },
                    scales: {
                        xAxes: [{
                            type: 'time',
                            time: {
                                distribution: 'series',
                                displayFormats: {
                                    'millisecond': 'mm:ss',
                                    'second': 'mm:ss',
                                    'minute': 'mm:ss',
                                    'hour': 'mm:ss',
                                    'day': 'mm:ss',
                                    'week': 'mm:ss',
                                    'month': 'mm:ss',
                                    'quarter': 'mm:ss',
                                    'year': 'mm:ss'
                                }
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Duration'
                            }
                        }],
                        yAxes: [
                            {
                                display: false,
                                id: "slides",
                                ticks: {
                                    beginAtZero: true,
                                    min: 0,
                                    max: 100
                                }
                            },
                            {
                                display: false,
                                id: "answer",
                                ticks: {
                                    beginAtZero: true,
                                    min: 0,
                                    max: 100
                                }
                            },
                            {
                                display: true,
                                position: "left",
                                id: "pulse",
                                ticks: {
                                    min: 0,
                                    max: 220
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Pulse Rate (BPM)'
                                }
                            },
                            {
                                display: false,
                                position: "right",
                                id: "blinks",
                                ticks: {
                                    beginAtZero: true,
                                    max: 100
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Blinks'
                                }
                            },
                            {
                                display: true,
                                position: "right",
                                id: "dilation",
                                ticks: {
                                    beginAtZero: true
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Dilation'
                                }
                            },
                            {
                                display: true,
                                position: "right",
                                id: "certainty",
                                ticks: {
                                    beginAtZero: true,
                                    min: 0,
                                    max: 100
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Certainty (%)'
                                }
                            },
                            {
                                display: false,
                                position: "right",
                                id: "slouch",
                                ticks: {
                                    beginAtZero: true,
                                    max: 5
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Slouch'
                                }
                            },
                        ]
                    }
                }
            };

            let ctx = document.getElementById('chart').getContext('2d');
            that.chart.element = new Chart(ctx, config);
        },
    },
    beforeMount: function () {
        this.result = window.quizmaster.result;
    },
    mounted: function () {
        let that = this;

        that.loading = true;

        axios
            .get(this.url.path.resource + '/' + that.result.id + '/logs/chart')
            .then(function (response) {
                that.getChartData(response.data);

                that.loading = false;
            });
    }
});
