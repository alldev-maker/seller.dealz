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
    },
    methods: {

    },
    beforeMount: function () {
        this.result.id = Result.id;
    },
    mounted: function () {

    }
});
