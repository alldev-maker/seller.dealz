/**
 * User Form
 **/

new Vue({
    el: '#application',
    data: {
        name: {
            singular: 'User',
            plural: 'Users'
        },
        loading: false,
        submitting: false,
        url: {
            path: '/admin/users'
        },
        user: {
            id: '',
            name: '',
            email: '',
            nice_name: ''
        },
        roles: {
            url: '/admin/roles/list',
            items: []
        }
    },
    methods: {
        submit: function () {
            var that = this;
            that.submitting = true;

            this.$validator.validateAll('user').then(function (result) {
                if (result) {
                    axios({
                        method: that.user.id !== '' ? 'PUT' : 'POST',
                        url: that.url.path + (that.user.id !== '' ? ('/' + that.user.id) : ''),
                        data: that.user
                    }).then(function () {
                        window.location = '/admin/users';
                    });
                } else {
                    that.submitting = false;
                }
            }).catch(function () {
                that.submitting = false;
            });
        }
    },
    beforeMount: function () {
        var that = this;

        that.user = window.quizmaster.user;

        axios({
            method: 'GET',
            url: that.roles.url + '?l=0'
        }).then(function (data) {
            that.roles.items = data.data;
        });
    }
});
