require('./bootstrap');
require('slick-carousel');
require('offline-js');

window.rrweb = require('rrweb');
window.DetectRTC = require('detectrtc');
window.RecordRTC = require('recordrtc');

Offline.options = {
    checkOnLoad: true,
    interceptRequests: false,
    reconnect: {
        initialDelay: 1,
        delay: 1
    }
};

let RRWeb_States = {
    ordering: 1,
    stop: false
}

let Calibration = {
    point: 0,
    points: {},

    words: [],
    data: [],
    accuracy: 0,
    capture: 120,

    events: {
        onDone: function () {
        }
    },

    init: function () {
        console.log('Calibration init() started.');

        let that = this;

        let canvas = document.getElementById("canvas-calibration");
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        canvas.style.position = "fixed";
        canvas.style.zIndex = "1040";

        Calibration.clearDots();

        jQuery('.content-loading').hide();
        jQuery('.content-calibration').show();

        jQuery('#btn-calibrate-start').on('click', function () {
            jQuery('#modal-instruction').modal('hide');
            Calibration.showPoints();
        });

        jQuery(".calibration").click(function () { // click event on the calibration buttons

            let id = jQuery(this).attr('id');

            if (!Calibration.points[id]) { // initialises if not done
                Calibration.points[id] = 0;
            }
            Calibration.points[id]++; // increments values

            if (Calibration.points[id] === 5) { //only turn to yellow after 5 clicks
                jQuery(this).css('background-color', 'yellow');
                jQuery(this).prop('disabled', true); //disables the button
                Calibration.point++;
            } else if (Calibration.points[id] < 5) {
                //Gradually increase the opacity of calibration points when click to give some indication to user.
                let opacity = 0.2 * Calibration.points[id] + 0.2;
                jQuery(this).css('opacity', opacity);
            }

            //Show the middle calibration point after all other points have been clicked.
            if (Calibration.point === 8) {
                jQuery("#pt5").show();
            }

            if (Calibration.point >= 9) { // last point is calibrated
                //using jquery to grab every element in Calibration class and hide them except the middle point.
                jQuery(".calibration").hide();
                jQuery("#pt5").show();

                // clears the canvas
                let canvas = document.getElementById("canvas-calibration");
                canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);

                // notification for the measurement process
                jQuery('#modal-calculate').modal({
                    backdrop: 'static',
                    show: true
                });
            }
        });

        jQuery('#btn-calculate-start').on('click', function () {
            jQuery('#modal-calculate').modal('hide');
            webgazer.vars.store_points_var = true; // start storing the prediction points

            Calibration.sleep(5000).then(function () {
                webgazer.vars.store_points_var = false; // stop storing the prediction points

                let past50 = that.getPoints(); // retrieve the stored points
                that.accuracy = that.calculatePrecision(past50);

                for (let i = 0; i < past50[0].length; i++) {
                    let coord = {
                        x: past50[0][i],
                        y: past50[1][i]
                    };

                    that.data.push(coord);
                }

                jQuery('#output-accuracy').html(that.accuracy);

                QuizMaster.data.logs.calibration = that.accuracy;

                jQuery('#modal-result').modal({
                    backdrop: 'static',
                    show: true
                });
            });
        });

        jQuery('#btn-done').on('click', function () {
            jQuery('#modal-result').modal('hide');
            Calibration.clearCanvas(true);

            webgazer.setGazeListener(function (data) {
                if (data == null) {
                    return;
                }

                let elems = document.elementFromPoint(data.x, data.y);

                if (elems && elems.classList.contains("s-text")) {

                    let delta = {
                        passageId: elems.getAttribute('data-passage-id'),
                        questionId: elems.getAttribute('data-question-id'),
                        choiceId: elems.getAttribute('data-choice-id'),
                        wordId: elems.getAttribute('data-word-id'),
                        wordText: elems.textContent
                    };

                    that.words.push(delta);


                    if (elems.getAttribute("highlighted") === "true") {
                        if (window.qm.settings.highlight === 1) {
                            let bg = elems.style.backgroundColor;
                            let rgb = bg.match(/\d+/g);
                            let oppa = parseInt(rgb[1]) - 15; // MJ, you fan of KPOP? :P

                            elems.style.backgroundColor = "rgb(188," + oppa + ", 255)";

                            if (oppa < 180) {
                                elems.style.color = "rgb(255,255,255)";
                            }
                        }

                        let bg = elems.dataset.bgcolor;
                        let rgb = bg.match(/\d+/g);
                        let oppa = parseInt(rgb[1]) - 15; // MJ, you fan of KPOP? :P

                        elems.dataset.bgcolor = "rgb(188," + oppa + ", 255)";

                        if (oppa < 180) {
                            elems.dataset.fgcolor = "rgb(255,255,255)";
                        }

                    } else {
                        elems.setAttribute("highlighted", "true");

                        if (window.qm.settings.highlight === 1) {
                            elems.style.backgroundColor = "rgb(188,255,255)";
                        }

                        elems.dataset.bgcolor = "rgb(188,255,255)";
                        elems.dataset.fgcolor = "rgb(0,0,0)"
                    }

                }

            });

            that.events.onDone();
        });

        jQuery('#btn-recalibrate').on('click', function () {
            jQuery('#modal-result').modal('hide');
            Calibration.clearDots();
            Calibration.clearCanvas();
            Calibration.showPoints()
        });
    },

    onDone: function (callback) {
        this.events.onDone = callback;
    },

    /**
     * Returns the stored tracker prediction points
     *
     * @returns {any[]}
     */
    getPoints: function () {
        let past50 = new Array(2);
        past50[0] = webgazer.vars.xPast50;
        past50[1] = webgazer.vars.yPast50;
        return past50;
    },

    /**
     * This function calculates a measurement for how precise
     * the eye tracker currently is which is displayed to the user
     *
     * @param past50Array
     * @returns {number}
     */
    calculatePrecision: function (past50Array) {
        let windowHeight = jQuery(window).height();
        let windowWidth = jQuery(window).width();

        // Retrieve the last 50 gaze prediction points
        let x50 = past50Array[0];
        let y50 = past50Array[1];

        // Calculate the position of the point the user is staring at
        let staringPointX = windowWidth / 2;
        let staringPointY = windowHeight / 2;

        let precisionPercentages = new Array(this.capture);
        this.calculatePrecisionPercentages(precisionPercentages, windowHeight, x50, y50, staringPointX, staringPointY);
        let precision = this.calculateAverage(precisionPercentages);

        // Return the precision measurement as a rounded percentage
        return Math.round(precision);
    },

    /**
     * Calculate percentage accuracy for each prediction based on distance of
     * the prediction point from the centre point (uses the window height as
     * lower threshold 0%)
     *
     * @param precisionPercentages
     * @param windowHeight
     * @param x50
     * @param y50
     * @param staringPointX
     * @param staringPointY
     */
    calculatePrecisionPercentages: function (precisionPercentages, windowHeight, x50, y50, staringPointX, staringPointY) {
        for (let x = 0; x < this.capture; x++) {
            // Calculate distance between each prediction and staring point
            let xDiff = staringPointX - x50[x];
            let yDiff = staringPointY - y50[x];
            let distance = Math.sqrt((xDiff * xDiff) + (yDiff * yDiff));

            // Calculate precision percentage
            let halfWindowHeight = windowHeight / 2;
            let precision = 0;
            if (distance <= halfWindowHeight && distance > -1) {
                precision = 100 - (distance / halfWindowHeight * 100);
            } else if (distance > halfWindowHeight) {
                precision = 0;
            } else if (distance > -1) {
                precision = 100;
            }

            // Store the precision
            precisionPercentages[x] = precision;
        }
    },

    /**
     *  Calculates the average of all precision percentages calculated
     *
     * @param precisionPercentages
     * @returns {number}
     */
    calculateAverage: function (precisionPercentages) {
        let precision = 0;
        for (let x = 0; x < this.capture; x++) {
            precision += precisionPercentages[x];
        }
        precision = precision / this.capture;
        return precision;
    },

    /**
     * Clear the calibraton canvas.
     *
     * @param hide
     */
    clearCanvas: function (hide) {
        jQuery(".calibration").hide();
        let canvas = document.getElementById("canvas-calibration");
        canvas.getContext('2d').clearRect(0, 0, 0, canvas.height);
        if (hide) {
            canvas.style.display = 'none';
        }
    },

    popInstruction: function () {
        this.clearCanvas();
        jQuery('#modal-instruction').modal('show');
    },

    showPoints: function () { // showcalibrationPoints:
        jQuery(".calibration").show();
        jQuery("#pt5").hide(); // initially hides the middle button
    },

    clearDots: function () {
        window.localStorage.clear();
        jQuery(".calibration")
            .css({
                'background-color': 'red',
                'opacity': 0.2
            })
            .prop('disabled', false);

        this.points = {};
        this.point = 0;
    },

    // sleep function because java doesn't have one,
    // sourced from http://stackoverflow.com/questions/951021/what-is-the-javascript-version-of-sleep
    sleep: function (time) {
        return new Promise(function (resolve) {
            return setTimeout(resolve, time);
        });
    }
};

