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
/******/ 	return __webpack_require__(__webpack_require__.s = 22);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/components/quizzes/quizzes/settings.js":
/*!*************************************************************!*\
  !*** ./resources/js/components/quizzes/quizzes/settings.js ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

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
      description: ''
    },
    depcheck: {
      checking: false,
      result: {
        flag: null,
        message: null,
        details: {}
      }
    }
  },
  methods: {
    doDepcheck: function doDepcheck() {
      var that = this;

      if (that.quiz.enabled) {
        return;
      }

      that.depcheck.checking = true;
      that.depcheck.result.flag = null;
      this.$bvModal.show('depcheck-modal');
      axios({
        method: 'GET',
        url: that.url.path + '/' + that.quiz.id + '/depcheck'
      }).then(function (data) {
        that.depcheck.result.flag = true;
        that.depcheck.result.message = 'Quiz is ready for deployment.';
        that.depcheck.checking = false;
      })["catch"](function (error) {
        that.depcheck.result.flag = false;
        that.depcheck.result.message = 'Quiz is not ready for deployment.';
        that.depcheck.result.reason = error.response.data.reason;
        that.quiz.enabled = false;
        that.depcheck.checking = false;
      });
    },
    submit: function submit() {
      var that = this;
      that.submitting = true;
      this.$validator.validateAll('quiz').then(function (result) {
        if (result) {
          that.quiz.action = 'update-settings';
          axios({
            method: that.quiz.id !== '' ? 'PUT' : 'POST',
            url: that.url.path + (that.quiz.id !== '' ? '/' + that.quiz.id : ''),
            data: that.quiz
          }).then(function () {
            window.location = '/quizzes/quizzes';
          })["catch"](function (error) {
            console.log(error);
            var content = 'Failed to update the form: ';
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
      })["catch"](function () {
        var content = 'Validation failed. Please check the form.';
        that.$bvToast.toast(content, {
          title: 'Error',
          variant: 'danger',
          solid: true
        });
        that.submitting = false;
      });
    }
  },
  beforeMount: function beforeMount() {
    var that = this;
    that.quiz = Quiz;
  }
});

/***/ }),

/***/ 22:
/*!*******************************************************************!*\
  !*** multi ./resources/js/components/quizzes/quizzes/settings.js ***!
  \*******************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /home/james/Projects/Web/GTC015 Quizmaster/resources/js/components/quizzes/quizzes/settings.js */"./resources/js/components/quizzes/quizzes/settings.js");


/***/ })

/******/ });