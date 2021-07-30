/**
 * Section Type Form
 **/

new Vue({
    el: '#type',
    data: {
        name: {
            singular: 'Section Type',
            plural: 'Section Types'
        },
        loading: false,
        submitting: false,
        url: {
            path: '/quizzes/types/sections'
        },
        section: {
            id: 0,
            name: '',
        },
        components: []
    },
    methods: {
        submit: function () {
            let that = this;
            that.submitting = true;

            this.$validator.validateAll('section').then(function (result) {
                if (result) {
                    axios({
                        method: that.section.id !== '' ? 'PUT' : 'POST',
                        url: that.url.path + (that.section.id !== '' ? ('/' + that.section.id) : ''),
                        data: that.section
                    }).then(function () {
                        window.location = '/quizzes/types/sections';
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
        that.section = window.quizmaster.sectiontype;
    }
});