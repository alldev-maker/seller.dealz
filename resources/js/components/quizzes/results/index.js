new Vue({
    el: '#component',
    data: {
        name: {
            singular: 'Result',
            plural: 'Results'
        },
        url: {
            path: {
                collection: '/quizzes/results/list',
                resource: '/quizzes/results'
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
                quiz: null
            },
            status: 0,
            qs: ''
        },
        quizzes: {
            url: '/quizzes/quizzes/list',
            items: [],
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
        result: {
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
                    quiz: null
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

        openFiltersModal: function () {
            jQuery('#entity-filter').modal('show');
        },

        openCreateModal: function () {
            jQuery('#entity-create').modal('show');
        },

        selectAll: function () {
            this.checkbox.ids = []
            if (this.checkbox.all) {
                for (let i in this.results.rows) {
                    this.checkbox.ids.push(this.results.rows[i].id);
                }
            }
        },
        select: function () {
            this.checkbox.all = false;
        },

        cancel: function (index) {
            let entity = _.clone(this.results.original[index]);
            entity.edit = 0;

            this.$set(this.results.rows, index, entity);
        },

        confirmRemove: function (result) {
            this.result = result;
            jQuery('#entity-remove').modal('show');
        },

        confirmRemoveSelected: function () {
            jQuery('#entity-remove-selected').modal('show');
        },

        filter: function () {
            let that = this;
            that.filters.qs = '&f[q]=' + that.filters.form.quiz.id
            that.filters.status = 1;
            jQuery('#entity-filter').modal('hide');
            this.search();
        },

        clearFilters: function () {
            this.filters = {
                form: {
                    quiz: null
                },
                status: 0,
                qs: ''
            };
            this.search();
        },

        populateProblemTypes: function (result) {
            let that = this;

            axios({
                method: 'PUT',
                url: this.url.path.resource + '/' + result.id,
                data: {
                    action: 'ppt',
                }
            }).then(function (result) {
                that.$bvToast.toast(that.name.singular + ' has been updated to populate Problem Types.', {
                    title: 'Message',
                    variant: 'success',
                    solid: true
                });
            });
        },

        recalculate: function(result) {
            let that = this;

            axios({
                method: 'PUT',
                url: this.url.path.resource + '/' + result.id,
                data: {
                    action: 'rcl',
                }
            }).then(function (result) {
                that.$bvToast.toast(that.name.singular + ' has been recalculated.', {
                    title: 'Message',
                    variant: 'success',
                    solid: true
                });
            });
        },

        create: function () {
            let that = this;

            axios({
                method: 'POST',
                url: this.url.path.resource,
                data: that.quiz
            }).then(function (result) {
                window.location = that.url.path.resource + '/' + result.data.id + '/edit';
            });
        },

        remove: function () {
            let that = this;

            axios({
                method: 'DELETE',
                url: this.url.path.resource + '/' + that.result.id
            }).then(function () {
                jQuery('#entity-remove').modal('hide');

                that.search();

                that.result = {
                    id: '',
                    name: ''
                };

                that.$bvToast.toast(that.name.singular + ' has been deleted.', {
                    title: 'Message',
                    variant: 'success',
                    solid: true
                });
            });
        },

        removeSelected: function () {
            let that = this;

            axios({
                method: 'DELETE',
                url: this.url.path.collection,
                data: {
                    ids: this.checkbox.ids
                }
            }).then(function () {
                jQuery('#entity-remove-selected').modal('hide');

                that.checkbox.ids = [];
                that.search();

                that.$bvToast.toast(that.name.plural + ' has been deleted.', {
                    title: 'Message',
                    variant: 'success',
                    solid: true
                });
            });
        }
    },

    watch: {
        'checkbox.all': function () {
            this.selectAll();
        }
    },

    beforeMount: function () {
        let that = this;
        axios({
            method: 'GET',
            url: that.quizzes.url + '?l=0'
        }).then(function (data) {
            that.quizzes.items = data.data;
        });

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
