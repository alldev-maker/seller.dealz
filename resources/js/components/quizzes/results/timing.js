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
        selected: null,
        result: {
            id: '',
        },
    },
    methods: {

    },
    beforeMount: function () {
        this.result.id = window.quizmaster.result.id;
        this.selected = window.quizmaster.selected;
    },
});
