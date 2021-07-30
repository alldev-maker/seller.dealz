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
/******/ 	return __webpack_require__(__webpack_require__.s = 55);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/components/quizzes/user_results/score.js":
/*!***************************************************************!*\
  !*** ./resources/js/components/quizzes/user_results/score.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("new Vue({\n  el: '#component',\n  data: {\n    name: {\n      singular: 'UserResult',\n      plural: 'UserResults'\n    },\n    url: {\n      path: {\n        resource: '/quizzes/user_results'\n      }\n    },\n    loading: false,\n    result: {\n      id: ''\n    },\n    data: {},\n    charts: {\n      score: {\n        element: null,\n        data: []\n      }\n    }\n  },\n  methods: {\n    generateGauge: function generateGauge(data) {\n      var that = this;\n      that.charts.score.data = data.scores;\n      that.charts.score.data.percentage = Math.round(data.scores.earned / data.scores.total * 100);\n    }\n  },\n  beforeMount: function beforeMount() {\n    this.result = window.quizmaster.result;\n  },\n  mounted: function mounted() {\n    var that = this;\n    that.loading = true;\n    axios.get(this.url.path.resource + '/' + that.result.id + '/logs/summary').then(function (response) {\n      that.data = response.data;\n      that.generateGauge(that.data);\n      that.loading = false;\n    });\n  }\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvanMvY29tcG9uZW50cy9xdWl6emVzL3VzZXJfcmVzdWx0cy9zY29yZS5qcz81MWNmIl0sIm5hbWVzIjpbIlZ1ZSIsImVsIiwiZGF0YSIsIm5hbWUiLCJzaW5ndWxhciIsInBsdXJhbCIsInVybCIsInBhdGgiLCJyZXNvdXJjZSIsImxvYWRpbmciLCJyZXN1bHQiLCJpZCIsImNoYXJ0cyIsInNjb3JlIiwiZWxlbWVudCIsIm1ldGhvZHMiLCJnZW5lcmF0ZUdhdWdlIiwidGhhdCIsInNjb3JlcyIsInBlcmNlbnRhZ2UiLCJNYXRoIiwicm91bmQiLCJlYXJuZWQiLCJ0b3RhbCIsImJlZm9yZU1vdW50Iiwid2luZG93IiwicXVpem1hc3RlciIsIm1vdW50ZWQiLCJheGlvcyIsImdldCIsInRoZW4iLCJyZXNwb25zZSJdLCJtYXBwaW5ncyI6IkFBQUEsSUFBSUEsR0FBSixDQUFRO0FBQ0pDLElBQUUsRUFBRSxZQURBO0FBRUpDLE1BQUksRUFBRTtBQUNGQyxRQUFJLEVBQUU7QUFDRkMsY0FBUSxFQUFFLFlBRFI7QUFFRkMsWUFBTSxFQUFFO0FBRk4sS0FESjtBQUtGQyxPQUFHLEVBQUU7QUFDREMsVUFBSSxFQUFFO0FBQ0ZDLGdCQUFRLEVBQUU7QUFEUjtBQURMLEtBTEg7QUFVRkMsV0FBTyxFQUFFLEtBVlA7QUFXRkMsVUFBTSxFQUFFO0FBQ0pDLFFBQUUsRUFBRTtBQURBLEtBWE47QUFjRlQsUUFBSSxFQUFFLEVBZEo7QUFlRlUsVUFBTSxFQUFFO0FBQ0pDLFdBQUssRUFBRTtBQUNIQyxlQUFPLEVBQUUsSUFETjtBQUVIWixZQUFJLEVBQUU7QUFGSDtBQURIO0FBZk4sR0FGRjtBQXdCSmEsU0FBTyxFQUFFO0FBQ0xDLGlCQUFhLEVBQUUsdUJBQVVkLElBQVYsRUFBZ0I7QUFDM0IsVUFBSWUsSUFBSSxHQUFHLElBQVg7QUFDQUEsVUFBSSxDQUFDTCxNQUFMLENBQVlDLEtBQVosQ0FBa0JYLElBQWxCLEdBQXlCQSxJQUFJLENBQUNnQixNQUE5QjtBQUNBRCxVQUFJLENBQUNMLE1BQUwsQ0FBWUMsS0FBWixDQUFrQlgsSUFBbEIsQ0FBdUJpQixVQUF2QixHQUFvQ0MsSUFBSSxDQUFDQyxLQUFMLENBQy9CbkIsSUFBSSxDQUFDZ0IsTUFBTCxDQUFZSSxNQUFaLEdBQXFCcEIsSUFBSSxDQUFDZ0IsTUFBTCxDQUFZSyxLQUFsQyxHQUEyQyxHQURYLENBQXBDO0FBR0g7QUFQSSxHQXhCTDtBQWlDSkMsYUFBVyxFQUFFLHVCQUFZO0FBQ3JCLFNBQUtkLE1BQUwsR0FBY2UsTUFBTSxDQUFDQyxVQUFQLENBQWtCaEIsTUFBaEM7QUFDSCxHQW5DRztBQW9DSmlCLFNBQU8sRUFBRSxtQkFBWTtBQUNqQixRQUFJVixJQUFJLEdBQUcsSUFBWDtBQUVBQSxRQUFJLENBQUNSLE9BQUwsR0FBZSxJQUFmO0FBQ0FtQixTQUFLLENBQ0FDLEdBREwsQ0FFUSxLQUFLdkIsR0FBTCxDQUFTQyxJQUFULENBQWNDLFFBQWQsR0FBeUIsR0FBekIsR0FBK0JTLElBQUksQ0FBQ1AsTUFBTCxDQUFZQyxFQUEzQyxHQUFnRCxlQUZ4RCxFQUlLbUIsSUFKTCxDQUlVLFVBQVVDLFFBQVYsRUFBb0I7QUFDdEJkLFVBQUksQ0FBQ2YsSUFBTCxHQUFZNkIsUUFBUSxDQUFDN0IsSUFBckI7QUFDQWUsVUFBSSxDQUFDRCxhQUFMLENBQW1CQyxJQUFJLENBQUNmLElBQXhCO0FBQ0FlLFVBQUksQ0FBQ1IsT0FBTCxHQUFlLEtBQWY7QUFDSCxLQVJMO0FBU0g7QUFqREcsQ0FBUiIsImZpbGUiOiIuL3Jlc291cmNlcy9qcy9jb21wb25lbnRzL3F1aXp6ZXMvdXNlcl9yZXN1bHRzL3Njb3JlLmpzLmpzIiwic291cmNlc0NvbnRlbnQiOlsibmV3IFZ1ZSh7XHJcbiAgICBlbDogJyNjb21wb25lbnQnLFxyXG4gICAgZGF0YToge1xyXG4gICAgICAgIG5hbWU6IHtcclxuICAgICAgICAgICAgc2luZ3VsYXI6ICdVc2VyUmVzdWx0JyxcclxuICAgICAgICAgICAgcGx1cmFsOiAnVXNlclJlc3VsdHMnLFxyXG4gICAgICAgIH0sXHJcbiAgICAgICAgdXJsOiB7XHJcbiAgICAgICAgICAgIHBhdGg6IHtcclxuICAgICAgICAgICAgICAgIHJlc291cmNlOiAnL3F1aXp6ZXMvdXNlcl9yZXN1bHRzJyxcclxuICAgICAgICAgICAgfSxcclxuICAgICAgICB9LFxyXG4gICAgICAgIGxvYWRpbmc6IGZhbHNlLFxyXG4gICAgICAgIHJlc3VsdDoge1xyXG4gICAgICAgICAgICBpZDogJycsXHJcbiAgICAgICAgfSxcclxuICAgICAgICBkYXRhOiB7fSxcclxuICAgICAgICBjaGFydHM6IHtcclxuICAgICAgICAgICAgc2NvcmU6IHtcclxuICAgICAgICAgICAgICAgIGVsZW1lbnQ6IG51bGwsXHJcbiAgICAgICAgICAgICAgICBkYXRhOiBbXSxcclxuICAgICAgICAgICAgfSxcclxuICAgICAgICB9LFxyXG4gICAgfSxcclxuICAgIG1ldGhvZHM6IHtcclxuICAgICAgICBnZW5lcmF0ZUdhdWdlOiBmdW5jdGlvbiAoZGF0YSkge1xyXG4gICAgICAgICAgICBsZXQgdGhhdCA9IHRoaXNcclxuICAgICAgICAgICAgdGhhdC5jaGFydHMuc2NvcmUuZGF0YSA9IGRhdGEuc2NvcmVzXHJcbiAgICAgICAgICAgIHRoYXQuY2hhcnRzLnNjb3JlLmRhdGEucGVyY2VudGFnZSA9IE1hdGgucm91bmQoXHJcbiAgICAgICAgICAgICAgICAoZGF0YS5zY29yZXMuZWFybmVkIC8gZGF0YS5zY29yZXMudG90YWwpICogMTAwXHJcbiAgICAgICAgICAgIClcclxuICAgICAgICB9LFxyXG4gICAgfSxcclxuICAgIGJlZm9yZU1vdW50OiBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgdGhpcy5yZXN1bHQgPSB3aW5kb3cucXVpem1hc3Rlci5yZXN1bHRcclxuICAgIH0sXHJcbiAgICBtb3VudGVkOiBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgbGV0IHRoYXQgPSB0aGlzXHJcblxyXG4gICAgICAgIHRoYXQubG9hZGluZyA9IHRydWVcclxuICAgICAgICBheGlvc1xyXG4gICAgICAgICAgICAuZ2V0KFxyXG4gICAgICAgICAgICAgICAgdGhpcy51cmwucGF0aC5yZXNvdXJjZSArICcvJyArIHRoYXQucmVzdWx0LmlkICsgJy9sb2dzL3N1bW1hcnknXHJcbiAgICAgICAgICAgIClcclxuICAgICAgICAgICAgLnRoZW4oZnVuY3Rpb24gKHJlc3BvbnNlKSB7XHJcbiAgICAgICAgICAgICAgICB0aGF0LmRhdGEgPSByZXNwb25zZS5kYXRhXHJcbiAgICAgICAgICAgICAgICB0aGF0LmdlbmVyYXRlR2F1Z2UodGhhdC5kYXRhKVxyXG4gICAgICAgICAgICAgICAgdGhhdC5sb2FkaW5nID0gZmFsc2VcclxuICAgICAgICAgICAgfSlcclxuICAgIH0sXHJcbn0pXHJcbiJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./resources/js/components/quizzes/user_results/score.js\n");

/***/ }),

/***/ 55:
/*!*********************************************************************!*\
  !*** multi ./resources/js/components/quizzes/user_results/score.js ***!
  \*********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! F:\Project\laravel\quizemaster\code\resources\js\components\quizzes\user_results\score.js */"./resources/js/components/quizzes/user_results/score.js");


/***/ })

/******/ });