let VideoRecorder = {
    recorder: null,
    counter: 0,
    isSubmitted: false,
    videoElem: document.getElementById('video-cap'),
    rtc: {
        supported: false,
        hasWebcam: false,
        allowed: false
    },
    events: {
        onClear: function () {
        },
    },
    onClear: function (callback) {
        this.events.onClear = callback;
    },
    init: function () {
        let that = this;

        DetectRTC.load(function () {
            that.rtc.supported = DetectRTC.isWebRTCSupported;
            that.rtc.hasWebcam = DetectRTC.hasWebcam;
            that.rtc.allowed = DetectRTC.isWebsiteHasWebcamPermissions;

            if (!that.rtc.allowed) {
                navigator.mediaDevices.getUserMedia({audio: false, video: true}).then(function () {
                    that.rtc.allowed = true;

                    let allClear = that.rtc.supported && that.rtc.hasWebcam && that.rtc.allowed;

                    if (allClear) {
                        jQuery('#noticeWrap').removeClass('d-none');

                        if (that.events.onClear && typeof that.events.onClear === 'function') {
                            that.events.onClear();
                        }
                    } else {
                        jQuery('#err-webcam').removeClass('d-none');

                        if (!that.rtc.supported) {
                            jQuery('#err-support').removeClass('d-none');
                        }
                        if (!that.rtc.hasWebcam) {
                            jQuery('#err-nocam').removeClass('d-none');
                        }
                        if (!that.rtc.allowed) {
                            jQuery('#err-noperm').removeClass('d-none');
                        }
                    }
                }).catch(function (error) {
                    console.error(error.name);

                    jQuery('#err-webcam').removeClass('d-none');
                    that.rtc.allowed = false;

                    switch (error.name) {
                        case 'NotAllowedError':
                            jQuery('#err-noperm').removeClass('d-none');
                            break;
                        case 'NotReadableError':
                            jQuery('#err-noread').removeClass('d-none');
                            break;
                        case 'AbortError':
                            jQuery('#err-aborted').removeClass('d-none');
                            break;
                        case 'NotFoundError':
                            jQuery('#err-404').removeClass('d-none');
                            break;
                    }
                });
            } else {
                let allClear = that.rtc.supported && that.rtc.hasWebcam && that.rtc.allowed;

                if (allClear) {
                    jQuery('#noticeWrap').removeClass('d-none');

                    if (that.events.onClear && typeof that.events.onClear === 'function') {
                        that.events.onClear();
                    }
                } else {
                    jQuery('#err-webcam').removeClass('d-none');

                    if (!that.rtc.supported) {
                        jQuery('#err-support').removeClass('d-none');
                    }
                    if (!that.rtc.hasWebcam) {
                        jQuery('#err-nocam').removeClass('d-none');
                    }
                    if (!that.rtc.allowed) {
                        jQuery('#err-noperm').removeClass('d-none');
                    }
                }
            }
        });
    },
    capture: function (callback) {
        navigator.mediaDevices.getUserMedia({audio: false, video: true}).then(function (camera) {
            callback(camera);
        }).catch(function (error) {
            console.error(error);
        });
    }
};

