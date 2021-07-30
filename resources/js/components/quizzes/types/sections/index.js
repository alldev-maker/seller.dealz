/**
 * Section Type Index Page
 **/

new Vue({
    el: '#type',
    data: {
        name: {
            singular: 'Section Type',
            plural: 'Section Types'
        },
        url: {
            path: {
                collection: '/quizzes/types/sections/list',
                resource: '/quizzes/types/sections',
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
        results: {
            rows: [],
            total: {
                records: 0,
                pages: 0
            }
        },
        checkbox: {
            ids: [],
            all: false
        },
        section: {
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

            this.search();
        },
        search: function () {
            let that = this;
            this.url.qs = 'q=' + this.query.keywords + '&p=' + this.query.page + '&l=' + this.query.limit + '';
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
            this.url.qs = 'q=' + this.query.keywords + '&p=' + this.query.page + '&l=' + this.query.limit + '';
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

        selectAll: function () {
            this.checkbox.ids = [];
            if (this.checkbox.all) {
                for (var i in this.results.rows) {
                    this.checkbox.ids.push(this.results.rows[i].id);
                }
            }
        },
        select: function () {
            this.checkbox.all = false;
        },

        confirmRemove: function (section) {
            this.section = section;
            jQuery('#entity-remove').modal('show');
        },

        confirmRemoveSelected: function () {
            jQuery('#entity-remove-selected').modal('show');
        },

        remove: function () {
            let that = this;

            axios({
                method: 'DELETE',
                url: this.url.path.resource + '/' + this.section.id
            }).then(function () {
                jQuery('#entity-remove').modal('hide');

                that.search();

                that.section = {
                    id: '',
                    name: ''
                };

                that.$bvToast.toast(that.name.singular + ' has been deleted.', {
                    title: 'Message',
                    variant:'success',
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
                    variant:'success',
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
