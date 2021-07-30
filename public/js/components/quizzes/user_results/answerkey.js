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
/******/ 	return __webpack_require__(__webpack_require__.s = 50);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/components/quizzes/user_results/answerkey.js":
/*!*******************************************************************!*\
  !*** ./resources/js/components/quizzes/user_results/answerkey.js ***!
  \*******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("new Vue({\n  el: '#component',\n  data: {\n    name: {\n      singular: 'UserResult',\n      plural: 'UserResults'\n    },\n    url: {\n      path: {\n        resource: '/quizzes/user_results'\n      }\n    },\n    loading: false,\n    result: {\n      id: ''\n    },\n    current: {\n      video: ''\n    }\n  },\n  methods: {\n    viewMovie: function viewMovie(url) {\n      this.current.video = url;\n      this.$bvModal.show('video-modal');\n    }\n  },\n  beforeMount: function beforeMount() {\n    this.result = window.quizmaster.result;\n  },\n  mounted: function mounted() {}\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvanMvY29tcG9uZW50cy9xdWl6emVzL3VzZXJfcmVzdWx0cy9hbnN3ZXJrZXkuanM/ZjY4MyJdLCJuYW1lcyI6WyJWdWUiLCJlbCIsImRhdGEiLCJuYW1lIiwic2luZ3VsYXIiLCJwbHVyYWwiLCJ1cmwiLCJwYXRoIiwicmVzb3VyY2UiLCJsb2FkaW5nIiwicmVzdWx0IiwiaWQiLCJjdXJyZW50IiwidmlkZW8iLCJtZXRob2RzIiwidmlld01vdmllIiwiJGJ2TW9kYWwiLCJzaG93IiwiYmVmb3JlTW91bnQiLCJ3aW5kb3ciLCJxdWl6bWFzdGVyIiwibW91bnRlZCJdLCJtYXBwaW5ncyI6IkFBQUEsSUFBSUEsR0FBSixDQUFRO0FBQ0pDLElBQUUsRUFBRSxZQURBO0FBRUpDLE1BQUksRUFBRTtBQUNGQyxRQUFJLEVBQUU7QUFDRkMsY0FBUSxFQUFFLFlBRFI7QUFFRkMsWUFBTSxFQUFFO0FBRk4sS0FESjtBQUtGQyxPQUFHLEVBQUU7QUFDREMsVUFBSSxFQUFFO0FBQ0ZDLGdCQUFRLEVBQUU7QUFEUjtBQURMLEtBTEg7QUFVRkMsV0FBTyxFQUFFLEtBVlA7QUFXRkMsVUFBTSxFQUFFO0FBQ0pDLFFBQUUsRUFBRTtBQURBLEtBWE47QUFjRkMsV0FBTyxFQUFFO0FBQ0xDLFdBQUssRUFBRTtBQURGO0FBZFAsR0FGRjtBQW9CSkMsU0FBTyxFQUFFO0FBQ0xDLGFBQVMsRUFBRSxtQkFBVVQsR0FBVixFQUFlO0FBQ3RCLFdBQUtNLE9BQUwsQ0FBYUMsS0FBYixHQUFxQlAsR0FBckI7QUFDQSxXQUFLVSxRQUFMLENBQWNDLElBQWQsQ0FBbUIsYUFBbkI7QUFDSDtBQUpJLEdBcEJMO0FBMEJKQyxhQUFXLEVBQUUsdUJBQVk7QUFDckIsU0FBS1IsTUFBTCxHQUFjUyxNQUFNLENBQUNDLFVBQVAsQ0FBa0JWLE1BQWhDO0FBQ0gsR0E1Qkc7QUE2QkpXLFNBQU8sRUFBRSxtQkFBWSxDQUFFO0FBN0JuQixDQUFSIiwiZmlsZSI6Ii4vcmVzb3VyY2VzL2pzL2NvbXBvbmVudHMvcXVpenplcy91c2VyX3Jlc3VsdHMvYW5zd2Vya2V5LmpzLmpzIiwic291cmNlc0NvbnRlbnQiOlsibmV3IFZ1ZSh7XHJcbiAgICBlbDogJyNjb21wb25lbnQnLFxyXG4gICAgZGF0YToge1xyXG4gICAgICAgIG5hbWU6IHtcclxuICAgICAgICAgICAgc2luZ3VsYXI6ICdVc2VyUmVzdWx0JyxcclxuICAgICAgICAgICAgcGx1cmFsOiAnVXNlclJlc3VsdHMnLFxyXG4gICAgICAgIH0sXHJcbiAgICAgICAgdXJsOiB7XHJcbiAgICAgICAgICAgIHBhdGg6IHtcclxuICAgICAgICAgICAgICAgIHJlc291cmNlOiAnL3F1aXp6ZXMvdXNlcl9yZXN1bHRzJyxcclxuICAgICAgICAgICAgfSxcclxuICAgICAgICB9LFxyXG4gICAgICAgIGxvYWRpbmc6IGZhbHNlLFxyXG4gICAgICAgIHJlc3VsdDoge1xyXG4gICAgICAgICAgICBpZDogJycsXHJcbiAgICAgICAgfSxcclxuICAgICAgICBjdXJyZW50OiB7XHJcbiAgICAgICAgICAgIHZpZGVvOiAnJyxcclxuICAgICAgICB9LFxyXG4gICAgfSxcclxuICAgIG1ldGhvZHM6IHtcclxuICAgICAgICB2aWV3TW92aWU6IGZ1bmN0aW9uICh1cmwpIHtcclxuICAgICAgICAgICAgdGhpcy5jdXJyZW50LnZpZGVvID0gdXJsXHJcbiAgICAgICAgICAgIHRoaXMuJGJ2TW9kYWwuc2hvdygndmlkZW8tbW9kYWwnKVxyXG4gICAgICAgIH0sXHJcbiAgICB9LFxyXG4gICAgYmVmb3JlTW91bnQ6IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB0aGlzLnJlc3VsdCA9IHdpbmRvdy5xdWl6bWFzdGVyLnJlc3VsdFxyXG4gICAgfSxcclxuICAgIG1vdW50ZWQ6IGZ1bmN0aW9uICgpIHt9LFxyXG59KVxyXG4iXSwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./resources/js/components/quizzes/user_results/answerkey.js\n");

/***/ }),

/***/ 50:
/*!*************************************************************************!*\
  !*** multi ./resources/js/components/quizzes/user_results/answerkey.js ***!
  \*************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! F:\Project\laravel\quizemaster\code\resources\js\components\quizzes\user_results\answerkey.js */"./resources/js/components/quizzes/user_results/answerkey.js");


/***/ })

/******/ });