new Vue({
    el: '#component',
    data: {
        name: {
            singular: 'Test Taker',
            plural: 'Test Takers'
        },
        loading: false,
        submitting: false,
        url: {
            path: '/roster/testtakers'
        },
        testtaker: {
            id: 0,
            name_family: '',
            name_given: '',
            name_middle: '',
            suffix: '',
            user: {
                name: '',
                password: ''
            },
            username: '',
            userpass: ''
        },
        countries: {
            url: '/geography/countries/list',
            items: []
        },
    },
    methods: {
        submit: function () {
            let that = this;
            that.submitting = true;

            this.$validator.validateAll('testtaker').then(function (result) {
                if (result) {
                    axios({
                        method: that.testtaker.id !== '' ? 'PUT' : 'POST',
                        url: that.url.path + (that.testtaker.id !== '' ? ('/' + that.testtaker.id) : ''),
                        data: that.testtaker
                    }).then(function () {
                        window.location = '/roster/testtakers';
                    }).catch(function (error) {
                        console.log(error);
                        let content = 'Failed to submit the form. Reason: ';
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
    created: function () {
        let that = this;
        this.$validator.extend('unique', {
            getMessage: function (field, params, data) {
                return data.error;
            },
            validate: async function (value, args) {
                let params = {
                    field: args[0],
                    value: value,
                    id: that.testtaker.user.id
                };

                let ref = await
                axios.post('/admin/users/exists', params);

                if (ref.data.result === false) {
                    return true;
                } else {
                    console.log(ref.data);
                    return {
                        valid: false,
                        data: {
                            error: ref.data.message
                        }
                    };
                }
            }
        });
    },
    beforeMount: function () {
        let that = this;

        that.testtaker = window.quizmaster.testtaker;

        axios({
            method: 'GET',
            url: that.countries.url + '?l=0'
        }).then(function (data) {
            that.countries.items = data.data;
        });
    }
});
