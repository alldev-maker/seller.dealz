new Vue({
    el: '#component',
    data: {
        name: {
            singular: 'Quiz',
            plural: 'Quizzes'
        },
        url: {
            path: {
                collection: '/quizzes/invitations/list',
                resource: '/quizzes/invitations'
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
        filters: {
            form: {
                plan: null
            },
            status: 0,
            qs: ''
        },
        results: {
            rows: [],
            original: [],
            total: {
                records: 0,
                pages: 0
            }
        },
        checkbox: {
            ids: [],
            all: false
        },
        quiz: {
            id: '',
            name: ''
        }
    },
    methods: {
        clear: function () {
            this.query = {
                keywords: '',
                page: 1,
                limit: parseInt(Settings['site.ipp.tabular'])
            };
            this.filters = {
                form: {
                    plan: null
                },
                status: 0,
                qs: ''
            };

            this.search();
        },
        search: function () {
            let that = this;

            this.url.qs = 'q=' + this.query.keywords + this.filters.qs + '&p=' + this.query.page + '&l=' + this.query.limit + '';
            that.loading = true;

            axios
                .get(this.url.path.collection + '?' + this.url.qs)
                .then(function (response) {
                    that.results.rows = response.data.records;
                    that.results.total.records = response.data.pagination.records.total;
                    that.results.total.pages = response.data.pagination.pages.total;

                    that.loading = false;
                });
        },
        turn: function () {
            let that = this;
            this.url.qs = 'q=' + this.query.keywords + this.filters.qs + '&p=' + this.query.page + '&l=' + this.query.limit + '';
            that.loading = true;

            axios
                .get(this.url.path.collection + '?' + this.url.qs)
                .then(function (response) {
                    that.results.rows = response.data.records;
                    that.results.total.records = response.data.pagination.records.total;
                    that.results.total.pages = response.data.pagination.pages.total;

                    that.loading = false;
                });
        },

        openDescriptionModal: function () {
            jQuery('#entity-description').modal('show');
        }
    },

    beforeMount: function () {
        this.clear();
    }
});
