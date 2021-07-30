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
/******/ 	return __webpack_require__(__webpack_require__.s = 53);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/components/quizzes/user_results/eyetracking.js":
/*!*********************************************************************!*\
  !*** ./resources/js/components/quizzes/user_results/eyetracking.js ***!
  \*********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("new Vue({\n  el: '#component',\n  data: {\n    name: {\n      singular: 'UserResult',\n      plural: 'UserResults'\n    },\n    url: {\n      path: {\n        resource: '/quizzes/user_results'\n      }\n    },\n    loading: false,\n    selected: null,\n    result: {\n      id: ''\n    }\n  },\n  methods: {\n    changeSection: function changeSection() {}\n  },\n  beforeMount: function beforeMount() {\n    this.result.id = window.quizmaster.result.id;\n    this.selected = window.quizmaster.selected;\n  },\n  mounted: function mounted() {\n    jQuery('.table-passage .content .s-text').each(function () {\n      var fg = jQuery(this).data('fgcolor');\n      var bg = jQuery(this).data('bgcolor');\n\n      if (fg !== undefined && bg !== undefined) {\n        var css = 'color: ' + fg + '; background-color: ' + bg + ';';\n        jQuery(this).attr('style', css);\n      }\n    });\n    jQuery('.table-question .question .s-text').each(function () {\n      var fg = jQuery(this).data('fgcolor');\n      var bg = jQuery(this).data('bgcolor');\n\n      if (fg !== undefined && bg !== undefined) {\n        var css = 'color: ' + fg + '; background-color: ' + bg + ';';\n        jQuery(this).attr('style', css);\n      }\n    });\n  }\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvanMvY29tcG9uZW50cy9xdWl6emVzL3VzZXJfcmVzdWx0cy9leWV0cmFja2luZy5qcz80OWY4Il0sIm5hbWVzIjpbIlZ1ZSIsImVsIiwiZGF0YSIsIm5hbWUiLCJzaW5ndWxhciIsInBsdXJhbCIsInVybCIsInBhdGgiLCJyZXNvdXJjZSIsImxvYWRpbmciLCJzZWxlY3RlZCIsInJlc3VsdCIsImlkIiwibWV0aG9kcyIsImNoYW5nZVNlY3Rpb24iLCJiZWZvcmVNb3VudCIsIndpbmRvdyIsInF1aXptYXN0ZXIiLCJtb3VudGVkIiwialF1ZXJ5IiwiZWFjaCIsImZnIiwiYmciLCJ1bmRlZmluZWQiLCJjc3MiLCJhdHRyIl0sIm1hcHBpbmdzIjoiQUFBQSxJQUFJQSxHQUFKLENBQVE7QUFDSkMsSUFBRSxFQUFFLFlBREE7QUFFSkMsTUFBSSxFQUFFO0FBQ0ZDLFFBQUksRUFBRTtBQUNGQyxjQUFRLEVBQUUsWUFEUjtBQUVGQyxZQUFNLEVBQUU7QUFGTixLQURKO0FBS0ZDLE9BQUcsRUFBRTtBQUNEQyxVQUFJLEVBQUU7QUFDRkMsZ0JBQVEsRUFBRTtBQURSO0FBREwsS0FMSDtBQVVGQyxXQUFPLEVBQUUsS0FWUDtBQVdGQyxZQUFRLEVBQUUsSUFYUjtBQVlGQyxVQUFNLEVBQUU7QUFDSkMsUUFBRSxFQUFFO0FBREE7QUFaTixHQUZGO0FBa0JKQyxTQUFPLEVBQUU7QUFDTEMsaUJBQWEsRUFBRSx5QkFBWSxDQUFFO0FBRHhCLEdBbEJMO0FBcUJKQyxhQUFXLEVBQUUsdUJBQVk7QUFDckIsU0FBS0osTUFBTCxDQUFZQyxFQUFaLEdBQWlCSSxNQUFNLENBQUNDLFVBQVAsQ0FBa0JOLE1BQWxCLENBQXlCQyxFQUExQztBQUNBLFNBQUtGLFFBQUwsR0FBZ0JNLE1BQU0sQ0FBQ0MsVUFBUCxDQUFrQlAsUUFBbEM7QUFDSCxHQXhCRztBQXlCSlEsU0FBTyxFQUFFLG1CQUFZO0FBQ2pCQyxVQUFNLENBQUMsaUNBQUQsQ0FBTixDQUEwQ0MsSUFBMUMsQ0FBK0MsWUFBWTtBQUN2RCxVQUFJQyxFQUFFLEdBQUdGLE1BQU0sQ0FBQyxJQUFELENBQU4sQ0FBYWpCLElBQWIsQ0FBa0IsU0FBbEIsQ0FBVDtBQUNBLFVBQUlvQixFQUFFLEdBQUdILE1BQU0sQ0FBQyxJQUFELENBQU4sQ0FBYWpCLElBQWIsQ0FBa0IsU0FBbEIsQ0FBVDs7QUFDQSxVQUFJbUIsRUFBRSxLQUFLRSxTQUFQLElBQW9CRCxFQUFFLEtBQUtDLFNBQS9CLEVBQTBDO0FBQ3RDLFlBQUlDLEdBQUcsR0FBRyxZQUFZSCxFQUFaLEdBQWlCLHNCQUFqQixHQUEwQ0MsRUFBMUMsR0FBK0MsR0FBekQ7QUFDQUgsY0FBTSxDQUFDLElBQUQsQ0FBTixDQUFhTSxJQUFiLENBQWtCLE9BQWxCLEVBQTJCRCxHQUEzQjtBQUNIO0FBQ0osS0FQRDtBQVNBTCxVQUFNLENBQUMsbUNBQUQsQ0FBTixDQUE0Q0MsSUFBNUMsQ0FBaUQsWUFBWTtBQUN6RCxVQUFJQyxFQUFFLEdBQUdGLE1BQU0sQ0FBQyxJQUFELENBQU4sQ0FBYWpCLElBQWIsQ0FBa0IsU0FBbEIsQ0FBVDtBQUNBLFVBQUlvQixFQUFFLEdBQUdILE1BQU0sQ0FBQyxJQUFELENBQU4sQ0FBYWpCLElBQWIsQ0FBa0IsU0FBbEIsQ0FBVDs7QUFDQSxVQUFJbUIsRUFBRSxLQUFLRSxTQUFQLElBQW9CRCxFQUFFLEtBQUtDLFNBQS9CLEVBQTBDO0FBQ3RDLFlBQUlDLEdBQUcsR0FBRyxZQUFZSCxFQUFaLEdBQWlCLHNCQUFqQixHQUEwQ0MsRUFBMUMsR0FBK0MsR0FBekQ7QUFDQUgsY0FBTSxDQUFDLElBQUQsQ0FBTixDQUFhTSxJQUFiLENBQWtCLE9BQWxCLEVBQTJCRCxHQUEzQjtBQUNIO0FBQ0osS0FQRDtBQVFIO0FBM0NHLENBQVIiLCJmaWxlIjoiLi9yZXNvdXJjZXMvanMvY29tcG9uZW50cy9xdWl6emVzL3VzZXJfcmVzdWx0cy9leWV0cmFja2luZy5qcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIm5ldyBWdWUoe1xyXG4gICAgZWw6ICcjY29tcG9uZW50JyxcclxuICAgIGRhdGE6IHtcclxuICAgICAgICBuYW1lOiB7XHJcbiAgICAgICAgICAgIHNpbmd1bGFyOiAnVXNlclJlc3VsdCcsXHJcbiAgICAgICAgICAgIHBsdXJhbDogJ1VzZXJSZXN1bHRzJyxcclxuICAgICAgICB9LFxyXG4gICAgICAgIHVybDoge1xyXG4gICAgICAgICAgICBwYXRoOiB7XHJcbiAgICAgICAgICAgICAgICByZXNvdXJjZTogJy9xdWl6emVzL3VzZXJfcmVzdWx0cycsXHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgfSxcclxuICAgICAgICBsb2FkaW5nOiBmYWxzZSxcclxuICAgICAgICBzZWxlY3RlZDogbnVsbCxcclxuICAgICAgICByZXN1bHQ6IHtcclxuICAgICAgICAgICAgaWQ6ICcnLFxyXG4gICAgICAgIH0sXHJcbiAgICB9LFxyXG4gICAgbWV0aG9kczoge1xyXG4gICAgICAgIGNoYW5nZVNlY3Rpb246IGZ1bmN0aW9uICgpIHt9LFxyXG4gICAgfSxcclxuICAgIGJlZm9yZU1vdW50OiBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgdGhpcy5yZXN1bHQuaWQgPSB3aW5kb3cucXVpem1hc3Rlci5yZXN1bHQuaWRcclxuICAgICAgICB0aGlzLnNlbGVjdGVkID0gd2luZG93LnF1aXptYXN0ZXIuc2VsZWN0ZWRcclxuICAgIH0sXHJcbiAgICBtb3VudGVkOiBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgalF1ZXJ5KCcudGFibGUtcGFzc2FnZSAuY29udGVudCAucy10ZXh0JykuZWFjaChmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIGxldCBmZyA9IGpRdWVyeSh0aGlzKS5kYXRhKCdmZ2NvbG9yJylcclxuICAgICAgICAgICAgbGV0IGJnID0galF1ZXJ5KHRoaXMpLmRhdGEoJ2JnY29sb3InKVxyXG4gICAgICAgICAgICBpZiAoZmcgIT09IHVuZGVmaW5lZCAmJiBiZyAhPT0gdW5kZWZpbmVkKSB7XHJcbiAgICAgICAgICAgICAgICBsZXQgY3NzID0gJ2NvbG9yOiAnICsgZmcgKyAnOyBiYWNrZ3JvdW5kLWNvbG9yOiAnICsgYmcgKyAnOydcclxuICAgICAgICAgICAgICAgIGpRdWVyeSh0aGlzKS5hdHRyKCdzdHlsZScsIGNzcylcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pXHJcblxyXG4gICAgICAgIGpRdWVyeSgnLnRhYmxlLXF1ZXN0aW9uIC5xdWVzdGlvbiAucy10ZXh0JykuZWFjaChmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIGxldCBmZyA9IGpRdWVyeSh0aGlzKS5kYXRhKCdmZ2NvbG9yJylcclxuICAgICAgICAgICAgbGV0IGJnID0galF1ZXJ5KHRoaXMpLmRhdGEoJ2JnY29sb3InKVxyXG4gICAgICAgICAgICBpZiAoZmcgIT09IHVuZGVmaW5lZCAmJiBiZyAhPT0gdW5kZWZpbmVkKSB7XHJcbiAgICAgICAgICAgICAgICBsZXQgY3NzID0gJ2NvbG9yOiAnICsgZmcgKyAnOyBiYWNrZ3JvdW5kLWNvbG9yOiAnICsgYmcgKyAnOydcclxuICAgICAgICAgICAgICAgIGpRdWVyeSh0aGlzKS5hdHRyKCdzdHlsZScsIGNzcylcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pXHJcbiAgICB9LFxyXG59KVxyXG4iXSwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./resources/js/components/quizzes/user_results/eyetracking.js\n");

/***/ }),

/***/ 53:
/*!***************************************************************************!*\
  !*** multi ./resources/js/components/quizzes/user_results/eyetracking.js ***!
  \***************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! F:\Project\laravel\quizemaster\code\resources\js\components\quizzes\user_results\eyetracking.js */"./resources/js/components/quizzes/user_results/eyetracking.js");


/***/ })

/******/ });