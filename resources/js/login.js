/**
 * User Login
 */

new Vue({
    el: '#component',
    data: {
        url: {
            path: '/login'
        },
        invalid: false,
        login: {
            username: '',
            password: '',
            redirect: '',
            remember: 0
        }
    },
    methods: {
        dismiss: function () {
            this.invalid = false;
        },
        submit: function () {
            var that = this;
            this.$validator.validateAll('login').then(function (result) {
                if (result) {
                    axios({
                        method: 'POST',
                        url: that.url.path,
                        data: that.login
                    }).then(function (response) {
                        if (response.data.result) {
                            window.location = that.login.redirect;
                        } else {
                            that.invalid = true;
                        }
                    });
                }
            })
        }
    },
    beforeMount: function () {
        var urlParams = new URLSearchParams(window.location.search);
        this.login.redirect = urlParams.has('w') ? urlParams.get('w') : '/';
    }
});