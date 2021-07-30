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
/******/ 	return __webpack_require__(__webpack_require__.s = 26);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/components/quizzes/takerquizzes/view.js":
/*!**************************************************************!*\
  !*** ./resources/js/components/quizzes/takerquizzes/view.js ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("jQuery('.slides').slick({\n  swipe: false,\n  infinite: false,\n  dots: false,\n  prevArrow: false,\n  nextArrow: false\n}).on('beforeChange', function (event, slick, prev, index) {\n  if (index === 0) {\n    jQuery('.buttons .btn-prev').attr('disabled', true);\n  } else {\n    jQuery('.buttons .btn-prev').attr('disabled', false);\n  }\n\n  if (slick.$slides.length === index + slick.options.slidesToScroll) {\n    jQuery('.buttons .btn-next').hide();\n    jQuery('.buttons .btn-submit').removeClass('d-none').show();\n  } else {\n    jQuery('.buttons .btn-next').show();\n    jQuery('.buttons .btn-submit').hide();\n  }\n\n  jQuery('.btn-navbox[data-index]').removeClass('active');\n  jQuery('.btn-navbox[data-index=\"' + index + '\"]').addClass('active');\n  jQuery('.slide-passage[data-index=\"' + index + '\"]').mCustomScrollbar({\n    axis: 'y',\n    theme: 'minimal-dark'\n  });\n  jQuery('.slide-question[data-index=\"' + index + '\"]').mCustomScrollbar({\n    axis: 'y',\n    theme: 'minimal-dark'\n  });\n}).on('afterChange', function (event, slick, index) {});\njQuery('.buttons .btn-prev').on('click', function () {\n  jQuery('.slides').slick('slickPrev');\n});\njQuery('.buttons .btn-next').on('click', function () {\n  jQuery('.slides').slick('slickNext');\n});\njQuery('.btn-navbox').on('click', function () {\n  var i = jQuery(this).data('index');\n  jQuery('.slides').slick('slickGoTo', i);\n});\njQuery('#navbox-area').mCustomScrollbar({\n  axis: 'y',\n  theme: 'minimal-dark'\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvanMvY29tcG9uZW50cy9xdWl6emVzL3Rha2VycXVpenplcy92aWV3LmpzP2M0YzkiXSwibmFtZXMiOlsialF1ZXJ5Iiwic2xpY2siLCJzd2lwZSIsImluZmluaXRlIiwiZG90cyIsInByZXZBcnJvdyIsIm5leHRBcnJvdyIsIm9uIiwiZXZlbnQiLCJwcmV2IiwiaW5kZXgiLCJhdHRyIiwiJHNsaWRlcyIsImxlbmd0aCIsIm9wdGlvbnMiLCJzbGlkZXNUb1Njcm9sbCIsImhpZGUiLCJyZW1vdmVDbGFzcyIsInNob3ciLCJhZGRDbGFzcyIsIm1DdXN0b21TY3JvbGxiYXIiLCJheGlzIiwidGhlbWUiLCJpIiwiZGF0YSJdLCJtYXBwaW5ncyI6IkFBQUFBLE1BQU0sQ0FBQyxTQUFELENBQU4sQ0FDS0MsS0FETCxDQUNXO0FBQ0hDLE9BQUssRUFBRSxLQURKO0FBRUhDLFVBQVEsRUFBRSxLQUZQO0FBR0hDLE1BQUksRUFBRSxLQUhIO0FBSUhDLFdBQVMsRUFBRSxLQUpSO0FBS0hDLFdBQVMsRUFBRTtBQUxSLENBRFgsRUFRS0MsRUFSTCxDQVFRLGNBUlIsRUFRd0IsVUFBVUMsS0FBVixFQUFpQlAsS0FBakIsRUFBd0JRLElBQXhCLEVBQThCQyxLQUE5QixFQUFxQztBQUNyRCxNQUFJQSxLQUFLLEtBQUssQ0FBZCxFQUFpQjtBQUNiVixVQUFNLENBQUMsb0JBQUQsQ0FBTixDQUE2QlcsSUFBN0IsQ0FBa0MsVUFBbEMsRUFBOEMsSUFBOUM7QUFDSCxHQUZELE1BRU87QUFDSFgsVUFBTSxDQUFDLG9CQUFELENBQU4sQ0FBNkJXLElBQTdCLENBQWtDLFVBQWxDLEVBQThDLEtBQTlDO0FBQ0g7O0FBRUQsTUFBSVYsS0FBSyxDQUFDVyxPQUFOLENBQWNDLE1BQWQsS0FBeUJILEtBQUssR0FBR1QsS0FBSyxDQUFDYSxPQUFOLENBQWNDLGNBQW5ELEVBQW1FO0FBQy9EZixVQUFNLENBQUMsb0JBQUQsQ0FBTixDQUE2QmdCLElBQTdCO0FBQ0FoQixVQUFNLENBQUMsc0JBQUQsQ0FBTixDQUErQmlCLFdBQS9CLENBQTJDLFFBQTNDLEVBQXFEQyxJQUFyRDtBQUNILEdBSEQsTUFHTztBQUNIbEIsVUFBTSxDQUFDLG9CQUFELENBQU4sQ0FBNkJrQixJQUE3QjtBQUNBbEIsVUFBTSxDQUFDLHNCQUFELENBQU4sQ0FBK0JnQixJQUEvQjtBQUNIOztBQUVEaEIsUUFBTSxDQUFDLHlCQUFELENBQU4sQ0FBa0NpQixXQUFsQyxDQUE4QyxRQUE5QztBQUNBakIsUUFBTSxDQUFDLDZCQUE2QlUsS0FBN0IsR0FBcUMsSUFBdEMsQ0FBTixDQUFrRFMsUUFBbEQsQ0FBMkQsUUFBM0Q7QUFFQW5CLFFBQU0sQ0FBQyxnQ0FBZ0NVLEtBQWhDLEdBQXdDLElBQXpDLENBQU4sQ0FBcURVLGdCQUFyRCxDQUFzRTtBQUNsRUMsUUFBSSxFQUFFLEdBRDREO0FBRWxFQyxTQUFLLEVBQUU7QUFGMkQsR0FBdEU7QUFLQXRCLFFBQU0sQ0FBQyxpQ0FBaUNVLEtBQWpDLEdBQXlDLElBQTFDLENBQU4sQ0FBc0RVLGdCQUF0RCxDQUF1RTtBQUNuRUMsUUFBSSxFQUFFLEdBRDZEO0FBRW5FQyxTQUFLLEVBQUU7QUFGNEQsR0FBdkU7QUFJSCxDQW5DTCxFQW9DS2YsRUFwQ0wsQ0FvQ1EsYUFwQ1IsRUFvQ3VCLFVBQVVDLEtBQVYsRUFBaUJQLEtBQWpCLEVBQXdCUyxLQUF4QixFQUErQixDQUFFLENBcEN4RDtBQXNDQVYsTUFBTSxDQUFDLG9CQUFELENBQU4sQ0FBNkJPLEVBQTdCLENBQWdDLE9BQWhDLEVBQXlDLFlBQVk7QUFDakRQLFFBQU0sQ0FBQyxTQUFELENBQU4sQ0FBa0JDLEtBQWxCLENBQXdCLFdBQXhCO0FBQ0gsQ0FGRDtBQUlBRCxNQUFNLENBQUMsb0JBQUQsQ0FBTixDQUE2Qk8sRUFBN0IsQ0FBZ0MsT0FBaEMsRUFBeUMsWUFBWTtBQUNqRFAsUUFBTSxDQUFDLFNBQUQsQ0FBTixDQUFrQkMsS0FBbEIsQ0FBd0IsV0FBeEI7QUFDSCxDQUZEO0FBSUFELE1BQU0sQ0FBQyxhQUFELENBQU4sQ0FBc0JPLEVBQXRCLENBQXlCLE9BQXpCLEVBQWtDLFlBQVk7QUFDMUMsTUFBSWdCLENBQUMsR0FBR3ZCLE1BQU0sQ0FBQyxJQUFELENBQU4sQ0FBYXdCLElBQWIsQ0FBa0IsT0FBbEIsQ0FBUjtBQUNBeEIsUUFBTSxDQUFDLFNBQUQsQ0FBTixDQUFrQkMsS0FBbEIsQ0FBd0IsV0FBeEIsRUFBcUNzQixDQUFyQztBQUNILENBSEQ7QUFLQXZCLE1BQU0sQ0FBQyxjQUFELENBQU4sQ0FBdUJvQixnQkFBdkIsQ0FBd0M7QUFDcENDLE1BQUksRUFBRSxHQUQ4QjtBQUVwQ0MsT0FBSyxFQUFFO0FBRjZCLENBQXhDIiwiZmlsZSI6Ii4vcmVzb3VyY2VzL2pzL2NvbXBvbmVudHMvcXVpenplcy90YWtlcnF1aXp6ZXMvdmlldy5qcy5qcyIsInNvdXJjZXNDb250ZW50IjpbImpRdWVyeSgnLnNsaWRlcycpXHJcbiAgICAuc2xpY2soe1xyXG4gICAgICAgIHN3aXBlOiBmYWxzZSxcclxuICAgICAgICBpbmZpbml0ZTogZmFsc2UsXHJcbiAgICAgICAgZG90czogZmFsc2UsXHJcbiAgICAgICAgcHJldkFycm93OiBmYWxzZSxcclxuICAgICAgICBuZXh0QXJyb3c6IGZhbHNlLFxyXG4gICAgfSlcclxuICAgIC5vbignYmVmb3JlQ2hhbmdlJywgZnVuY3Rpb24gKGV2ZW50LCBzbGljaywgcHJldiwgaW5kZXgpIHtcclxuICAgICAgICBpZiAoaW5kZXggPT09IDApIHtcclxuICAgICAgICAgICAgalF1ZXJ5KCcuYnV0dG9ucyAuYnRuLXByZXYnKS5hdHRyKCdkaXNhYmxlZCcsIHRydWUpXHJcbiAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgalF1ZXJ5KCcuYnV0dG9ucyAuYnRuLXByZXYnKS5hdHRyKCdkaXNhYmxlZCcsIGZhbHNlKVxyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgaWYgKHNsaWNrLiRzbGlkZXMubGVuZ3RoID09PSBpbmRleCArIHNsaWNrLm9wdGlvbnMuc2xpZGVzVG9TY3JvbGwpIHtcclxuICAgICAgICAgICAgalF1ZXJ5KCcuYnV0dG9ucyAuYnRuLW5leHQnKS5oaWRlKClcclxuICAgICAgICAgICAgalF1ZXJ5KCcuYnV0dG9ucyAuYnRuLXN1Ym1pdCcpLnJlbW92ZUNsYXNzKCdkLW5vbmUnKS5zaG93KClcclxuICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICBqUXVlcnkoJy5idXR0b25zIC5idG4tbmV4dCcpLnNob3coKVxyXG4gICAgICAgICAgICBqUXVlcnkoJy5idXR0b25zIC5idG4tc3VibWl0JykuaGlkZSgpXHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBqUXVlcnkoJy5idG4tbmF2Ym94W2RhdGEtaW5kZXhdJykucmVtb3ZlQ2xhc3MoJ2FjdGl2ZScpXHJcbiAgICAgICAgalF1ZXJ5KCcuYnRuLW5hdmJveFtkYXRhLWluZGV4PVwiJyArIGluZGV4ICsgJ1wiXScpLmFkZENsYXNzKCdhY3RpdmUnKVxyXG5cclxuICAgICAgICBqUXVlcnkoJy5zbGlkZS1wYXNzYWdlW2RhdGEtaW5kZXg9XCInICsgaW5kZXggKyAnXCJdJykubUN1c3RvbVNjcm9sbGJhcih7XHJcbiAgICAgICAgICAgIGF4aXM6ICd5JyxcclxuICAgICAgICAgICAgdGhlbWU6ICdtaW5pbWFsLWRhcmsnLFxyXG4gICAgICAgIH0pXHJcblxyXG4gICAgICAgIGpRdWVyeSgnLnNsaWRlLXF1ZXN0aW9uW2RhdGEtaW5kZXg9XCInICsgaW5kZXggKyAnXCJdJykubUN1c3RvbVNjcm9sbGJhcih7XHJcbiAgICAgICAgICAgIGF4aXM6ICd5JyxcclxuICAgICAgICAgICAgdGhlbWU6ICdtaW5pbWFsLWRhcmsnLFxyXG4gICAgICAgIH0pXHJcbiAgICB9KVxyXG4gICAgLm9uKCdhZnRlckNoYW5nZScsIGZ1bmN0aW9uIChldmVudCwgc2xpY2ssIGluZGV4KSB7fSlcclxuXHJcbmpRdWVyeSgnLmJ1dHRvbnMgLmJ0bi1wcmV2Jykub24oJ2NsaWNrJywgZnVuY3Rpb24gKCkge1xyXG4gICAgalF1ZXJ5KCcuc2xpZGVzJykuc2xpY2soJ3NsaWNrUHJldicpXHJcbn0pXHJcblxyXG5qUXVlcnkoJy5idXR0b25zIC5idG4tbmV4dCcpLm9uKCdjbGljaycsIGZ1bmN0aW9uICgpIHtcclxuICAgIGpRdWVyeSgnLnNsaWRlcycpLnNsaWNrKCdzbGlja05leHQnKVxyXG59KVxyXG5cclxualF1ZXJ5KCcuYnRuLW5hdmJveCcpLm9uKCdjbGljaycsIGZ1bmN0aW9uICgpIHtcclxuICAgIGxldCBpID0galF1ZXJ5KHRoaXMpLmRhdGEoJ2luZGV4JylcclxuICAgIGpRdWVyeSgnLnNsaWRlcycpLnNsaWNrKCdzbGlja0dvVG8nLCBpKVxyXG59KVxyXG5cclxualF1ZXJ5KCcjbmF2Ym94LWFyZWEnKS5tQ3VzdG9tU2Nyb2xsYmFyKHtcclxuICAgIGF4aXM6ICd5JyxcclxuICAgIHRoZW1lOiAnbWluaW1hbC1kYXJrJyxcclxufSlcclxuIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./resources/js/components/quizzes/takerquizzes/view.js\n");

/***/ }),

/***/ 26:
/*!********************************************************************!*\
  !*** multi ./resources/js/components/quizzes/takerquizzes/view.js ***!
  \********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! F:\Project\laravel\quizemaster\code\resources\js\components\quizzes\takerquizzes\view.js */"./resources/js/components/quizzes/takerquizzes/view.js");


/***/ })

/******/ });