let PageTimer = {
    time: 0,

    started: false,
    handle: null,

    init: function () {
        this.start();
    },

    tick: function () {
        if (Offline.state === 'up') {
            this.time = this.time + 1;
        }
    },

    start: function () {
        let that = this;

        that.started = true;
        that.handle = setInterval(function () {
            that.tick();

        }, 1000);
    },

    stop: function () {
        this.started = false;
        clearInterval(this.handle);
    }
};

let QuizTimerOld = {
    duration: 0,
    timeLeft: 0,

    started: false,
    handle: null,

    display: true,

    init: function () {
        this.started = false;

        let jq_sidebar_timer = jQuery('#sidebar-timer');

        jq_sidebar_timer.collapse('show').removeClass('bg-light-danger');
        jQuery('.timer .value').removeClass('text-danger');
        this.showTime();

        jq_sidebar_timer.on('show.bs.collapse', function () {
            jQuery('#timer-toggle').text('Hide Timer');
        }).on('hide.bs.collapse', function () {
            jQuery('#timer-toggle').text('Show Timer');
        })
    },

    onStop: function (callback) {
        if (callback && (typeof callback === 'function')) {
            callback();
        }
    },

    showTime: function () {
        let hours = Math.floor(this.timeLeft / 3600);
        let minutes = Math.floor((this.timeLeft - (hours * 3600)) / 60);
        let seconds = Math.floor((this.timeLeft - (hours * 3600) - (minutes * 60)));

        if (minutes < "10") {
            minutes = "0" + minutes;
        }
        if (seconds < "10") {
            seconds = "0" + seconds;
        }

        if (this.timeLeft === 5) {
            jQuery('#sidebar-timer').collapse('show').addClass('bg-light-danger');
            jQuery('.timer .value').addClass('text-danger');
            this.display = true;
        }

        if (hours > 0) {
            jQuery(".timer .value.hr").html(hours + "<span class=\"unit small\">hr</span>").show();
        } else {
            jQuery(".timer .value.hr").hide();
        }

        jQuery(".timer .value.min").html(minutes + "<span class=\"unit small\">min</span>");
        jQuery(".timer .value.sec").html(seconds + "<span class=\"unit small\">sec</span>");
    },

    tick: function () {
        if (Offline.state === 'up') {
            this.timeLeft = this.timeLeft - 1;

            this.showTime();

            if (this.timeLeft <= 0) {
                this.stop();
            }
        }
    },

    tickNew: function () {

    },

    start: function () {
        let that = this;

        that.started = true;
        that.handle = setInterval(function () {
            that.tick();
        }, 1000);
    },

    stop: function () {
        this.started = false;
        this.onStop();
        clearInterval(this.handle);
    },
};

