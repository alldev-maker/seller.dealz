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
/******/ 	return __webpack_require__(__webpack_require__.s = 54);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/components/quizzes/user_results/timing.js":
/*!****************************************************************!*\
  !*** ./resources/js/components/quizzes/user_results/timing.js ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("new Vue({\n  el: '#component',\n  data: {\n    name: {\n      singular: 'UserResult',\n      plural: 'UserResults'\n    },\n    url: {\n      path: {\n        resource: '/quizzes/user_results'\n      }\n    },\n    loading: false,\n    selected: null,\n    result: {\n      id: ''\n    }\n  },\n  methods: {},\n  beforeMount: function beforeMount() {\n    this.result.id = window.quizmaster.result.id;\n    this.selected = window.quizmaster.selected;\n  }\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvanMvY29tcG9uZW50cy9xdWl6emVzL3VzZXJfcmVzdWx0cy90aW1pbmcuanM/ZGFkYyJdLCJuYW1lcyI6WyJWdWUiLCJlbCIsImRhdGEiLCJuYW1lIiwic2luZ3VsYXIiLCJwbHVyYWwiLCJ1cmwiLCJwYXRoIiwicmVzb3VyY2UiLCJsb2FkaW5nIiwic2VsZWN0ZWQiLCJyZXN1bHQiLCJpZCIsIm1ldGhvZHMiLCJiZWZvcmVNb3VudCIsIndpbmRvdyIsInF1aXptYXN0ZXIiXSwibWFwcGluZ3MiOiJBQUFBLElBQUlBLEdBQUosQ0FBUTtBQUNKQyxJQUFFLEVBQUUsWUFEQTtBQUVKQyxNQUFJLEVBQUU7QUFDRkMsUUFBSSxFQUFFO0FBQ0ZDLGNBQVEsRUFBRSxZQURSO0FBRUZDLFlBQU0sRUFBRTtBQUZOLEtBREo7QUFLRkMsT0FBRyxFQUFFO0FBQ0RDLFVBQUksRUFBRTtBQUNGQyxnQkFBUSxFQUFFO0FBRFI7QUFETCxLQUxIO0FBVUZDLFdBQU8sRUFBRSxLQVZQO0FBV0ZDLFlBQVEsRUFBRSxJQVhSO0FBWUZDLFVBQU0sRUFBRTtBQUNKQyxRQUFFLEVBQUU7QUFEQTtBQVpOLEdBRkY7QUFrQkpDLFNBQU8sRUFBRSxFQWxCTDtBQW1CSkMsYUFBVyxFQUFFLHVCQUFZO0FBQ3JCLFNBQUtILE1BQUwsQ0FBWUMsRUFBWixHQUFpQkcsTUFBTSxDQUFDQyxVQUFQLENBQWtCTCxNQUFsQixDQUF5QkMsRUFBMUM7QUFDQSxTQUFLRixRQUFMLEdBQWdCSyxNQUFNLENBQUNDLFVBQVAsQ0FBa0JOLFFBQWxDO0FBQ0g7QUF0QkcsQ0FBUiIsImZpbGUiOiIuL3Jlc291cmNlcy9qcy9jb21wb25lbnRzL3F1aXp6ZXMvdXNlcl9yZXN1bHRzL3RpbWluZy5qcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIm5ldyBWdWUoe1xyXG4gICAgZWw6ICcjY29tcG9uZW50JyxcclxuICAgIGRhdGE6IHtcclxuICAgICAgICBuYW1lOiB7XHJcbiAgICAgICAgICAgIHNpbmd1bGFyOiAnVXNlclJlc3VsdCcsXHJcbiAgICAgICAgICAgIHBsdXJhbDogJ1VzZXJSZXN1bHRzJyxcclxuICAgICAgICB9LFxyXG4gICAgICAgIHVybDoge1xyXG4gICAgICAgICAgICBwYXRoOiB7XHJcbiAgICAgICAgICAgICAgICByZXNvdXJjZTogJy9xdWl6emVzL3VzZXJfcmVzdWx0cycsXHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgfSxcclxuICAgICAgICBsb2FkaW5nOiBmYWxzZSxcclxuICAgICAgICBzZWxlY3RlZDogbnVsbCxcclxuICAgICAgICByZXN1bHQ6IHtcclxuICAgICAgICAgICAgaWQ6ICcnLFxyXG4gICAgICAgIH0sXHJcbiAgICB9LFxyXG4gICAgbWV0aG9kczoge30sXHJcbiAgICBiZWZvcmVNb3VudDogZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHRoaXMucmVzdWx0LmlkID0gd2luZG93LnF1aXptYXN0ZXIucmVzdWx0LmlkXHJcbiAgICAgICAgdGhpcy5zZWxlY3RlZCA9IHdpbmRvdy5xdWl6bWFzdGVyLnNlbGVjdGVkXHJcbiAgICB9LFxyXG59KVxyXG4iXSwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./resources/js/components/quizzes/user_results/timing.js\n");

/***/ }),

/***/ 54:
/*!**********************************************************************!*\
  !*** multi ./resources/js/components/quizzes/user_results/timing.js ***!
  \**********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! F:\Project\laravel\quizemaster\code\resources\js\components\quizzes\user_results\timing.js */"./resources/js/components/quizzes/user_results/timing.js");


/***/ })

/******/ });