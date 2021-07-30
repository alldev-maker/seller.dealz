require('./bootstrap');
require('slick-carousel');
require('offline-js');

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
let QuizTimer = {
    duration: window.qm.quiz.duration * 60,
    timeLeft: window.qm.quiz.duration * 60,

    started: false,
    handle: null,

    display: true,

    init: function () {
        this.showTime();

        jQuery('#sidebar-timer').on('show.bs.collapse', function () {
            jQuery('#timer-toggle').text('Hide Timer');
        }).on('hide.bs.collapse', function () {
            jQuery('#timer-toggle').text('Show Timer');
        })
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
            jQuery(".timer .value.hr").html(hours + "<span class=\"unit small\">hr</span>");
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
    },
};
let SlideTimer = {
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
        let that = this;

        that.started = true;
        that.duration = 0;
        that.handle = setInterval(function () {
            that.tick();
        }, 1000);
    },

    stop: function () {
        this.started = false;
        clearInterval(this.handle);
    },
};

let QuizMaster = {
    data: {
        id: window.qm.quiz.id,
        name: '',
        email: '',
        elapsed: 0,
        sections: [],
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
            section: {},
            slide: {
                type: '',
                group: 'init',
                id: '',
                clock: 0,
                duration: 0,
            },
        }
    },
    submitting: false,

    init: function () {
        let that = this;

        QuizTimer.init();

        that.data.current.slide = {
            type: 'home',
            group: 'init',
            id: 0,
            answered: 0,
            clock: PageTimer.time,
            duration: 0,
        };

        jQuery.ajax({
            url: window.qm.quiz.page + '/sections/' + window.qm.sid,
            type: 'GET',
            success: function (data) {
                that.data.sections = data;
                window.qm.testpaper = {
                    'id': window.qm.quiz.id,
                    'sections': []
                }
                that.data.sections.forEach(function (section) {
                    let new_pass = []
                   section.passages.forEach(function (passage) {

                   })
                   window.qm.testpaper.sections.push({
                       'id': section.id,
                       'passages': new_pass
                   })
                });
            }
        });

        SlideTimer.start();

        jQuery(".slides").slick({
            swipe: false,
            infinite: false,
            dots: false,
            prevArrow: false,
            nextArrow: false
        }).on('beforeChange', function (event, slick, prev, index) {
            let slide = jQuery('.slide[data-index="' + index + '"]');

            if (index === 0) {
                jQuery(".buttons .btn-prev").attr('disabled', true);
            } else {
                jQuery(".buttons .btn-prev").attr('disabled', false);
            }

            if (slick.$slides.length === index + slick.options.slidesToScroll) {
                jQuery(".buttons .btn-next").hide();
                if (slide.data('slide-group') === 'init') {
                    jQuery(".buttons .btn-proceed").removeClass('d-none').show();
                } else {
                    jQuery(".buttons .btn-submit").removeClass('d-none').show();
                }
            } else {
                jQuery(".buttons .btn-next").show();
                if (slide.data('slide-group') === 'init') {
                    jQuery(".buttons .btn-proceed").hide();
                } else {
                    jQuery(".buttons .btn-submit").hide();
                }
            }

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

            SlideTimer.stop();
            that.data.current.slide.duration = SlideTimer.duration;
            that.data.logs.slides.push(that.data.current.slide);

            let timeStart = jQuery('.slide[data-index="' + index + '"]').is('[data-qt-start]');
            if (QuizTimer.duration > 0 && !QuizTimer.started && timeStart) {
                QuizTimer.start();
            }
        }).on('afterChange', function (event, slick, index) {
            // Log the slide
            let slide = jQuery('.slide[data-index="' + index + '"]');
            that.data.current.slide = {
                type: slide.data('slide-type'),
                id: slide.data('id'),
                group: slide.data('slide-group'),
                answered: 0,
                clock: PageTimer.time,
                duration: 0,
            };
            SlideTimer.start();
        });

        jQuery(".buttons .btn-prev").on('click', function () {
            jQuery('.slides').slick('slickPrev');
        });

        jQuery(".buttons .btn-next").on('click', function () {
            jQuery('.slides').slick('slickNext');
        });

        jQuery(".buttons .btn-proceed").on('click', function () {
            if (that.data.current.slide.group === 'init') {

            }
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

        // Log the slide
        SlideTimer.stop();
        that.data.current.slide.duration = SlideTimer.duration;
        that.data.logs.slides.push(that.data.current.slide);

        QuizTimer.stop();
        PageTimer.stop();

        that.data.time.end = moment.utc().format('YYYY-MM-DD HH:mm:ss');

        that.submitting = true;

        that.hide.slides();
        that.hide.done();
        that.show.progressBar();

        RRWeb_States.stop = true;

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

        let formData = new FormData();

        formData.append('sid', window.qm.sid);
        formData.append('data', JSON.stringify(that.data));

        that.post_data(formData)
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

    Offline.on('confirmed-down', function () {
        offline_overlay.css('display', 'flex');
    });

    Offline.on('confirmed-up', function () {
        offline_overlay.css('display', 'none');
    });

    PageTimer.init();
    QuizMaster.init();
});