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
/******/ 	return __webpack_require__(__webpack_require__.s = 29);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/components/quizzes/takerquizzes/settings.js":
/*!******************************************************************!*\
  !*** ./resources/js/components/quizzes/takerquizzes/settings.js ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("new Vue({\n  el: '#component',\n  data: {\n    name: {\n      singular: 'TakerQuiz',\n      plural: 'TakerQuizzes'\n    },\n    loading: false,\n    submitting: false,\n    url: {\n      path: '/quizzes/takerquizzes'\n    },\n    quiz: {\n      id: '',\n      name: '',\n      description: ''\n    },\n    depcheck: {\n      checking: false,\n      result: {\n        flag: null,\n        message: null,\n        details: {}\n      }\n    }\n  },\n  methods: {\n    doDepcheck: function doDepcheck() {\n      var that = this;\n\n      if (that.quiz.enabled) {\n        return;\n      }\n\n      that.depcheck.checking = true;\n      that.depcheck.result.flag = null;\n      this.$bvModal.show('depcheck-modal');\n      axios({\n        method: 'GET',\n        url: that.url.path + '/' + that.quiz.id + '/depcheck'\n      }).then(function (data) {\n        that.depcheck.result.flag = true;\n        that.depcheck.result.message = 'Quiz is ready for deployment.';\n        that.depcheck.checking = false;\n      })[\"catch\"](function (error) {\n        that.depcheck.result.flag = false;\n        that.depcheck.result.message = 'Quiz is not ready for deployment.';\n        that.depcheck.result.reason = error.response.data.reason;\n        that.quiz.enabled = false;\n        that.depcheck.checking = false;\n      });\n    },\n    submit: function submit() {\n      var that = this;\n      that.submitting = true;\n      this.$validator.validateAll('quiz').then(function (result) {\n        if (result) {\n          that.quiz.action = 'update-settings';\n          axios({\n            method: that.quiz.id !== '' ? 'PUT' : 'POST',\n            url: that.url.path + (that.quiz.id !== '' ? '/' + that.quiz.id : ''),\n            data: that.quiz\n          }).then(function () {\n            window.location = '/quizzes/takerquizzes';\n          })[\"catch\"](function (error) {\n            console.log(error);\n            var content = 'Failed to update the form: ';\n            that.$bvToast.toast(content, {\n              title: 'Error',\n              variant: 'danger',\n              solid: true\n            });\n            that.submitting = false;\n          });\n        } else {\n          that.submitting = false;\n        }\n      })[\"catch\"](function () {\n        var content = 'Validation failed. Please check the form.';\n        that.$bvToast.toast(content, {\n          title: 'Error',\n          variant: 'danger',\n          solid: true\n        });\n        that.submitting = false;\n      });\n    }\n  },\n  beforeMount: function beforeMount() {\n    var that = this;\n    that.quiz = Quiz;\n  }\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvanMvY29tcG9uZW50cy9xdWl6emVzL3Rha2VycXVpenplcy9zZXR0aW5ncy5qcz9iYWM4Il0sIm5hbWVzIjpbIlZ1ZSIsImVsIiwiZGF0YSIsIm5hbWUiLCJzaW5ndWxhciIsInBsdXJhbCIsImxvYWRpbmciLCJzdWJtaXR0aW5nIiwidXJsIiwicGF0aCIsInF1aXoiLCJpZCIsImRlc2NyaXB0aW9uIiwiZGVwY2hlY2siLCJjaGVja2luZyIsInJlc3VsdCIsImZsYWciLCJtZXNzYWdlIiwiZGV0YWlscyIsIm1ldGhvZHMiLCJkb0RlcGNoZWNrIiwidGhhdCIsImVuYWJsZWQiLCIkYnZNb2RhbCIsInNob3ciLCJheGlvcyIsIm1ldGhvZCIsInRoZW4iLCJlcnJvciIsInJlYXNvbiIsInJlc3BvbnNlIiwic3VibWl0IiwiJHZhbGlkYXRvciIsInZhbGlkYXRlQWxsIiwiYWN0aW9uIiwid2luZG93IiwibG9jYXRpb24iLCJjb25zb2xlIiwibG9nIiwiY29udGVudCIsIiRidlRvYXN0IiwidG9hc3QiLCJ0aXRsZSIsInZhcmlhbnQiLCJzb2xpZCIsImJlZm9yZU1vdW50IiwiUXVpeiJdLCJtYXBwaW5ncyI6IkFBQUEsSUFBSUEsR0FBSixDQUFRO0FBQ0pDLElBQUUsRUFBRSxZQURBO0FBRUpDLE1BQUksRUFBRTtBQUNGQyxRQUFJLEVBQUU7QUFDRkMsY0FBUSxFQUFFLFdBRFI7QUFFRkMsWUFBTSxFQUFFO0FBRk4sS0FESjtBQUtGQyxXQUFPLEVBQUUsS0FMUDtBQU1GQyxjQUFVLEVBQUUsS0FOVjtBQU9GQyxPQUFHLEVBQUU7QUFDREMsVUFBSSxFQUFFO0FBREwsS0FQSDtBQVVGQyxRQUFJLEVBQUU7QUFDRkMsUUFBRSxFQUFFLEVBREY7QUFFRlIsVUFBSSxFQUFFLEVBRko7QUFHRlMsaUJBQVcsRUFBRTtBQUhYLEtBVko7QUFlRkMsWUFBUSxFQUFFO0FBQ05DLGNBQVEsRUFBRSxLQURKO0FBRU5DLFlBQU0sRUFBRTtB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LHFCQUFPLEVBQUUsUUFGZ0I7QUFHekJDLG1CQUFLLEVBQUU7QUFIa0IsYUFBN0I7QUFLQXZCLGdCQUFJLENBQUNkLFVBQUwsR0FBa0IsS0FBbEI7QUFDSCxXQW5CTDtBQW9CSCxTQXZCRCxNQXVCTztBQUNIYyxjQUFJLENBQUNkLFVBQUwsR0FBa0IsS0FBbEI7QUFDSDtBQUNKLE9BN0JMLFdBOEJXLFlBQVk7QUFDZixZQUFJZ0MsT0FBTyxHQUFHLDJDQUFkO0FBQ0FsQixZQUFJLENBQUNtQixRQUFMLENBQWNDLEtBQWQsQ0FBb0JGLE9BQXBCLEVBQTZCO0FBQ3pCRyxlQUFLLEVBQUUsT0FEa0I7QUFFekJDLGlCQUFPLEVBQUUsUUFGZ0I7QUFHekJDLGVBQUssRUFBRTtBQUhrQixTQUE3QjtBQUtBdkIsWUFBSSxDQUFDZCxVQUFMLEdBQWtCLEtBQWxCO0FBQ0gsT0F0Q0w7QUF1Q0g7QUEzRUksR0ExQkw7QUF1R0pzQyxhQUFXLEVBQUUsdUJBQVk7QUFDckIsUUFBSXhCLElBQUksR0FBRyxJQUFYO0FBRUFBLFFBQUksQ0FBQ1gsSUFBTCxHQUFZb0MsSUFBWjtBQUNIO0FBM0dHLENBQVIiLCJmaWxlIjoiLi9yZXNvdXJjZXMvanMvY29tcG9uZW50cy9xdWl6emVzL3Rha2VycXVpenplcy9zZXR0aW5ncy5qcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIm5ldyBWdWUoe1xyXG4gICAgZWw6ICcjY29tcG9uZW50JyxcclxuICAgIGRhdGE6IHtcclxuICAgICAgICBuYW1lOiB7XHJcbiAgICAgICAgICAgIHNpbmd1bGFyOiAnVGFrZXJRdWl6JyxcclxuICAgICAgICAgICAgcGx1cmFsOiAnVGFrZXJRdWl6emVzJyxcclxuICAgICAgICB9LFxyXG4gICAgICAgIGxvYWRpbmc6IGZhbHNlLFxyXG4gICAgICAgIHN1Ym1pdHRpbmc6IGZhbHNlLFxyXG4gICAgICAgIHVybDoge1xyXG4gICAgICAgICAgICBwYXRoOiAnL3F1aXp6ZXMvdGFrZXJxdWl6emVzJyxcclxuICAgICAgICB9LFxyXG4gICAgICAgIHF1aXo6IHtcclxuICAgICAgICAgICAgaWQ6ICcnLFxyXG4gICAgICAgICAgICBuYW1lOiAnJyxcclxuICAgICAgICAgICAgZGVzY3JpcHRpb246ICcnLFxyXG4gICAgICAgIH0sXHJcbiAgICAgICAgZGVwY2hlY2s6IHtcclxuICAgICAgICAgICAgY2hlY2tpbmc6IGZhbHNlLFxyXG4gICAgICAgICAgICByZXN1bHQ6IHtcclxuICAgICAgICAgICAgICAgIGZsYWc6IG51bGwsXHJcbiAgICAgICAgICAgICAgICBtZXNzYWdlOiBudWxsLFxyXG4gICAgICAgICAgICAgICAgZGV0YWlsczoge30sXHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgfSxcclxuICAgIH0sXHJcbiAgICBtZXRob2RzOiB7XHJcbiAgICAgICAgZG9EZXBjaGVjazogZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICBsZXQgdGhhdCA9IHRoaXNcclxuXHJcbiAgICAgICAgICAgIGlmICh0aGF0LnF1aXouZW5hYmxlZCkge1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuXHJcbiAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgIHRoYXQuZGVwY2hlY2suY2hlY2tpbmcgPSB0cnVlXHJcbiAgICAgICAgICAgIHRoYXQuZGVwY2hlY2sucmVzdWx0LmZsYWcgPSBudWxsXHJcblxyXG4gICAgICAgICAgICB0aGlzLiRidk1vZGFsLnNob3coJ2RlcGNoZWNrLW1vZGFsJylcclxuXHJcbiAgICAgICAgICAgIGF4aW9zKHtcclxuICAgICAgICAgICAgICAgIG1ldGhvZDogJ0dFVCcsXHJcbiAgICAgICAgICAgICAgICB1cmw6IHRoYXQudXJsLnBhdGggKyAnLycgKyB0aGF0LnF1aXouaWQgKyAnL2RlcGNoZWNrJyxcclxuICAgICAgICAgICAgfSlcclxuICAgICAgICAgICAgICAgIC50aGVuKGZ1bmN0aW9uIChkYXRhKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgdGhhdC5kZXBjaGVjay5yZXN1bHQuZmxhZyA9IHRydWVcclxuICAgICAgICAgICAgICAgICAgICB0aGF0LmRlcGNoZWNrLnJlc3VsdC5tZXNzYWdlID1cclxuICAgICAgICAgICAgICAgICAgICAgICAgJ1F1aXogaXMgcmVhZHkgZm9yIGRlcGxveW1lbnQuJ1xyXG4gICAgICAgICAgICAgICAgICAgIHRoYXQuZGVwY2hlY2suY2hlY2tpbmcgPSBmYWxzZVxyXG4gICAgICAgICAgICAgICAgfSlcclxuICAgICAgICAgICAgICAgIC5jYXRjaChmdW5jdGlvbiAoZXJyb3IpIHtcclxuICAgICAgICAgICAgICAgICAgICB0aGF0LmRlcGNoZWNrLnJlc3VsdC5mbGFnID0gZmFsc2VcclxuICAgICAgICAgICAgICAgICAgICB0aGF0LmRlcGNoZWNrLnJlc3VsdC5tZXNzYWdlID1cclxuICAgICAgICAgICAgICAgICAgICAgICAgJ1F1aXogaXMgbm90IHJlYWR5IGZvciBkZXBsb3ltZW50LidcclxuICAgICAgICAgICAgICAgICAgICB0aGF0LmRlcGNoZWNrLnJlc3VsdC5yZWFzb24gPSBlcnJvci5yZXNwb25zZS5kYXRhLnJlYXNvblxyXG4gICAgICAgICAgICAgICAgICAgIHRoYXQucXVpei5lbmFibGVkID0gZmFsc2VcclxuICAgICAgICAgICAgICAgICAgICB0aGF0LmRlcGNoZWNrLmNoZWNraW5nID0gZmFsc2VcclxuICAgICAgICAgICAgICAgIH0pXHJcbiAgICAgICAgfSxcclxuICAgICAgICBzdWJtaXQ6IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgbGV0IHRoYXQgPSB0aGlzXHJcbiAgICAgICAgICAgIHRoYXQuc3VibWl0dGluZyA9IHRydWVcclxuXHJcbiAgICAgICAgICAgIHRoaXMuJHZhbGlkYXRvclxyXG4gICAgICAgICAgICAgICAgLnZhbGlkYXRlQWxsKCdxdWl6JylcclxuICAgICAgICAgICAgICAgIC50aGVuKGZ1bmN0aW9uIChyZXN1bHQpIHtcclxuICAgICAgICAgICAgICAgICAgICBpZiAocmVzdWx0KSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoYXQucXVpei5hY3Rpb24gPSAndXBkYXRlLXNldHRpbmdzJ1xyXG5cclxuICAgICAgICAgICAgICAgICAgICAgICAgYXhpb3Moe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbWV0aG9kOiB0aGF0LnF1aXouaWQgIT09ICcnID8gJ1BVVCcgOiAnUE9TVCcsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB1cmw6XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhhdC51cmwucGF0aCArXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgKHRoYXQucXVpei5pZCAhPT0gJycgPyAnLycgKyB0aGF0LnF1aXouaWQgOiAnJyksXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBkYXRhOiB0aGF0LnF1aXosXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH0pXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAudGhlbihmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgd2luZG93LmxvY2F0aW9uID0gJy9xdWl6emVzL3Rha2VycXVpenplcydcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuY2F0Y2goZnVuY3Rpb24gKGVycm9yKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgY29uc29sZS5sb2coZXJyb3IpXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbGV0IGNvbnRlbnQgPSAnRmFpbGVkIHRvIHVwZGF0ZSB0aGUgZm9ybTogJ1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRoYXQuJGJ2VG9hc3QudG9hc3QoY29udGVudCwge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aXRsZTogJ0Vycm9yJyxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyaWFudDogJ2RhbmdlcicsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNvbGlkOiB0cnVlLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhhdC5zdWJtaXR0aW5nID0gZmFsc2VcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pXHJcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgdGhhdC5zdWJtaXR0aW5nID0gZmFsc2VcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICB9KVxyXG4gICAgICAgICAgICAgICAgLmNhdGNoKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgICAgICBsZXQgY29udGVudCA9ICdWYWxpZGF0aW9uIGZhaWxlZC4gUGxlYXNlIGNoZWNrIHRoZSBmb3JtLidcclxuICAgICAgICAgICAgICAgICAgICB0aGF0LiRidlRvYXN0LnRvYXN0KGNvbnRlbnQsIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgdGl0bGU6ICdFcnJvcicsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhcmlhbnQ6ICdkYW5nZXInLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICBzb2xpZDogdHJ1ZSxcclxuICAgICAgICAgICAgICAgICAgICB9KVxyXG4gICAgICAgICAgICAgICAgICAgIHRoYXQuc3VibWl0dGluZyA9IGZhbHNlXHJcbiAgICAgICAgICAgICAgICB9KVxyXG4gICAgICAgIH0sXHJcbiAgICB9LFxyXG4gICAgYmVmb3JlTW91bnQ6IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICBsZXQgdGhhdCA9IHRoaXNcclxuXHJcbiAgICAgICAgdGhhdC5xdWl6ID0gUXVpelxyXG4gICAgfSxcclxufSlcclxuIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./resources/js/components/quizzes/takerquizzes/settings.js\n");

/***/ }),

/***/ 29:
/*!************************************************************************!*\
  !*** multi ./resources/js/components/quizzes/takerquizzes/settings.js ***!
  \************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! F:\Project\laravel\quizemaster\code\resources\js\components\quizzes\takerquizzes\settings.js */"./resources/js/components/quizzes/takerquizzes/settings.js");


/***/ })

/******/ });