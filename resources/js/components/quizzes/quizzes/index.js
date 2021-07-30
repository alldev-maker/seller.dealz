new Vue({
    el: '#component',
    data: {
        name: {
            singular: 'Quiz',
            plural: 'Quizzes'
        },
        url: {
            path: {
                collection: '/quizzes/quizzes/list',
                resource: '/quizzes/quizzes'
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

        confirmRemove: function (quiz) {
            this.quiz = quiz;
            jQuery('#entity-remove').modal('show');
        },

        confirmRemoveSelected: function () {
            jQuery('#entity-remove-selected').modal('show');
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

        copy: function(id) {
            let that = this;

            axios({
                method: 'PUT',
                url: this.url.path.resource + '/' + id,
                data: {
                    action: 'copy'
                }
            }).then(function () {
                jQuery('#entity-remove').modal('hide');

                that.search();

                that.$bvToast.toast(that.name.singular + ' has been copied.', {
                    title: 'Message',
                    variant: 'success',
                    solid: true
                });
            });
        },

        remove: function () {
            let that = this;

            axios({
                method: 'DELETE',
                url: this.url.path.resource + '/' + this.quiz.id
            }).then(function () {
                jQuery('#entity-remove').modal('hide');

                that.search();

                that.quiz = {
                    id: 0,
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
                url: this.url.path,
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
        this.clear();
    },

    mounted: function () {
        if (Message != null) {
            this.$bvToast.toast(Message.content, {
                title: 'Message',
                variant: Message.status,
                solid: true
            });
        }
    }
});
