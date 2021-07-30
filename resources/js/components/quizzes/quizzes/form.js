new Vue({
    el: '#component',
    data: {
        name: {
            singular: 'Quiz',
            plural: 'Quizzes'
        },
        loading: false,
        submitting: false,
        url: {
            path: '/quizzes/quizzes'
        },
        quiz: {
            id: '',
            name: '',
            description: '',
        },
        config: {
            tinymce: {
                height: 350,
                menubar: false,
                branding: false,
                plugins: [
                    'lists link image charmap anchor',
                    'media table paste help wordcount'
                ],
                toolbar: 'bold italic |  bullist numlist outdent indent | removeformat | help'
            },
            large: {
                height: 350,
                menubar: false,
                branding: false,
                plugins: [
                    'lists link image charmap anchor',
                    'media table paste help wordcount'
                ],
                toolbar: 'bold italic | bullist numlist outdent indent | alignleft aligncenter alignright alignjustify | media link image charmap | removeformat | help',
                file_picker_callback: function(callback, value, meta) {
                    let url = '/mediamanager/modal';

                    if (meta.filetype === 'image') {
                        url += '/image';
                    }

                    if (meta.filetype === 'media') {
                        url += '/video';
                    }

                    tinymce.activeEditor.windowManager.openUrl({
                        title: 'Media Manager',
                        url: url,
                        onMessage: function (api, data) {
                            callback(data.url, { 'alt': 'Image', 'data-media-id': data.id });
                            api.close();
                        }
                    });
                },
                relative_urls : false,
                remove_script_host : false,
                convert_urls : true,
                content_css: '/css/frontend-tinymce.css'
            }
        },
        users: {
            url: '/admin/users/list',
            items: [],
        },
        scoring_types: {
            url: '/quizzes/types/scorings/list',
            items: []
        }
    },
    methods: {
        submit: function () {
            let that = this;
            that.submitting = true;

            this.$validator.validateAll('quiz').then(function (result) {
                if (result) {
                    that.quiz.action = 'update-about';

                    axios({
                        method: that.quiz.id !== '' ? 'PUT' : 'POST',
                        url: that.url.path + (that.quiz.id !== '' ? ('/' + that.quiz.id) : ''),
                        data: that.quiz
                    }).then(function () {
                        window.location = '/quizzes/quizzes';
                    }).catch(function () {
                        let content = 'Failed to submit the form.';
                        that.$bvToast.toast(content, {
                            title: 'Error',
                            variant: 'danger',
                            solid: true
                        });
                        that.submitting = false;
                    });
                } else {
                    that.submitting = false;
                }
            }).catch(function () {
                let content = 'Validation failed. Please check the form.';
                that.$bvToast.toast(content, {
                    title: 'Error',
                    variant: 'danger',
                    solid: true
                });
                that.submitting = false;
            });
        }
    },
    beforeMount: function () {
        let that = this;

        that.quiz = window.quizmaster.quiz;

        let role = window.quizmaster.user.role;
        let is_admin = role.slug === 'admin' || role.slug === 'developer'

        if (is_admin) {
            axios({
                method: 'GET',
                url: that.users.url + '?l=0'
            }).then(function (data) {
                that.users.items = data.data;
            });
            axios({
                method: 'GET',
                url: that.scoring_types.url + '?l=0'
            }).then(function (data) {
                that.scoring_types.items = data.data;
            });
        }
    }
});
