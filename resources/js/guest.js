require('./bootstrap');
require('./slidereveal');

require("vue-select");
require("vue-switches");
require("vuedraggable");
require("vue-slider-component");

window.Vue = require('vue');

import BootstrapVue from 'bootstrap-vue';
import VeeValidate from 'vee-validate';
import VueSelect from 'vue-select';
import Switches from 'vue-switches';

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
                confirmed: function () {
                    return "Password failed to match."
                }
            }
        }
    }
});

Vue.component('v-select', VueSelect);
Vue.component('switches', Switches);

window.quizmaster = {};
