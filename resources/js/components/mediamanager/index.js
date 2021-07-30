
new Vue({
    el: '#component',
    data: {
        name: {
            singular: 'Media Manager',
            plural: 'Media Manager'
        },
        url: {
            path: {
                upload:  '/mediamanager/upload',
                collection: '/mediamanager/files/list/' + window.quizmaster.mm.type,
                resource: '/mediamanager/files'
            },
            qs: ''
        },
        submitting: false,
        loading: false,
        query: {
            keywords: '',
            page: 1,
            limit: parseInt(window.quizmaster.mm.settings['mm.ipp'])
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
        debug: true,
        prog: 0,
        fileUpload: {
            isUploading: false,
            files: [],
            count: {
                all: 0,
                success: 0,
                failed: 0,
            }
        },
        file: {
            id: '',
            name: ''
        }
    },
    methods: {
        clear: function () {
            this.query = {
                keywords: '',
                page: 1,
                limit: parseInt(window.quizmaster.mm.settings['mm.ipp'])
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
                    that.results.rows.push(...response.data.records);
                    that.results.total.records = response.data.pagination.records.total;
                    that.results.total.pages = response.data.pagination.pages.total;

                    that.loading = false;
                });
        },

        selectFiles: function () {
            document.getElementById("stdFileUpload").click()
        },
        handleUpload: function () {
            let that = this;
            if (!that.fileUpload.isUploading) {
                let files = that.$refs.stdFileUpload.files;
                that.upload(files);
            }
        },
        upload: function(files) {
            let that = this;

            if (files.length === 0) {
                return false;
            }

            that.fileUpload.uploading = true;
            that.fileUpload.files = [];
            that.fileUpload.count.all = 0;
            that.fileUpload.count.success = 0;
            that.fileUpload.count.failed = 0;

            this.$bvModal.show('progresses');

            for (let i = 0; i < files.length; i++) {
                that.fileUpload.files[i]          = files[i];
                that.fileUpload.files[i].progress = 0;
                that.fileUpload.files[i].done     = null;
                that.fileUpload.files[i].error    = '';

                if (!window.quizmaster.mm.settings['mm.file.types'].includes(that.fileUpload.files[i].type)) {
                    that.fileUpload.files[i].done  = false;
                    that.fileUpload.files[i].error = 'Invalid file.';

                    that.fileUpload.count.failed++;
                    that.fileUpload.count.all++;

                    if (that.fileUpload.count.all === files.length) {
                        that.handleDone();
                    }

                    continue;
                }

                if (window.quizmaster.mm.settings['mm.file.size.max'] < that.fileUpload.files[i].size) {
                    that.fileUpload.files[i].done = false;
                    that.fileUpload.files[i].error = 'File size exceeded.';

                    that.fileUpload.count.failed++;
                    that.fileUpload.count.all++;

                    if (that.fileUpload.count.all === files.length) {
                        that.handleDone();
                    }

                    continue;
                }

                let data = new FormData();
                data.append('file', files[i]);

                axios.post(
                    that.url.path.upload,
                    data,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        },
                        onUploadProgress: function(event) {
                            that.fileUpload.files[i].progress = Math.round((event.loaded * 100) / event.total);
                            that.prog++;
                            if (that.debug) {
                                console.log('File ' + that.fileUpload.files[i].name + ' progress: ' + that.fileUpload.files[i].progress + '%');
                            }
                        }
                    }).then(function () {
                        that.fileUpload.files[i].done = true;

                        that.fileUpload.count.success++;
                        that.fileUpload.count.all++;

                        if (that.fileUpload.count.all === files.length) {
                            that.handleDone();
                        }
                    }).catch(function () {
                        that.fileUpload.files[i].done  = false;
                        that.fileUpload.files[i].error = 'Unknown reason.';

                        that.fileUpload.count.failed++;
                        that.fileUpload.count.all++;

                        if (that.fileUpload.count.all === files.length) {
                            that.handleDone();
                        }
                    })
            }

            return true;
        },
        handleDone: function () {
            let that = this;

            that.fileUpload.uploading = false;

            let title = '';
            let variant = '';
            let content = '';

            if (that.fileUpload.count.success === that.fileUpload.count.all) {
                title = 'Success';
                variant = 'success';
                content = (that.fileUpload.count.all === 1 ? 'File' : 'All files') + ' has been uploaded.';
            } else {
                title = 'Warning';
                variant = 'warning';
                content = 'Not all files has been uploaded.';

                if (that.fileUpload.count.failed === that.fileUpload.count.all) {
                    title = 'Error';
                    variant = 'danger';
                    content = (that.fileUpload.count.all === 1 ? 'File ' : 'All files') + ' failed to upload.';
                }
            }

            setTimeout(function () {
                that.results = {
                    rows: [],
                        original: [],
                        total: {
                        records: 0,
                            pages: 0
                    }
                };
                that.clear();
                that.$bvModal.hide('progresses');
                that.$bvToast.toast(content, {
                    title: title,
                    variant: variant,
                    solid: true
                });
             }, 10);
        },

        openViewFileModal: function (file) {
            this.file = file;

            this.$bvModal.show('view-modal');
        },
        pickFile: function (file) {
            let windowParent = window.parent;

            windowParent.postMessage({
                    mceAction: 'noop',
                    id: file.id,
                    name: file.name,
                    url: file.urls.source,
                },
                '*'
            );
        },

        openConfirmDeleteFileModal: function (file) {
            this.file = file;

            this.$bvModal.show('file-delete-modal');
        },
        deleteFile: function (file) {
            let that = this;
            that.submitting = true;

            axios({
                method: 'DELETE',
                url: that.url.path.resource + '/' + that.file.id
            }).then(function () {
                that.file = {
                    id: '',
                    name: '',
                };

                that.clear();
                that.submitting = false;

                let content = 'File [' + file.name + '] has been deleted.';
                that.$bvToast.toast(content, {
                    title: 'Success',
                    variant: 'success',
                    solid: true
                });
            }).catch(function () {
                that.submitting = false;

                let content = 'Failed to delete the file [' + file.name + '].';
                that.$bvToast.toast(content, {
                    title: 'Error',
                    variant: 'danger',
                    solid: true
                });
            });
        },
    },
    mounted: function () {
        let that = this;

        that.clear();

        jQuery(window).bind('scroll', function () {
            if (jQuery(document).height() <= jQuery(window).scrollTop() + jQuery(window).height()) {
                ++that.query.page;
                that.turn();
            }
        });

        jQuery(document).bind('dragover', function () {
            if (!that.fileUpload.isUploading) {
                jQuery('#dropzone')
                    .removeClass('d-none')
                    .addClass('d-flex');
            }
        });

        jQuery(document).bind('dragleave drop', function () {
            if (!that.fileUpload.isUploading) {
                jQuery('#dropzone')
                    .removeClass('d-flex')
                    .addClass('d-none');
            }
        });

        jQuery(document).bind('drop', function (e) {
            e.preventDefault();

            if (!that.fileUpload.isUploading) {
                let files = e.originalEvent.dataTransfer.files;
                that.upload(files);
            }
        });

        jQuery(document).bind('dragover dragleave drop', function (e) {
            e.preventDefault();
        });
    }
});