let QuizTimer = {
    duration: 0,
    time_start: 0,
    time_overall: 0,
    time_left: 0,
    running: false,
    handle: null,

    started: false,
    display: true,

    init: function () {
        this.time_start = 0;
        this.time_overall = 0;

        this.started = false;
        this.running = false;

        let jq_sidebar_timer = jQuery('#sidebar-timer');

        jq_sidebar_timer.collapse('show').removeClass('bg-light-danger');
        jQuery('.timer .value').removeClass('text-danger');
        this.showTime();

        jq_sidebar_timer.on('show.bs.collapse', function () {
            jQuery('#timer-toggle').text('Hide Timer');
        }).on('hide.bs.collapse', function () {
            jQuery('#timer-toggle').text('Show Timer');
        })
    },

    getElapsedTime: function () {
        if (!this.time_start) {
            return 0;
        }

        return Date.now() - this.time_start;
    },

    showTime: function () {
        console.log(this.time_left);
        let hours = Math.floor(this.time_left / 3600);
        let minutes = Math.floor((this.time_left - (hours * 3600)) / 60);
        let seconds = Math.floor((this.time_left - (hours * 3600) - (minutes * 60)));

        if (minutes < "10") {
            minutes = "0" + minutes;
        }
        if (seconds < "10") {
            seconds = "0" + seconds;
        }

        if (this.timeLeft === 5) {
            jQuery('#sidebar-timer').collapse('show').addClass('bg-light-danger');
            jQuery('.timer .value').addClass('text-danger');
            this.display = true;
        }

        if (hours > 0) {
            jQuery(".timer .value.hr").html(hours + "<span class=\"unit small\">hr</span>").show();
        } else {
            jQuery(".timer .value.hr").hide();
        }

        jQuery(".timer .value.min").html(minutes + "<span class=\"unit small\">min</span>");
        jQuery(".timer .value.sec").html(seconds + "<span class=\"unit small\">sec</span>");
    },

    start: function () {
        let that = this;

        if (that.running) {
            return console.error('QuizTimer is already running.');
        }

        that.started = true;
        that.running = true;
        that.time_start = Date.now();

        that.handle = setInterval(function () {
            that.tick();
        }, 200);
    },

    tick: function () {
        this.time_left = this.duration -  Math.round(this.getTime() / 1000);

        this.showTime();

        if (this.time_left <= 0) {
            this.stop();
        }
    },

    getTime () {
        if (!this.time_start) {
            return 0;
        }

        if (this.running) {
            return this.time_overall + this.getElapsedTime();
        }

        return this.time_overall;
    },

    stop: function () {
        if (!this.running) {
            return console.error('QuizTimer is already stopped.');
        }

        this.started = false;
        this.running = false;
        this.time_overall = this.time_overall + this.getElapsedTime();

        clearInterval(this.handle);

        this.onStop();
    },


    onStop: function (callback) {
        if (callback && (typeof callback === 'function')) {
            callback();
        }
    },

};

