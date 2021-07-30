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
/******/ 	return __webpack_require__(__webpack_require__.s = 52);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/components/quizzes/user_results/video.js":
/*!***************************************************************!*\
  !*** ./resources/js/components/quizzes/user_results/video.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("new Vue({\n  el: '#component',\n  data: {\n    name: {\n      singular: 'UserResult',\n      plural: 'UserResults'\n    },\n    url: {\n      path: {\n        resource: '/quizzes/user_results'\n      }\n    },\n    loading: false,\n    result: {\n      id: ''\n    }\n  },\n  methods: {},\n  beforeMount: function beforeMount() {\n    this.result.id = Result.id;\n  },\n  mounted: function mounted() {}\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvanMvY29tcG9uZW50cy9xdWl6emVzL3VzZXJfcmVzdWx0cy92aWRlby5qcz84Y2EzIl0sIm5hbWVzIjpbIlZ1ZSIsImVsIiwiZGF0YSIsIm5hbWUiLCJzaW5ndWxhciIsInBsdXJhbCIsInVybCIsInBhdGgiLCJyZXNvdXJjZSIsImxvYWRpbmciLCJyZXN1bHQiLCJpZCIsIm1ldGhvZHMiLCJiZWZvcmVNb3VudCIsIlJlc3VsdCIsIm1vdW50ZWQiXSwibWFwcGluZ3MiOiJBQUFBLElBQUlBLEdBQUosQ0FBUTtBQUNKQyxJQUFFLEVBQUUsWUFEQTtBQUVKQyxNQUFJLEVBQUU7QUFDRkMsUUFBSSxFQUFFO0FBQ0ZDLGNBQVEsRUFBRSxZQURSO0FBRUZDLFlBQU0sRUFBRTtBQUZOLEtBREo7QUFLRkMsT0FBRyxFQUFFO0FBQ0RDLFVBQUksRUFBRTtBQUNGQyxnQkFBUSxFQUFFO0FBRFI7QUFETCxLQUxIO0FBVUZDLFdBQU8sRUFBRSxLQVZQO0FBV0ZDLFVBQU0sRUFBRTtBQUNKQyxRQUFFLEVBQUU7QUFEQTtBQVhOLEdBRkY7QUFpQkpDLFNBQU8sRUFBRSxFQWpCTDtBQWtCSkMsYUFBVyxFQUFFLHVCQUFZO0FBQ3JCLFNBQUtILE1BQUwsQ0FBWUMsRUFBWixHQUFpQkcsTUFBTSxDQUFDSCxFQUF4QjtBQUNILEdBcEJHO0FBcUJKSSxTQUFPLEVBQUUsbUJBQVksQ0FBRTtBQXJCbkIsQ0FBUiIsImZpbGUiOiIuL3Jlc291cmNlcy9qcy9jb21wb25lbnRzL3F1aXp6ZXMvdXNlcl9yZXN1bHRzL3ZpZGVvLmpzLmpzIiwic291cmNlc0NvbnRlbnQiOlsibmV3IFZ1ZSh7XHJcbiAgICBlbDogJyNjb21wb25lbnQnLFxyXG4gICAgZGF0YToge1xyXG4gICAgICAgIG5hbWU6IHtcclxuICAgICAgICAgICAgc2luZ3VsYXI6ICdVc2VyUmVzdWx0JyxcclxuICAgICAgICAgICAgcGx1cmFsOiAnVXNlclJlc3VsdHMnLFxyXG4gICAgICAgIH0sXHJcbiAgICAgICAgdXJsOiB7XHJcbiAgICAgICAgICAgIHBhdGg6IHtcclxuICAgICAgICAgICAgICAgIHJlc291cmNlOiAnL3F1aXp6ZXMvdXNlcl9yZXN1bHRzJyxcclxuICAgICAgICAgICAgfSxcclxuICAgICAgICB9LFxyXG4gICAgICAgIGxvYWRpbmc6IGZhbHNlLFxyXG4gICAgICAgIHJlc3VsdDoge1xyXG4gICAgICAgICAgICBpZDogJycsXHJcbiAgICAgICAgfSxcclxuICAgIH0sXHJcbiAgICBtZXRob2RzOiB7fSxcclxuICAgIGJlZm9yZU1vdW50OiBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgdGhpcy5yZXN1bHQuaWQgPSBSZXN1bHQuaWRcclxuICAgIH0sXHJcbiAgICBtb3VudGVkOiBmdW5jdGlvbiAoKSB7fSxcclxufSlcclxuIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./resources/js/components/quizzes/user_results/video.js\n");

/***/ }),

/***/ 52:
/*!*********************************************************************!*\
  !*** multi ./resources/js/components/quizzes/user_results/video.js ***!
  \*********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! F:\Project\laravel\quizemaster\code\resources\js\components\quizzes\user_results\video.js */"./resources/js/components/quizzes/user_results/video.js");


/***/ })

/******/ });