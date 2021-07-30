new Vue({
    el: '#component',
    data: {
        name: {
            singular: 'Test Giver',
            plural: 'Test Givers'
        },
        loading: false,
        submitting: false,
        url: {
            path: '/roster/testgivers'
        },
        testgiver: {
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
            var that = this;
            that.submitting = true;

            this.$validator.validateAll('testgiver').then(function (result) {
                if (result) {
                    axios({
                        method: that.testgiver.id !== '' ? 'PUT' : 'POST',
                        url: that.url.path + (that.testgiver.id !== '' ? ('/' + that.testgiver.id) : ''),
                        data: that.testgiver
                    }).then(function () {
                        window.location = '/roster/testgivers';
                    }).catch(function (error) {
                        console.log(error);
                        var content = 'Failed to submit the form. Reason: ';
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
                var content = 'Validation failed. Please check the form.';
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
        var that = this;
        this.$validator.extend('unique', {
            getMessage: function (field, params, data) {
                return data.error;
            },
            validate: async function (value, args) {
                var params = {
                    field: args[0],
                    value: value,
                    id: that.testgiver.user.id
                };

                var ref = await
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
        var that = this;

        that.testgiver = TestGiver;

        axios({
            method: 'GET',
            url: that.countries.url + '?l=0'
        }).then(function (data) {
            that.countries.items = data.data;
        });
    }
});