let SlideTimer = {
    ts_start: 0,
    ts_stop: 0,
    duration: 0,

    started: false,
    handle: null,


    init: function () {

    },

    tick: function () {
        if (Offline.state === 'up') {
            this.duration = this.duration + 1;
        }
    },

    start: function () {
        this.started = true;
        this.ts_start = Date.now();
    },

    stop: function () {
        this.ts_stop = Date.now();
        this.started = false;

        this.duration = this.ts_stop - this.ts_start;
        console.log(this.duration);
    },
};

let QuizMaster = {
    data: {
        id: window.qm.quiz.id,
        name: '',
        email: '',
        elapsed: 0,
        testpaper: {},
        answers: {},
        logs: {
            calibration: 0,
            slides: [],
            answers: [],
            rrweb: [],
            words: []
        },
        time: {
            start: '',
            end: '',
        },
        html: {
            passages: {},
            questions: {},
            choices: {}
        },
        current: {
            index: 0,
            section: {
                id: '-'
            },
            slide: {
                type: '',
                id: '',
                clock: 0,
                duration: 0,
                passage_reread: 0
            },
        }
    },
    submitting: false,

    init: function () {
        let that = this;

        jQuery('.navbox-section').css({ display: 'none' });
        jQuery('.navbox-section[data-section-id="-"]').css({ display: 'grid'});

        that.data.current.slide = {
            type: 'home',
            id: 0,
            answered: 0,
            clock: PageTimer.time,
            duration: 0,
            passage_reread: 0
        };

        SlideTimer.start();

        jQuery(".slides").slick({
            swipe: false,
            infinite: false,
            dots: false,
            prevArrow: false,
            nextArrow: false
        }).on('beforeChange', function (event, slick, prev, index) {
            console.log('Index [' + prev + ']: Event beforeChange start.');
            let slide_current = jQuery('.slide[data-index="' + prev + '"]');
            let slide_next = jQuery('.slide[data-index="' + index + '"]');

            let flag_current = slide_current.data('slide-flag');
            let section_id_current = slide_current.data('section-id');

            jQuery('.btn-navbox[data-index]').removeClass('active');
            jQuery('.btn-navbox[data-index="' + index + '"]').addClass('active');

            jQuery('.slide-passage[data-index="' + index + '"]').mCustomScrollbar({
                axis: 'y',
                theme: 'minimal-dark'
            });

            jQuery('.slide-question[data-index="' + index + '"]').mCustomScrollbar({
                axis: 'y',
                theme: 'minimal-dark'
            });

            let next_passage = slide_next.data('slide-type');

            if (next_passage === 'passage') {
                that.data.current.slide.passage_reread = 1;
            }

            console.log('Index [' + prev + ']: Event beforeChange end.');

        }).on('afterChange', function (event, slick, index) {
            console.log('Index [' + index + ']: Event afterChange start.');

            console.log('Slide timer stop.')
            SlideTimer.stop();
            console.log('Slide timer pushed.');
            that.data.current.slide.duration = SlideTimer.duration;
            that.data.logs.slides.push(that.data.current.slide);

            let slide = jQuery('.slide[data-index="' + index + '"]');

            let jq_btn_prev = jQuery(".buttons .btn-prev");
            let jq_btn_next = jQuery(".buttons .btn-next");
            let jq_btn_start = jQuery(".buttons .btn-start");

            let flag = slide.data('slide-flag');
            let section_id = slide.data('section-id');
            let qt_start = slide.data('qt-start');

            that.data.current.slide = {
                type: slide.data('slide-type'),
                id: slide.data('id'),
                answered: 0,
                clock: PageTimer.time,
                duration: 0,
                passage_reread: 0
            };

            if (that.data.current.section.id !== section_id) {
                that.data.current.section.id = section_id;

                jQuery('.navbox-section').css({ display: 'none' });
                jQuery('.navbox-section[data-section-id="' + section_id + '"]').css({ display: 'grid'});
            }

            if (QuizTimer.time_left === 0 && !QuizTimer.started && section_id !== '-' && flag === 's') {
                console.log('Section ID: ' + section_id)
                QuizTimer.duration = window.qm.sections[section_id].time_limit * 60;
                QuizTimer.time_left = window.qm.sections[section_id].time_limit * 60;
                console.log(QuizTimer.duration, QuizTimer.time_left);
                QuizTimer.init();
                QuizTimer.onStop = function () {
                    console.log('Quiz Timer has been stopped.')
                    let slide = jQuery('.slide[data-section-id="' + that.data.current.section.id + '"][data-slide-flag="e"]');
                    let index = slide.data('index');
                    let succeeding = jQuery('.slide[data-index="' + (index + 1) + '"]');
                    if (succeeding.length > 0) { // Move to next slide
                        console.log('Move to next section.');
                        that.data.current.section = {
                            id: succeeding.data('section-id')
                        }
                        jQuery('.slides').slick('slickGoTo', index + 1, false);

                        jQuery('.navbox-section').css({ display: 'none' });
                        jQuery('.navbox-section[data-section-id="' + succeeding.data('section-id') + '"]').css({ display: 'grid'});
                    } else { // Submit
                        console.log('Slide timer stop.')
                        SlideTimer.stop();

                        that.data.current.slide.duration = SlideTimer.duration;
                        that.data.logs.slides.push(that.data.current.slide);

                        console.log('Submitting...');
                        that.submit();
                    }
                };
            }

            if (!QuizTimer.started && section_id !== '-' && qt_start) {
                console.log('Quiz timer start.');
                QuizTimer.start();
            }

            console.log('Slide timer start.')
            SlideTimer.start();

            if (index === 0) {
                jq_btn_prev.attr('disabled', true);
            } else {
                jq_btn_prev.attr('disabled', false);
            }

            if (flag === 'e' && section_id === '-') {
                jq_btn_start.removeClass('d-none').show();
                jq_btn_next.hide();
            } else if (flag === 'e' && section_id === that.data.current.section.id ) {
                jq_btn_next.attr('disabled', true);
            } else {
                jq_btn_start.hide();
                jq_btn_next.show();
                jq_btn_next.attr('disabled', false);
            }

            if (flag === 's' && section_id !== '-') {
                jq_btn_prev.attr('disabled', true);
            } else {
                jq_btn_prev.attr('disabled', false);
            }

            console.log('Index [' + index + ']: Event afterChange end.');
        });

        jQuery(".buttons .btn-prev").on('click', function () {
            jQuery('.slides').slick('slickPrev');
        });

        jQuery(".buttons .btn-next").on('click', function () {
            jQuery('.slides').slick('slickNext');
        });

        jQuery(".buttons .btn-start").on('click', function () {
            jQuery('.slides').slick('slickNext');
        });

        jQuery(".btn-navbox").on('click', function () {
            let i = jQuery(this).data('index');
            jQuery('.slides').slick('slickGoTo', i);
        });

        jQuery(".choice-input").on('change', function () {
            // Log the answer.
            that.data.current.slide.answered = 1;

            let log = {
                questionId: jQuery(this).data('question-id'),
                choiceId: jQuery(this).attr('value'),
                clock: PageTimer.time
            };
            that.data.logs.answers.push(log);
        });

        jQuery("#quiz").removeClass('d-none').addClass('d-flex').on('submit', function (event) {
            event.preventDefault();
            that.submit();
        });

        that.data.time.start = moment.utc().format('YYYY-MM-DD HH:mm:ss');

        jQuery('#navbox-area').mCustomScrollbar({
            axis: 'y',
            theme: 'minimal-dark'
        });

        jQuery('#btn-upload-done').on('click', function () {
            jQuery('.progress-area').removeClass('d-flex').addClass('d-none');
            jQuery('.done-area').removeClass('d-none').addClass('d-flex');
        });
    },

    show: {
        progressBar: function () {
            jQuery('.progress-area').removeClass('d-none').addClass('d-flex');
        },
        uploadDone: function () {
            jQuery('#upload-done').removeClass('d-none').addClass('d-block');
        },
        done: function () {
            jQuery('.done-area').removeClass('d-none').addClass('d-flex');
        }
    },

    hide: {
        slides: function () {
            jQuery('.slides-area').removeClass('d-block').addClass('d-none');
        },
        progressBar: function () {
            jQuery('.progress-area').removeClass('d-flex').addClass('d-none');
        },
        done: function () {
            jQuery('.done-area').removeClass('d-flex').addClass('d-none');
        }
    },

    submit: function () {
        let that = this;

        PageTimer.stop();

        that.data.time.end = moment.utc().format('YYYY-MM-DD HH:mm:ss');

        that.submitting = true;

        that.hide.slides();
        that.hide.done();
        that.show.progressBar();

        VideoRecorder.recorder.stopRecording(function () {
            RRWeb_States.stop = true;

            let blob = VideoRecorder.recorder.getBlob();
            webgazer.end();

            VideoRecorder.recorder.camera.stop();

            that.data.elapsed = PageTimer.time;
            that.data.name = jQuery('#name').val();
            that.data.email = jQuery('#email').val();
            that.data.target_score = jQuery('#target_score').val();
            that.data.testpaper = window.qm.testpaper;

            jQuery('#quiz .passage-content').each(function () {
                that.data.html.passages[jQuery(this).data('id')] = jQuery(this).html();
            });

            jQuery('#quiz .question-content').each(function () {
                that.data.html.questions[jQuery(this).data('id')] = jQuery(this).html();
            });

            jQuery('#quiz .choice-content').each(function () {
                that.data.html.choices[jQuery(this).data('id')] = jQuery(this).html();
            });

            jQuery("#quiz input.choice-input:checked").each(function () {
                let qid = jQuery(this).data('question-id');
                that.data.answers[qid] = jQuery(this).val();
            });

            that.data.logs.words = Calibration.words;

            let formData = new FormData();

            formData.append('sid', window.qm.sid);
            formData.append('data', JSON.stringify(that.data));

            that.post_data(formData)
        });
    },

    post_data: function (formData) {
        let that = this;

        jQuery.ajax({
            url: window.qm.quiz.page,
            type: 'POST',
            data: formData,
            cache: false,
            enctype: 'multipart/form-data',
            contentType: false,
            processData: false,
            xhr: function () {
                let xhr = new window.XMLHttpRequest();

                // Upload progress start
                xhr.upload.addEventListener("progress", function (event) {
                    if (event.lengthComputable) {
                        let percentComplete = (event.loaded / event.total) * 100;
                        jQuery(".progress-upload .progress-bar").css("width", percentComplete + "%");
                    }
                }, false);
                // Upload progress end

                return xhr;
            },
            success: function () {
                that.submitting = false;
                that.show.uploadDone();
            },
            error: function(event) {
                if (event.statusCode === 0) {
                    console.error('No internet connection.');
                    setTimeout(function() {
                        that.post_data(formData);
                    }, 1000);
                }
            }
        });
    }
};

