new Vue({
    el: '#component',
    data: {
        name: {
            singular: 'Quiz',
            plural: 'Quizzes'
        },
        loading: false,
        submitting: false,
        url: {
            path: '/quizzes/quizzes'
        },
        quiz: {
            id: '',
            name: '',
            description: '',
        },
        depcheck: {
            checking: false,
            result: {
                flag: null,
                message: null,
                details: {}
            },
        }
    },
    methods: {
        doDepcheck: function () {
            let that = this;

            if (that.quiz.enabled) {
                return;
            }

            that.depcheck.checking = true;
            that.depcheck.result.flag = null;

            this.$bvModal.show('depcheck-modal');

            axios({
                method: 'GET',
                url: that.url.path + '/' + that.quiz.id + '/depcheck'
            }).then(function (data) {
                that.depcheck.result.flag = true;
                that.depcheck.result.message = 'Quiz is ready for deployment.'
                that.depcheck.checking = false;
            }).catch(function (error) {
                that.depcheck.result.flag = false;
                that.depcheck.result.message = 'Quiz is not ready for deployment.'
                that.depcheck.result.reason = error.response.data.reason;
                that.quiz.enabled = false;
                that.depcheck.checking = false;
            });
        },
        submit: function () {
            let that = this;
            that.submitting = true;

            this.$validator.validateAll('quiz').then(function (result) {
                if (result) {
                    that.quiz.action = 'update-settings';

                    axios({
                        method: that.quiz.id !== '' ? 'PUT' : 'POST',
                        url: that.url.path + (that.quiz.id !== '' ? ('/' + that.quiz.id) : ''),
                        data: that.quiz
                    }).then(function () {
                        window.location = '/quizzes/quizzes';
                    }).catch(function (error) {
                        console.log(error);
                        let content = 'Failed to update the form: ';
                        that.$bvToast.toast(content, {
                            title: 'Error',
                            variant: 'danger',
                            solid: true
                        });
                        that.submitting = false;
                    });
                } else {
                    that.submitting = false;
                }
            }).catch(function () {
                let content = 'Validation failed. Please check the form.';
                that.$bvToast.toast(content, {
                    title: 'Error',
                    variant: 'danger',
                    solid: true
                });
                that.submitting = false;
            });
        }
    },
    beforeMount: function () {
        let that = this;

        that.quiz = Quiz;
    }
});
