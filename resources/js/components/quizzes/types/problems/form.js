/**
 * Problem Type Form
 **/

new Vue({
    el: '#type',
    data: {
        name: {
            singular: 'Problem Type',
            plural: 'Problem Types'
        },
        loading: false,
        submitting: false,
        url: {
            path: '/quizzes/types/problems'
        },
        problem: {
            key: '',
            name: '',
        },
    },
    methods: {
        submit: function () {
            let that = this;
            that.submitting = true;

            this.$validator.validateAll('type').then(function (result) {
                if (result) {
                    axios({
                        method: that.problem.key !== '' ? 'PUT' : 'POST',
                        url: that.url.path + (that.problem.key !== '' ? ('/' + that.problem.key) : ''),
                        data: that.problem
                    }).then(function () {
                        window.location = '/quizzes/types/problems';
                    }).catch(function () {
                        that.submitting = false;
                        that.$bvToast.toast('Failed to save ' + that.name.singular + '.', {
                            title: 'Message',
                            variant: 'danger',
                            solid: true
                        });
                    });
                } else {
                    that.submitting = false;
                    that.$bvToast.toast('Failed to save ' + that.name.singular + '.', {
                        title: 'Message',
                        variant: 'danger',
                        solid: true
                    });
                }
            }).catch(function () {
                that.submitting = false;
                that.$bvToast.toast('Failed to save ' + that.name.singular + '.', {
                    title: 'Message',
                    variant: 'danger',
                    solid: true
                });
            });
        }
    },
    beforeMount: function () {
        let that = this;
        that.problem = window.quizmaster.problemtype;
    }
});