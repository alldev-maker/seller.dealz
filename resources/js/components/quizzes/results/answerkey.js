new Vue({
    el: '#component',
    data: {
        name: {
            singular: 'Result',
            plural: 'Results'
        },
        url: {
            path: {
                resource: '/quizzes/results'
            },
        },
        loading: false,
        result: {
            id: '',
        },
        current: {
            video: ''
        }
    },
    methods: {
        viewMovie: function (url) {
            this.current.video = url;
            this.$bvModal.show('video-modal');
        }
    },
    beforeMount: function () {
        this.result = window.quizmaster.result;
    },
    mounted: function () {

    }
});
