new Vue({
    el: '#settings',
    data: {
        name: {
            singular: 'Settings',
            plural: 'Settings'
        },
        url: {
            path: {
                collection: '/admin/settings/list',
                resource: '/admin/settings',
            },
            qs: ''
        },
        loading: false,
        submitting: false,
        query: {
            keywords: '',
            page: 1,
            limit: parseInt(Settings['site.ipp.tabular'])
        },
    },
    methods: {

    },
    beforeMount: function () {
        this.clear();
    },
    mounted: function () {
        if (window.quizmaster.message != null) {
            this.$bvToast.toast(window.quizmaster.message.content, {
                title: 'Message',
                variant: window.quizmaster.message.status,
                solid: true
            });
        }
    }
});
