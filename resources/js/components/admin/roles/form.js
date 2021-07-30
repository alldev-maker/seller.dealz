/**
 * Role Form
 **/

new Vue({
    el: '#role',
    data: {
        name: {
            singular: 'Role',
            plural: 'Roles'
        },
        loading: false,
        submitting: false,
        url: {
            path: '/admin/roles'
        },
        role: {
            id: 0,
            name: '',
            slug: '',
            permissions: []
        },
        components: []
    },
    methods: {
        submit: function () {
            var that = this;
            that.submitting = true;

            this.$validator.validateAll('role').then(function (result) {
                if (result) {
                    axios({
                        method: that.role.id !== '' ? 'PUT' : 'POST',
                        url: that.url.path + (that.role.id !== '' ? ('/' + that.role.id) : ''),
                        data: that.role
                    }).then(function () {
                        window.location = '/admin/roles';
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
        var that = this;
        this.role = Role;
    }
});