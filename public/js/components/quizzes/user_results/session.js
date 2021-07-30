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
/******/ 	return __webpack_require__(__webpack_require__.s = 51);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/components/quizzes/user_results/session.js":
/*!*****************************************************************!*\
  !*** ./resources/js/components/quizzes/user_results/session.js ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("// TO DO: Integrate the rrweb player or make your own session player.\n// window.rrweb = require('rrweb');\n// window.rrwebPlayer = require('rrweb-player');\nnew Vue({\n  el: '#component',\n  data: {\n    name: {\n      singular: 'UserResult',\n      plural: 'UserResults'\n    },\n    url: {\n      path: {\n        resource: '/quizzes/user_results'\n      }\n    },\n    loading: false,\n    result: {\n      id: '',\n      session: []\n    }\n  },\n  methods: {\n    getEvents: function getEvents() {\n      var that = this;\n      that.loading = true;\n      axios.get(this.url.path.resource + '/' + that.result.id + '/' + 'logs/session').then(function (response) {\n        that.result.session = response.data;\n        that.loading = false;\n        new rrwebPlayer({\n          target: document.getElementById('session-player-area'),\n          // customizable root element\n          data: {\n            events: that.result.session,\n            autoPlay: true\n          }\n        });\n      });\n    }\n  },\n  beforeMount: function beforeMount() {\n    this.result.id = Result.id;\n  },\n  mounted: function mounted() {\n    this.getEvents();\n  }\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvanMvY29tcG9uZW50cy9xdWl6emVzL3VzZXJfcmVzdWx0cy9zZXNzaW9uLmpzP2I1MzYiXSwibmFtZXMiOlsiVnVlIiwiZWwiLCJkYXRhIiwibmFtZSIsInNpbmd1bGFyIiwicGx1cmFsIiwidXJsIiwicGF0aCIsInJlc291cmNlIiwibG9hZGluZyIsInJlc3VsdCIsImlkIiwic2Vzc2lvbiIsIm1ldGhvZHMiLCJnZXRFdmVudHMiLCJ0aGF0IiwiYXhpb3MiLCJnZXQiLCJ0aGVuIiwicmVzcG9uc2UiLCJycndlYlBsYXllciIsInRhcmdldCIsImRvY3VtZW50IiwiZ2V0RWxlbWVudEJ5SWQiLCJldmVudHMiLCJhdXRvUGxheSIsImJlZm9yZU1vdW50IiwiUmVzdWx0IiwibW91bnRlZCJdLCJtYXBwaW5ncyI6IkFBQUE7QUFDQTtBQUNBO0FBRUEsSUFBSUEsR0FBSixDQUFRO0FBQ0pDLElBQUUsRUFBRSxZQURBO0FBRUpDLE1BQUksRUFBRTtBQUNGQyxRQUFJLEVBQUU7QUFDRkMsY0FBUSxFQUFFLFlBRFI7QUFFRkMsWUFBTSxFQUFFO0FBRk4sS0FESjtBQUtGQyxPQUFHLEVBQUU7QUFDREMsVUFBSSxFQUFFO0FBQ0ZDLGdCQUFRLEVBQUU7QUFEUjtBQURMLEtBTEg7QUFVRkMsV0FBTyxFQUFFLEtBVlA7QUFXRkMsVUFBTSxFQUFFO0FBQ0pDLFFBQUUsRUFBRSxFQURBO0FBRUpDLGFBQU8sRUFBRTtBQUZMO0FBWE4sR0FGRjtBQWtCSkMsU0FBTyxFQUFFO0FBQ0xDLGFBQVMsRUFBRSxxQkFBWTtBQUNuQixVQUFJQyxJQUFJLEdBQUcsSUFBWDtBQUVBQSxVQUFJLENBQUNOLE9BQUwsR0FBZSxJQUFmO0FBRUFPLFdBQUssQ0FDQUMsR0FETCxDQUVRLEtBQUtYLEdBQUwsQ0FBU0MsSUFBVCxDQUFjQyxRQUFkLEdBQ0ksR0FESixHQUVJTyxJQUFJLENBQUNMLE1BQUwsQ0FBWUMsRUFGaEIsR0FHSSxHQUhKLEdBSUksY0FOWixFQVFLTyxJQVJMLENBUVUsVUFBVUMsUUFBVixFQUFvQjtBQUN0QkosWUFBSSxDQUFDTCxNQUFMLENBQVlFLE9BQVosR0FBc0JPLFFBQVEsQ0FBQ2pCLElBQS9CO0FBQ0FhLFlBQUksQ0FBQ04sT0FBTCxHQUFlLEtBQWY7QUFFQSxZQUFJVyxXQUFKLENBQWdCO0FBQ1pDLGdCQUFNLEVBQUVDLFFBQVEsQ0FBQ0MsY0FBVCxDQUF3QixxQkFBeEIsQ0FESTtBQUM0QztBQUN4RHJCLGNBQUksRUFBRTtBQUNGc0Isa0JBQU0sRUFBRVQsSUFBSSxDQUFDTCxNQUFMLENBQVlFLE9BRGxCO0FBRUZhLG9CQUFRLEVBQUU7QUFGUjtBQUZNLFNBQWhCO0FBT0gsT0FuQkw7QUFvQkg7QUExQkksR0FsQkw7QUE4Q0pDLGFBQVcsRUFBRSx1QkFBWTtBQUNyQixTQUFLaEIsTUFBTCxDQUFZQyxFQUFaLEdBQWlCZ0IsTUFBTSxDQUFDaEIsRUFBeEI7QUFDSCxHQWhERztBQWlESmlCLFNBQU8sRUFBRSxtQkFBWTtBQUNqQixTQUFLZCxTQUFMO0FBQ0g7QUFuREcsQ0FBUiIsImZpbGUiOiIuL3Jlc291cmNlcy9qcy9jb21wb25lbnRzL3F1aXp6ZXMvdXNlcl9yZXN1bHRzL3Nlc3Npb24uanMuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvLyBUTyBETzogSW50ZWdyYXRlIHRoZSBycndlYiBwbGF5ZXIgb3IgbWFrZSB5b3VyIG93biBzZXNzaW9uIHBsYXllci5cclxuLy8gd2luZG93LnJyd2ViID0gcmVxdWlyZSgncnJ3ZWInKTtcclxuLy8gd2luZG93LnJyd2ViUGxheWVyID0gcmVxdWlyZSgncnJ3ZWItcGxheWVyJyk7XHJcblxyXG5uZXcgVnVlKHtcclxuICAgIGVsOiAnI2NvbXBvbmVudCcsXHJcbiAgICBkYXRhOiB7XHJcbiAgICAgICAgbmFtZToge1xyXG4gICAgICAgICAgICBzaW5ndWxhcjogJ1VzZXJSZXN1bHQnLFxyXG4gICAgICAgICAgICBwbHVyYWw6ICdVc2VyUmVzdWx0cycsXHJcbiAgICAgICAgfSxcclxuICAgICAgICB1cmw6IHtcclxuICAgICAgICAgICAgcGF0aDoge1xyXG4gICAgICAgICAgICAgICAgcmVzb3VyY2U6ICcvcXVpenplcy91c2VyX3Jlc3VsdHMnLFxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgIH0sXHJcbiAgICAgICAgbG9hZGluZzogZmFsc2UsXHJcbiAgICAgICAgcmVzdWx0OiB7XHJcbiAgICAgICAgICAgIGlkOiAnJyxcclxuICAgICAgICAgICAgc2Vzc2lvbjogW10sXHJcbiAgICAgICAgfSxcclxuICAgIH0sXHJcbiAgICBtZXRob2RzOiB7XHJcbiAgICAgICAgZ2V0RXZlbnRzOiBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIGxldCB0aGF0ID0gdGhpc1xyXG5cclxuICAgICAgICAgICAgdGhhdC5sb2FkaW5nID0gdHJ1ZVxyXG5cclxuICAgICAgICAgICAgYXhpb3NcclxuICAgICAgICAgICAgICAgIC5nZXQoXHJcbiAgICAgICAgICAgICAgICAgICAgdGhpcy51cmwucGF0aC5yZXNvdXJjZSArXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICcvJyArXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoYXQucmVzdWx0LmlkICtcclxuICAgICAgICAgICAgICAgICAgICAgICAgJy8nICtcclxuICAgICAgICAgICAgICAgICAgICAgICAgJ2xvZ3Mvc2Vzc2lvbidcclxuICAgICAgICAgICAgICAgIClcclxuICAgICAgICAgICAgICAgIC50aGVuKGZ1bmN0aW9uIChyZXNwb25zZSkge1xyXG4gICAgICAgICAgICAgICAgICAgIHRoYXQucmVzdWx0LnNlc3Npb24gPSByZXNwb25zZS5kYXRhXHJcbiAgICAgICAgICAgICAgICAgICAgdGhhdC5sb2FkaW5nID0gZmFsc2VcclxuXHJcbiAgICAgICAgICAgICAgICAgICAgbmV3IHJyd2ViUGxheWVyKHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgdGFyZ2V0OiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnc2Vzc2lvbi1wbGF5ZXItYXJlYScpLCAvLyBjdXN0b21pemFibGUgcm9vdCBlbGVtZW50XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGRhdGE6IHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGV2ZW50czogdGhhdC5yZXN1bHQuc2Vzc2lvbixcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGF1dG9QbGF5OiB0cnVlLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAgICAgICAgIH0pXHJcbiAgICAgICAgICAgICAgICB9KVxyXG4gICAgICAgIH0sXHJcbiAgICB9LFxyXG4gICAgYmVmb3JlTW91bnQ6IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB0aGlzLnJlc3VsdC5pZCA9IFJlc3VsdC5pZFxyXG4gICAgfSxcclxuICAgIG1vdW50ZWQ6IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB0aGlzLmdldEV2ZW50cygpXHJcbiAgICB9LFxyXG59KVxyXG4iXSwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./resources/js/components/quizzes/user_results/session.js\n");

/***/ }),

/***/ 51:
/*!***********************************************************************!*\
  !*** multi ./resources/js/components/quizzes/user_results/session.js ***!
  \***********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! F:\Project\laravel\quizemaster\code\resources\js\components\quizzes\user_results\session.js */"./resources/js/components/quizzes/user_results/session.js");


/***/ })

/******/ });