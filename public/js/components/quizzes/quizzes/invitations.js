/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 21);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/components/quizzes/quizzes/invitations.js":
/*!****************************************************************!*\
  !*** ./resources/js/components/quizzes/quizzes/invitations.js ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

new Vue({
  el: '#component',
  data: {
    name: {
      singular: 'Invitation',
      plural: 'Invitations'
    },
    url: {
      path: {
        collection: '',
        resource: ''
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
    filters: {
      form: {
        plan: null
      },
      status: 0,
      qs: ''
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
    invitations: {
      addresses: ''
    },
    invitation: {
      id: '',
      email: ''
    }
  },
  methods: {
    clear: function clear() {
      this.query = {
        keywords: '',
        page: 1,
        limit: parseInt(Settings['site.ipp.tabular'])
      };
      this.filters = {
        form: {
          plan: null
        },
        status: 0,
        qs: ''
      };
      this.search();
    },
    search: function search() {
      var that = this;
      this.url.qs = 'q=' + this.query.keywords + this.filters.qs + '&p=' + this.query.page + '&l=' + this.query.limit + '';
      that.loading = true;
      axios.get(this.url.path.collection + '?' + this.url.qs).then(function (response) {
        that.results.rows = response.data.records;
        that.results.total.records = response.data.pagination.records.total;
        that.results.total.pages = response.data.pagination.pages.total;
        that.loading = false;
      });
    },
    turn: function turn() {
      var that = this;
      this.url.qs = 'q=' + this.query.keywords + this.filters.qs + '&p=' + this.query.page + '&l=' + this.query.limit + '';
      that.loading = true;
      axios.get(this.url.path.collection + '?' + this.url.qs).then(function (response) {
        that.results.rows = response.data.records;
        that.results.total.records = response.data.pagination.records.total;
        that.results.total.pages = response.data.pagination.pages.total;
        that.loading = false;
      });
    },
    openFiltersModal: function openFiltersModal() {
      jQuery('#entity-filter').modal('show');
    },
    openCreateModal: function openCreateModal() {
      jQuery('#entity-create').modal('show');
    },
    selectAll: function selectAll() {
      this.checkbox.ids = [];

      if (this.checkbox.all) {
        for (var i in this.results.rows) {
          this.checkbox.ids.push(this.results.rows[i].id);
        }
      }
    },
    select: function select() {
      this.checkbox.all = false;
    },
    cancel: function cancel(index) {
      var entity = _.clone(this.results.original[index]);

      entity.edit = 0;
      this.$set(this.results.rows, index, entity);
    },
    confirmRemove: function confirmRemove(quiz) {
      this.quiz = quiz;
      jQuery('#entity-remove').modal('show');
    },
    confirmRemoveSelected: function confirmRemoveSelected() {
      jQuery('#entity-remove-selected').modal('show');
    },
    invite: function invite() {
      var that = this;
      that.submitting = true;
      axios({
        method: 'POST',
        url: this.url.path.resource,
        data: that.invitations
      }).then(function (result) {
        that.clear();
        jQuery('#entity-create').modal('hide');
        that.submitting = false;
        that.$bvToast.toast('Invitation/s has been sent.', {
          title: 'Message',
          variant: 'success',
          solid: true
        });
      });
    },
    remove: function remove() {
      var that = this;
      axios({
        method: 'DELETE',
        url: that.url.path.resource + '/' + this.invitation.id
      }).then(function () {
        jQuery('#entity-remove').modal('hide');
        that.search();
        that.invitation = {
          id: 0,
          name: ''
        };
        that.$bvToast.toast(that.name.singular + ' has been deleted.', {
          title: 'Message',
          variant: 'success',
          solid: true
        });
      });
    },
    removeSelected: function removeSelected() {
      var that = this;
      axios({
        method: 'DELETE',
        url: that.url.path.collection,
        data: {
          ids: this.checkbox.ids
        }
      }).then(function () {
        jQuery('#entity-remove-selected').modal('hide');
        that.checkbox.ids = [];
        that.search();
        that.$bvToast.toast(that.name.plural + ' has been deleted.', {
          title: 'Message',
          variant: 'success',
          solid: true
        });
      });
    }
  },
  watch: {
    'checkbox.all': function checkboxAll() {
      this.selectAll();
    }
  },
  beforeMount: function beforeMount() {
    var that = this;
    that.quiz = window.quizmaster.quiz;
    that.url = {
      path: {
        collection: '/quizzes/quizzes/' + that.quiz.id + '/invitations/list',
        resource: '/quizzes/quizzes/' + that.quiz.id + '/invitations'
      },
      qs: ''
    };
    that.clear();
  },
  mounted: function mounted() {
    if (window.quizmaster.message != null) {
      this.$bvToast.toast(window.quizmaster.message.content, {
        title: 'Message',
        variant: window.quizmaster.message.status,
        solid: true
      });
    }
  }
});

/***/ }),

/***/ 21:
/*!**********************************************************************!*\
  !*** multi ./resources/js/components/quizzes/quizzes/invitations.js ***!
  \**********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /home/james/Projects/Web/GTC015 Quizmaster/resources/js/components/quizzes/quizzes/invitations.js */"./resources/js/components/quizzes/quizzes/invitations.js");


/***/ })

/******/ });