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
/******/ 	return __webpack_require__(__webpack_require__.s = 25);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/components/quizzes/takerquizzes/form.js":
/*!**************************************************************!*\
  !*** ./resources/js/components/quizzes/takerquizzes/form.js ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("new Vue({\n  el: '#component',\n  data: {\n    name: {\n      singular: 'TakerQuiz',\n      plural: 'TakerQuizzes'\n    },\n    loading: false,\n    submitting: false,\n    url: {\n      path: '/quizzes/takerquizzes'\n    },\n    quiz: {\n      id: '',\n      name: '',\n      description: ''\n    },\n    config: {\n      tinymce: {\n        height: 350,\n        menubar: false,\n        branding: false,\n        plugins: ['lists link image charmap anchor', 'media table paste help wordcount', 'code'],\n        toolbar: 'bold italic |  bullist numlist outdent indent | removeformat | help | code'\n      },\n      large: {\n        height: 350,\n        menubar: false,\n        branding: false,\n        plugins: ['lists link image charmap anchor', 'media table paste help wordcount', 'code'],\n        toolbar: 'bold italic | bullist numlist outdent indent | alignleft aligncenter alignright alignjustify | media link image charmap | removeformat | help | code',\n        file_picker_callback: function file_picker_callback(callback, value, meta) {\n          var url = '/mediamanager/modal';\n\n          if (meta.filetype === 'image') {\n            url += '/image';\n          }\n\n          if (meta.filetype === 'media') {\n            url += '/video';\n          }\n\n          tinymce.activeEditor.windowManager.openUrl({\n            title: 'Media Manager',\n            url: url,\n            onMessage: function onMessage(api, data) {\n              callback(data.url, {\n                alt: 'Image',\n                'data-media-id': data.id\n              });\n              api.close();\n            }\n          });\n        },\n        relative_urls: false,\n        remove_script_host: false,\n        convert_urls: true,\n        content_css: '/css/frontend-tinymce.css'\n      }\n    },\n    users: {\n      url: '/admin/users/list',\n      items: []\n    },\n    scoring_types: {\n      url: '/quizzes/types/scorings/list',\n      items: []\n    }\n  },\n  methods: {\n    submit: function submit() {\n      var that = this;\n      that.submitting = true;\n      this.$validator.validateAll('quiz').then(function (result) {\n        if (result) {\n          that.quiz.action = 'update-about';\n          axios({\n            method: that.quiz.id !== '' ? 'PUT' : 'POST',\n            url: that.url.path + (that.quiz.id !== '' ? '/' + that.quiz.id : ''),\n            data: that.quiz\n          }).then(function () {\n            window.location = '/quizzes/takerquizzes';\n          })[\"catch\"](function () {\n            var content = 'Failed to submit the form.';\n            that.$bvToast.toast(content, {\n              title: 'Error',\n              variant: 'danger',\n              solid: true\n            });\n            that.submitting = false;\n          });\n        } else {\n          that.submitting = false;\n        }\n      })[\"catch\"](function () {\n        var content = 'Validation failed. Please check the form.';\n        that.$bvToast.toast(content, {\n          title: 'Error',\n          variant: 'danger',\n          solid: true\n        });\n        that.submitting = false;\n      });\n    }\n  },\n  beforeMount: function beforeMount() {\n    var that = this;\n    that.quiz = window.quizmaster.quiz;\n    var role = window.quizmaster.user.role;\n    var is_admin = role.slug === 'admin' || role.slug === 'developer';\n\n    if (is_admin) {\n      axios({\n        method: 'GET',\n        url: that.users.url + '?l=0'\n      }).then(function (data) {\n        that.users.items = data.data;\n      });\n      axios({\n        method: 'GET',\n        url: that.scoring_types.url + '?l=0'\n      }).then(function (data) {\n        that.scoring_types.items = data.data;\n      });\n    }\n  }\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvanMvY29tcG9uZW50cy9xdWl6emVzL3Rha2VycXVpenplcy9mb3JtLmpzP2FkNzIiXSwibmFtZXMiOlsiVnVlIiwiZWwiLCJkYXRhIiwibmFtZSIsInNpbmd1bGFyIiwicGx1cmFsIiwibG9hZGluZyIsInN1Ym1pdHRpbmciLCJ1cmwiLCJwYXRoIiwicXVpeiIsImlkIiwiZGVzY3JpcHRpb24iLCJjb25maWciLCJ0aW55bWNlIiwiaGVpZ2h0IiwibWVudWJhciIsImJyYW5kaW5nIiwicGx1Z2lucyIsInRvb2xiYXIiLCJsYXJnZSIsImZpbGVfcGlja2VyX2NhbGxiYWNrIiwiY2FsbGJhY2siLCJ2YWx1ZSIsIm1ldGEiLCJmaWxldHlwZSIsImFjdGl2ZUVkaXRvciIsIndpbmRvd01hbmFnZXIiLCJvcGVuVXJsIiwidGl0bGUiLCJvbk1lc3NhZ2UiLCJhcGkiLCJhbHQiLCJjbG9zZSIsInJlbGF0aXZlX3VybHMiLCJyZW1vdmVfc2NyaXB0X2hvc3QiLCJjb252ZXJ0X3VybHMiLCJjb250ZW50X2NzcyIsInVzZXJzIiwiaXRlbXMiLCJzY29yaW5nX3R5cGVzIiwibWV0aG9kcyIsInN1Ym1pdCIsInRoYXQiLCIkdmFsaWRhdG9yIiwidmFsaWRhdGVBbGwiLCJ0aGVuIiwicmVzdWx0IiwiYWN0aW9uIiwiYXhpb3MiLCJtZXRob2QiLCJ3aW5kb3ciLCJsb2NhdGlvbiIsImNvbnRlbnQiLCIkYnZUb2FzdCIsInRvYXN0IiwidmFyaWFudCIsInNvbGlkIiwiYmVmb3JlTW91bnQiLCJxdWl6bWFzdGVyIiwicm9sZSIsInVzZXIiLCJpc19hZG1pbiIsInNsdWciXSwibWFwcGluZ3MiOiJBQUFBLElBQUlBLEdBQUosQ0FBUTtBQUNKQyxJQUFFLEVBQUUsWUFEQTtBQUVKQyxNQUFJLEVBQUU7QUFDRkMsUUFBSSxFQUFFO0FBQ0ZDLGNBQVEsRUFBRSxXQURSO0FBRUZDLFlBQU0sRUFBRTtBQUZOLEtBREo7QUFLRkMsV0FBTyxFQUFF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V0FBSyxDQUFDO0FBQ0ZDLGNBQU0sRUFBRSxLQUROO0FBRUYxQyxXQUFHLEVBQUVtQyxJQUFJLENBQUNMLEtBQUwsQ0FBVzlCLEdBQVgsR0FBaUI7QUFGcEIsT0FBRCxDQUFMLENBR0dzQyxJQUhILENBR1EsVUFBVTVDLElBQVYsRUFBZ0I7QUFDcEJ5QyxZQUFJLENBQUNMLEtBQUwsQ0FBV0MsS0FBWCxHQUFtQnJDLElBQUksQ0FBQ0EsSUFBeEI7QUFDSCxPQUxEO0FBTUErQyxXQUFLLENBQUM7QUFDRkMsY0FBTSxFQUFFLEtBRE47QUFFRjFDLFdBQUcsRUFBRW1DLElBQUksQ0FBQ0gsYUFBTCxDQUFtQmhDLEdBQW5CLEdBQXlCO0FBRjVCLE9BQUQsQ0FBTCxDQUdHc0MsSUFISCxDQUdRLFVBQVU1QyxJQUFWLEVBQWdCO0FBQ3BCeUMsWUFBSSxDQUFDSCxhQUFMLENBQW1CRCxLQUFuQixHQUEyQnJDLElBQUksQ0FBQ0EsSUFBaEM7QUFDSCxPQUxEO0FBTUg7QUFDSjtBQWxKRyxDQUFSIiwiZmlsZSI6Ii4vcmVzb3VyY2VzL2pzL2NvbXBvbmVudHMvcXVpenplcy90YWtlcnF1aXp6ZXMvZm9ybS5qcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIm5ldyBWdWUoe1xyXG4gICAgZWw6ICcjY29tcG9uZW50JyxcclxuICAgIGRhdGE6IHtcclxuICAgICAgICBuYW1lOiB7XHJcbiAgICAgICAgICAgIHNpbmd1bGFyOiAnVGFrZXJRdWl6JyxcclxuICAgICAgICAgICAgcGx1cmFsOiAnVGFrZXJRdWl6emVzJyxcclxuICAgICAgICB9LFxyXG4gICAgICAgIGxvYWRpbmc6IGZhbHNlLFxyXG4gICAgICAgIHN1Ym1pdHRpbmc6IGZhbHNlLFxyXG4gICAgICAgIHVybDoge1xyXG4gICAgICAgICAgICBwYXRoOiAnL3F1aXp6ZXMvdGFrZXJxdWl6emVzJyxcclxuICAgICAgICB9LFxyXG4gICAgICAgIHF1aXo6IHtcclxuICAgICAgICAgICAgaWQ6ICcnLFxyXG4gICAgICAgICAgICBuYW1lOiAnJyxcclxuICAgICAgICAgICAgZGVzY3JpcHRpb246ICcnLFxyXG4gICAgICAgIH0sXHJcbiAgICAgICAgY29uZmlnOiB7XHJcbiAgICAgICAgICAgIHRpbnltY2U6IHtcclxuICAgICAgICAgICAgICAgIGhlaWdodDogMzUwLFxyXG4gICAgICAgICAgICAgICAgbWVudWJhcjogZmFsc2UsXHJcbiAgICAgICAgICAgICAgICBicmFuZGluZzogZmFsc2UsXHJcbiAgICAgICAgICAgICAgICBwbHVnaW5zOiBbXHJcbiAgICAgICAgICAgICAgICAgICAgJ2xpc3RzIGxpbmsgaW1hZ2UgY2hhcm1hcCBhbmNob3InLFxyXG4gICAgICAgICAgICAgICAgICAgICdtZWRpYSB0YWJsZSBwYXN0ZSBoZWxwIHdvcmRjb3VudCcsXHJcbiAgICAgICAgICAgICAgICAgICAgJ2NvZGUnLFxyXG4gICAgICAgICAgICAgICAgXSxcclxuICAgICAgICAgICAgICAgIHRvb2xiYXI6XHJcbiAgICAgICAgICAgICAgICAgICAgJ2JvbGQgaXRhbGljIHwgIGJ1bGxpc3QgbnVtbGlzdCBvdXRkZW50IGluZGVudCB8IHJlbW92ZWZvcm1hdCB8IGhlbHAgfCBjb2RlJyxcclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgbGFyZ2U6IHtcclxuICAgICAgICAgICAgICAgIGhlaWdodDogMzUwLFxyXG4gICAgICAgICAgICAgICAgbWVudWJhcjogZmFsc2UsXHJcbiAgICAgICAgICAgICAgICBicmFuZGluZzogZmFsc2UsXHJcbiAgICAgICAgICAgICAgICBwbHVnaW5zOiBbXHJcbiAgICAgICAgICAgICAgICAgICAgJ2xpc3RzIGxpbmsgaW1hZ2UgY2hhcm1hcCBhbmNob3InLFxyXG4gICAgICAgICAgICAgICAgICAgICdtZWRpYSB0YWJsZSBwYXN0ZSBoZWxwIHdvcmRjb3VudCcsXHJcbiAgICAgICAgICAgICAgICAgICAgJ2NvZGUnLFxyXG4gICAgICAgICAgICAgICAgXSxcclxuICAgICAgICAgICAgICAgIHRvb2xiYXI6XHJcbiAgICAgICAgICAgICAgICAgICAgJ2JvbGQgaXRhbGljIHwgYnVsbGlzdCBudW1saXN0IG91dGRlbnQgaW5kZW50IHwgYWxpZ25sZWZ0IGFsaWduY2VudGVyIGFsaWducmlnaHQgYWxpZ25qdXN0aWZ5IHwgbWVkaWEgbGluayBpbWFnZSBjaGFybWFwIHwgcmVtb3ZlZm9ybWF0IHwgaGVscCB8IGNvZGUnLFxyXG4gICAgICAgICAgICAgICAgZmlsZV9waWNrZXJfY2FsbGJhY2s6IGZ1bmN0aW9uIChjYWxsYmFjaywgdmFsdWUsIG1ldGEpIHtcclxuICAgICAgICAgICAgICAgICAgICBsZXQgdXJsID0gJy9tZWRpYW1hbmFnZXIvbW9kYWwnXHJcblxyXG4gICAgICAgICAgICAgICAgICAgIGlmIChtZXRhLmZpbGV0eXBlID09PSAnaW1hZ2UnKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHVybCArPSAnL2ltYWdlJ1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgICAgICAgICAgaWYgKG1ldGEuZmlsZXR5cGUgPT09ICdtZWRpYScpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgdXJsICs9ICcvdmlkZW8nXHJcbiAgICAgICAgICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgICAgICAgICB0aW55bWNlLmFjdGl2ZUVkaXRvci53aW5kb3dNYW5hZ2VyLm9wZW5Vcmwoe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB0aXRsZTogJ01lZGlhIE1hbmFnZXInLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICB1cmw6IHVybCxcclxuICAgICAgICAgICAgICAgICAgICAgICAgb25NZXNzYWdlOiBmdW5jdGlvbiAoYXBpLCBkYXRhKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjYWxsYmFjayhkYXRhLnVybCwge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGFsdDogJ0ltYWdlJyxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnZGF0YS1tZWRpYS1pZCc6IGRhdGEuaWQsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgYXBpLmNsb3NlKClcclxuICAgICAgICAgICAgICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgICAgICAgICB9KVxyXG4gICAgICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgICAgIHJlbGF0aXZlX3VybHM6IGZhbHNlLFxyXG4gICAgICAgICAgICAgICAgcmVtb3ZlX3NjcmlwdF9ob3N0OiBmYWxzZSxcclxuICAgICAgICAgICAgICAgIGNvbnZlcnRfdXJsczogdHJ1ZSxcclxuICAgICAgICAgICAgICAgIGNvbnRlbnRfY3NzOiAnL2Nzcy9mcm9udGVuZC10aW55bWNlLmNzcycsXHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgfSxcclxuICAgICAgICB1c2Vyczoge1xyXG4gICAgICAgICAgICB1cmw6ICcvYWRtaW4vdXNlcnMvbGlzdCcsXHJcbiAgICAgICAgICAgIGl0ZW1zOiBbXSxcclxuICAgICAgICB9LFxyXG4gICAgICAgIHNjb3JpbmdfdHlwZXM6IHtcclxuICAgICAgICAgICAgdXJsOiAnL3F1aXp6ZXMvdHlwZXMvc2NvcmluZ3MvbGlzdCcsXHJcbiAgICAgICAgICAgIGl0ZW1zOiBbXSxcclxuICAgICAgICB9LFxyXG4gICAgfSxcclxuICAgIG1ldGhvZHM6IHtcclxuICAgICAgICBzdWJtaXQ6IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgbGV0IHRoYXQgPSB0aGlzXHJcbiAgICAgICAgICAgIHRoYXQuc3VibWl0dGluZyA9IHRydWVcclxuXHJcbiAgICAgICAgICAgIHRoaXMuJHZhbGlkYXRvclxyXG4gICAgICAgICAgICAgICAgLnZhbGlkYXRlQWxsKCdxdWl6JylcclxuICAgICAgICAgICAgICAgIC50aGVuKGZ1bmN0aW9uIChyZXN1bHQpIHtcclxuICAgICAgICAgICAgICAgICAgICBpZiAocmVzdWx0KSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoYXQucXVpei5hY3Rpb24gPSAndXBkYXRlLWFib3V0J1xyXG5cclxuICAgICAgICAgICAgICAgICAgICAgICAgYXhpb3Moe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbWV0aG9kOiB0aGF0LnF1aXouaWQgIT09ICcnID8gJ1BVVCcgOiAnUE9TVCcsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB1cmw6XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhhdC51cmwucGF0aCArXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgKHRoYXQucXVpei5pZCAhPT0gJycgPyAnLycgKyB0aGF0LnF1aXouaWQgOiAnJyksXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBkYXRhOiB0aGF0LnF1aXosXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH0pXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAudGhlbihmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgd2luZG93LmxvY2F0aW9uID0gJy9xdWl6emVzL3Rha2VycXVpenplcydcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuY2F0Y2goZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxldCBjb250ZW50ID0gJ0ZhaWxlZCB0byBzdWJtaXQgdGhlIGZvcm0uJ1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRoYXQuJGJ2VG9hc3QudG9hc3QoY29udGVudCwge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aXRsZTogJ0Vycm9yJyxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyaWFudDogJ2RhbmdlcicsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNvbGlkOiB0cnVlLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhhdC5zdWJtaXR0aW5nID0gZmFsc2VcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pXHJcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgdGhhdC5zdWJtaXR0aW5nID0gZmFsc2VcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICB9KVxyXG4gICAgICAgICAgICAgICAgLmNhdGNoKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgICAgICBsZXQgY29udGVudCA9ICdWYWxpZGF0aW9uIGZhaWxlZC4gUGxlYXNlIGNoZWNrIHRoZSBmb3JtLidcclxuICAgICAgICAgICAgICAgICAgICB0aGF0LiRidlRvYXN0LnRvYXN0KGNvbnRlbnQsIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgdGl0bGU6ICdFcnJvcicsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhcmlhbnQ6ICdkYW5nZXInLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICBzb2xpZDogdHJ1ZSxcclxuICAgICAgICAgICAgICAgICAgICB9KVxyXG4gICAgICAgICAgICAgICAgICAgIHRoYXQuc3VibWl0dGluZyA9IGZhbHNlXHJcbiAgICAgICAgICAgICAgICB9KVxyXG4gICAgICAgIH0sXHJcbiAgICB9LFxyXG4gICAgYmVmb3JlTW91bnQ6IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICBsZXQgdGhhdCA9IHRoaXNcclxuXHJcbiAgICAgICAgdGhhdC5xdWl6ID0gd2luZG93LnF1aXptYXN0ZXIucXVpelxyXG5cclxuICAgICAgICBsZXQgcm9sZSA9IHdpbmRvdy5xdWl6bWFzdGVyLnVzZXIucm9sZVxyXG4gICAgICAgIGxldCBpc19hZG1pbiA9IHJvbGUuc2x1ZyA9PT0gJ2FkbWluJyB8fCByb2xlLnNsdWcgPT09ICdkZXZlbG9wZXInXHJcblxyXG4gICAgICAgIGlmIChpc19hZG1pbikge1xyXG4gICAgICAgICAgICBheGlvcyh7XHJcbiAgICAgICAgICAgICAgICBtZXRob2Q6ICdHRVQnLFxyXG4gICAgICAgICAgICAgICAgdXJsOiB0aGF0LnVzZXJzLnVybCArICc/bD0wJyxcclxuICAgICAgICAgICAgfSkudGhlbihmdW5jdGlvbiAoZGF0YSkge1xyXG4gICAgICAgICAgICAgICAgdGhhdC51c2Vycy5pdGVtcyA9IGRhdGEuZGF0YVxyXG4gICAgICAgICAgICB9KVxyXG4gICAgICAgICAgICBheGlvcyh7XHJcbiAgICAgICAgICAgICAgICBtZXRob2Q6ICdHRVQnLFxyXG4gICAgICAgICAgICAgICAgdXJsOiB0aGF0LnNjb3JpbmdfdHlwZXMudXJsICsgJz9sPTAnLFxyXG4gICAgICAgICAgICB9KS50aGVuKGZ1bmN0aW9uIChkYXRhKSB7XHJcbiAgICAgICAgICAgICAgICB0aGF0LnNjb3JpbmdfdHlwZXMuaXRlbXMgPSBkYXRhLmRhdGFcclxuICAgICAgICAgICAgfSlcclxuICAgICAgICB9XHJcbiAgICB9LFxyXG59KVxyXG4iXSwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./resources/js/components/quizzes/takerquizzes/form.js\n");

/***/ }),

/***/ 25:
/*!********************************************************************!*\
  !*** multi ./resources/js/components/quizzes/takerquizzes/form.js ***!
  \********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! F:\Project\laravel\quizemaster\code\resources\js\components\quizzes\takerquizzes\form.js */"./resources/js/components/quizzes/takerquizzes/form.js");


/***/ })

/******/ });