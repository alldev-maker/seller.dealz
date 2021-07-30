require('./bootstrap');
require('./slidereveal');

require('slick-carousel');

require("vue-select");
require("vue-switches");
require("vuedraggable");
require("vue-slider-component");
require("@tinymce/tinymce-vue");
require('vue-radial-progress');
require('@voerro/vue-tagsinput');
require('apexcharts');
require('vue-apexcharts');

window.Vue = require('vue');

import BootstrapVue from 'bootstrap-vue';
import VeeValidate from 'vee-validate';
import VueSelect from 'vue-select';
import Switches from 'vue-switches';
import Draggable from 'vuedraggable';
import VueSlider from 'vue-slider-component';
import Editor from '@tinymce/tinymce-vue';
import RadialProgressBar from 'vue-radial-progress';
import VoerroTagsInput from '@voerro/vue-tagsinput';

Vue.use(BootstrapVue);
Vue.use(VeeValidate, {
    dictionary: {
        en: {
            messages: {
                email: function () {
                    return "Must be a valid email address.";
                },
                min: function (field, args) {
                    return "Need at least " + args + " characters.";
                },
                required: function () {
                    return "This field is required.";
                },

            }
        }
    }
});

Vue.component('v-select', VueSelect);
Vue.component('switches', Switches);
Vue.component('draggable', Draggable);
Vue.component('vue-slider', VueSlider);
Vue.component('editor', Editor);
Vue.component('radial-progress-bar', RadialProgressBar);
Vue.component('tags-input', VoerroTagsInput);

window.quizmaster = {};

jQuery(document).ready(function () {
    jQuery('#sidebar').slideReveal({
        width: 270,
        trigger: jQuery(".sidebar-trigger"),
        push: false,
        overlay: true
    });

    jQuery('#sidebar').mCustomScrollbar({
        axis: 'y',
        theme: 'minimal-dark'
    });
});
