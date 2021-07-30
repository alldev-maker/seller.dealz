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
/******/ 	return __webpack_require__(__webpack_require__.s = 19);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/components/quizzes/quizzes/view.js":
/*!*********************************************************!*\
  !*** ./resources/js/components/quizzes/quizzes/view.js ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

jQuery(".slides").slick({
  swipe: false,
  infinite: false,
  dots: false,
  prevArrow: false,
  nextArrow: false
}).on('beforeChange', function (event, slick, prev, index) {
  if (index === 0) {
    jQuery(".buttons .btn-prev").attr('disabled', true);
  } else {
    jQuery(".buttons .btn-prev").attr('disabled', false);
  }

  if (slick.$slides.length === index + slick.options.slidesToScroll) {
    jQuery(".buttons .btn-next").hide();
    jQuery(".buttons .btn-submit").removeClass('d-none').show();
  } else {
    jQuery(".buttons .btn-next").show();
    jQuery(".buttons .btn-submit").hide();
  }

  jQuery('.btn-navbox[data-index]').removeClass('active');
  jQuery('.btn-navbox[data-index="' + index + '"]').addClass('active');
  jQuery('.slide-passage[data-index="' + index + '"]').mCustomScrollbar({
    axis: 'y',
    theme: 'minimal-dark'
  });
  jQuery('.slide-question[data-index="' + index + '"]').mCustomScrollbar({
    axis: 'y',
    theme: 'minimal-dark'
  });
}).on('afterChange', function (event, slick, index) {});
jQuery(".buttons .btn-prev").on('click', function () {
  jQuery('.slides').slick('slickPrev');
});
jQuery(".buttons .btn-next").on('click', function () {
  jQuery('.slides').slick('slickNext');
});
jQuery(".btn-navbox").on('click', function () {
  var i = jQuery(this).data('index');
  jQuery('.slides').slick('slickGoTo', i);
});
jQuery('#navbox-area').mCustomScrollbar({
  axis: 'y',
  theme: 'minimal-dark'
});

/***/ }),

/***/ 19:
/*!***************************************************************!*\
  !*** multi ./resources/js/components/quizzes/quizzes/view.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /home/james/Projects/Web/GTC015 Quizmaster/resources/js/components/quizzes/quizzes/view.js */"./resources/js/components/quizzes/quizzes/view.js");


/***/ })

/******/ });