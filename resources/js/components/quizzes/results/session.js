// TO DO: Integrate the rrweb player or make your own session player.
// window.rrweb = require('rrweb');
// window.rrwebPlayer = require('rrweb-player');

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
            session: []
        },
    },
    methods: {
        getEvents: function () {
            let that = this;

            that.loading = true;

            axios
                .get(this.url.path.resource + '/' + that.result.id + '/' + 'logs/session')
                .then(function (response) {
                    that.result.session = response.data;
                    that.loading = false;

                    new rrwebPlayer({
                        target: document.getElementById("session-player-area"), // customizable root element
                        data: {
                            events: that.result.session,
                            autoPlay: true,
                        },
                    });
                });
        }
    },
    beforeMount: function () {
        this.result.id = Result.id;
    },
    mounted: function () {
        this.getEvents();
    }
});