jQuery(window).on('load', function () {
    let offline_overlay = jQuery('.offline-overlay');

    jQuery('#quiz').on('keyup keypress', function(e) {
        const keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });

    Offline.on('confirmed-down', function () {
        offline_overlay.css('display', 'flex');
    });

    Offline.on('confirmed-up', function () {
        offline_overlay.css('display', 'none');
    });

    webgazer.setRegression('weightedRidge');
    webgazer.setTracker('clmtrackr');
    webgazer.begin();

    webgazer.showVideo(true);
    webgazer.showFaceFeedbackBox(true);
    webgazer.showFaceOverlay(true);
    webgazer.showPredictionPoints(window.qm.settings.showDots);

    function checkIfReady() {
        if (webgazer.isReady()) {
            let canvas = document.getElementById("canvas-calibration");
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            canvas.style.position = 'fixed';
            canvas.style.zIndex = "1040";
        } else {
            setTimeout(checkIfReady, 100);
        }
    }

    setTimeout(checkIfReady, 100);

    PageTimer.init();
    rrweb.record({
        emit(event) {
            if (!RRWeb_States.stop && Offline.state === 'up') {
                QuizMaster.data.logs.rrweb.push({
                    'content': JSON.stringify(event),
                    'ordering': RRWeb_States.ordering
                });
                RRWeb_States.ordering++;
            }
        },
    });

    function save_websession() {
        if (!RRWeb_States.stop) {
            let body = QuizMaster.data.logs.rrweb;
            QuizMaster.data.logs.rrweb = [];

            if (body.length > 0) {
                post_websession(body)
            }
        }
    }

    function post_websession(body) {
        jQuery.ajax({
            url: window.qm.quiz.page + '/upload/websessions',
            type: 'POST',
            data: {
                sid: window.qm.sid,
                rrweb: body
            },
            cache: false,
            error: function(event) {
                if (event.statusCode === 0) {
                    console.error('No internet connection.');
                    setTimeout(function() {
                        post_websession(body);
                    }, 1000);
                }
            }
        });
    }

    setInterval(save_websession, 1000);

    Calibration.onDone(function () {
        jQuery('#webgazerVideoFeed')
            .css({'position': 'absolute'})
            .appendTo('#sidebar-video .wrapper');
        jQuery('#webgazerVideoCanvas')
            .css({'position': 'absolute'})
            .appendTo('#sidebar-video .wrapper');
        jQuery('#webgazerFaceOverlay')
            .css({'position': 'absolute'})
            .appendTo('#sidebar-video .wrapper');
        jQuery('#webgazerFaceFeedbackBox')
            .css({'position': 'absolute'})
            .appendTo('#sidebar-video .wrapper');

        QuizMaster.init();
    });

    VideoRecorder.onClear(function () {
        console.log('Video onClear start.')
        Calibration.init();
        Calibration.popInstruction();
        console.log('Video onClear end.')
    });

    VideoRecorder.init();
    VideoRecorder.capture(function (camera) {
        VideoRecorder.videoElem.srcObject = camera;
        VideoRecorder.recorder = new RecordRTC(camera, {
            type: 'video',
            mimeType: 'video/webm',
            frameRate: {exact: 60},
            timeSlice: 1000,
            ondataavailable: function(blob) {
                if (Offline.state === 'up') {
                    VideoRecorder.counter++;
                    let filename = (VideoRecorder.counter + '').padStart(6, '0')
                    let videoFile = new File([blob], filename + '.webm', {type: 'video/webm'});

                    let formData = new FormData();

                    formData.append('sid', window.qm.sid);
                    formData.append('video', videoFile);

                    post_video(formData)
                }
            },
        });
        VideoRecorder.recorder.startRecording();
        VideoRecorder.recorder.camera = camera;
    });

    function post_video(formData) {
        jQuery.ajax({
            url: window.qm.quiz.page + '/upload/videos',
            type: 'POST',
            data: formData,
            cache: false,
            enctype: 'multipart/form-data',
            contentType: false,
            processData: false,
            error: function(event) {
                if (event.statusCode === 0) {
                    console.error('No internet connection.');
                    setTimeout(function() {
                        post_video(formData);
                    }, 1000);
                }
            }
        });
    }
});

jQuery(window).on('resize', function () {
    let canvas = document.getElementById('canvas-calibration');
    let context = canvas.getContext('2d');
    context.clearRect(0, 0, canvas.width, canvas.height);
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
});
