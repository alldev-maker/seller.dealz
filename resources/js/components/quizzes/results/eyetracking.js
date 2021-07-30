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
        changeSection: function () {

        }
    },
    beforeMount: function () {
        this.result.id = window.quizmaster.result.id;
        this.selected = window.quizmaster.selected;
    },
    mounted: function () {
        jQuery('.table-passage .content .s-text').each(function () {
            let fg = jQuery(this).data('fgcolor');
            let bg = jQuery(this).data('bgcolor');
            if (fg !== undefined && bg !== undefined) {
                let css = 'color: ' + fg + '; background-color: ' + bg + ';';
                jQuery(this).attr('style', css);
            }
        });

        jQuery('.table-question .question .s-text').each(function () {
            let fg = jQuery(this).data('fgcolor');
            let bg = jQuery(this).data('bgcolor');
            if (fg !== undefined && bg !== undefined) {
                let css = 'color: ' + fg + '; background-color: ' + bg + ';';
                jQuery(this).attr('style', css);
            }
        });
    }
});
