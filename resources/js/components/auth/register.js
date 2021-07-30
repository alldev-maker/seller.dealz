new Vue({
    el: '#component',
    data: {
        name: {
            singular: 'Registration',
            plural: 'Registration'
        },
        loading: false,
        submitting: false,
        url: {
            path: '/register'
        },
        testtaker: {
            family_name: '',
            given_name: '',
            suffix: '',
            nickname: '',
            user: {
                name: '',
                password: ''
            },
        },
        countries: {
            url: '/register/countries',
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
                        method: 'POST',
                        url: that.url.path,
                        data: that.testtaker
                    }).then(function () {
                        window.location = '/register/done';
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
                    axios.post('/register/exists', params);

                if (ref.data.result === false) {
                    return true;
                } else {
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

        axios({
            method: 'GET',
            url: that.countries.url + '?l=0'
        }).then(function (data) {
            that.countries.items = data.data;
        });
    }
});
