new Vue({
    el: '#component',
    data: {
        name: {
            singular: 'Invitation',
            plural: 'Invitations'
        },
        url: {
            path: {
                collection: '',
                resource: ''
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
        invitations: {
            addresses: ''
        },
        invitation: {
            id: '',
            email: ''
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

        invite: function () {
            let that = this;

            that.submitting = true;

            axios({
                method: 'POST',
                url: this.url.path.resource,
                data: that.invitations
            }).then(function (result) {
                that.clear();
                jQuery('#entity-create').modal('hide');
                that.submitting = false;

                that.$bvToast.toast('Invitation/s has been sent.', {
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
                url: that.url.path.resource + '/' + this.invitation.id
            }).then(function () {
                jQuery('#entity-remove').modal('hide');

                that.search();

                that.invitation = {
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
                url: that.url.path.collection,
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

        that.quiz = window.quizmaster.quiz;
        that.url = {
            path: {
                collection: '/quizzes/quizzes/' + that.quiz.id + '/invitations/list',
                resource: '/quizzes/quizzes/' + that.quiz.id + '/invitations',
            },
            qs: ''
        };

        that.clear();
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
