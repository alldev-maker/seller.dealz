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
/******/ 	return __webpack_require__(__webpack_require__.s = 37);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/components/mediamanager/index.js":
/*!*******************************************************!*\
  !*** ./resources/js/components/mediamanager/index.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance"); }

function _iterableToArray(iter) { if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } }

new Vue({
  el: '#component',
  data: {
    name: {
      singular: 'Media Manager',
      plural: 'Media Manager'
    },
    url: {
      path: {
        upload: '/mediamanager/upload',
        collection: '/mediamanager/files/list/' + window.quizmaster.mm.type,
        resource: '/mediamanager/files'
      },
      qs: ''
    },
    submitting: false,
    loading: false,
    query: {
      keywords: '',
      page: 1,
      limit: parseInt(window.quizmaster.mm.settings['mm.ipp'])
    },
    results: {
      rows: [],
      original: [],
      total: {
        records: 0,
        pages: 0
      }
    },
    checkbox: {
      ids: [],
      all: false
    },
    debug: true,
    prog: 0,
    fileUpload: {
      isUploading: false,
      files: [],
      count: {
        all: 0,
        success: 0,
        failed: 0
      }
    },
    file: {
      id: '',
      name: ''
    }
  },
  methods: {
    clear: function clear() {
      this.query = {
        keywords: '',
        page: 1,
        limit: parseInt(window.quizmaster.mm.settings['mm.ipp'])
      };
      this.search();
    },
    search: function search() {
      var that = this;
      this.url.qs = 'q=' + this.query.keywords + '&p=' + this.query.page + '&l=' + this.query.limit + '';
      that.loading = true;
      axios.get(this.url.path.collection + '?' + this.url.qs).then(function (response) {
        that.results.rows = response.data.records;
        that.results.total.records = response.data.pagination.records.total;
        that.results.total.pages = response.data.pagination.pages.total;
        that.loading = false;
      });
    },
    turn: function turn() {
      var that = this;
      this.url.qs = 'q=' + this.query.keywords + '&p=' + this.query.page + '&l=' + this.query.limit + '';
      that.loading = true;
      axios.get(this.url.path.collection + '?' + this.url.qs).then(function (response) {
        var _that$results$rows;

        (_that$results$rows = that.results.rows).push.apply(_that$results$rows, _toConsumableArray(response.data.records));

        that.results.total.records = response.data.pagination.records.total;
        that.results.total.pages = response.data.pagination.pages.total;
        that.loading = false;
      });
    },
    selectFiles: function selectFiles() {
      document.getElementById("stdFileUpload").click();
    },
    handleUpload: function handleUpload() {
      var that = this;

      if (!that.fileUpload.isUploading) {
        var files = that.$refs.stdFileUpload.files;
        that.upload(files);
      }
    },
    upload: function upload(files) {
      var that = this;

      if (files.length === 0) {
        return false;
      }

      that.fileUpload.uploading = true;
      that.fileUpload.files = [];
      that.fileUpload.count.all = 0;
      that.fileUpload.count.success = 0;
      that.fileUpload.count.failed = 0;
      this.$bvModal.show('progresses');

      var _loop = function _loop(i) {
        that.fileUpload.files[i] = files[i];
        that.fileUpload.files[i].progress = 0;
        that.fileUpload.files[i].done = null;
        that.fileUpload.files[i].error = '';

        if (!window.quizmaster.mm.settings['mm.file.types'].includes(that.fileUpload.files[i].type)) {
          that.fileUpload.files[i].done = false;
          that.fileUpload.files[i].error = 'Invalid file.';
          that.fileUpload.count.failed++;
          that.fileUpload.count.all++;

          if (that.fileUpload.count.all === files.length) {
            that.handleDone();
          }

          return "continue";
        }

        if (window.quizmaster.mm.settings['mm.file.size.max'] < that.fileUpload.files[i].size) {
          that.fileUpload.files[i].done = false;
          that.fileUpload.files[i].error = 'File size exceeded.';
          that.fileUpload.count.failed++;
          that.fileUpload.count.all++;

          if (that.fileUpload.count.all === files.length) {
            that.handleDone();
          }

          return "continue";
        }

        var data = new FormData();
        data.append('file', files[i]);
        axios.post(that.url.path.upload, data, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
          onUploadProgress: function onUploadProgress(event) {
            that.fileUpload.files[i].progress = Math.round(event.loaded * 100 / event.total);
            that.prog++;

            if (that.debug) {
              console.log('File ' + that.fileUpload.files[i].name + ' progress: ' + that.fileUpload.files[i].progress + '%');
            }
          }
        }).then(function () {
          that.fileUpload.files[i].done = true;
          that.fileUpload.count.success++;
          that.fileUpload.count.all++;

          if (that.fileUpload.count.all === files.length) {
            that.handleDone();
          }
        })["catch"](function () {
          that.fileUpload.files[i].done = false;
          that.fileUpload.files[i].error = 'Unknown reason.';
          that.fileUpload.count.failed++;
          that.fileUpload.count.all++;

          if (that.fileUpload.count.all === files.length) {
            that.handleDone();
          }
        });
      };

      for (var i = 0; i < files.length; i++) {
        var _ret = _loop(i);

        if (_ret === "continue") continue;
      }

      return true;
    },
    handleDone: function handleDone() {
      var that = this;
      that.fileUpload.uploading = false;
      var title = '';
      var variant = '';
      var content = '';

      if (that.fileUpload.count.success === that.fileUpload.count.all) {
        title = 'Success';
        variant = 'success';
        content = (that.fileUpload.count.all === 1 ? 'File' : 'All files') + ' has been uploaded.';
      } else {
        title = 'Warning';
        variant = 'warning';
        content = 'Not all files has been uploaded.';

        if (that.fileUpload.count.failed === that.fileUpload.count.all) {
          title = 'Error';
          variant = 'danger';
          content = (that.fileUpload.count.all === 1 ? 'File ' : 'All files') + ' failed to upload.';
        }
      }

      setTimeout(function () {
        that.results = {
          rows: [],
          original: [],
          total: {
            records: 0,
            pages: 0
          }
        };
        that.clear();
        that.$bvModal.hide('progresses');
        that.$bvToast.toast(content, {
          title: title,
          variant: variant,
          solid: true
        });
      }, 10);
    },
    openViewFileModal: function openViewFileModal(file) {
      this.file = file;
      this.$bvModal.show('view-modal');
    },
    pickFile: function pickFile(file) {
      var windowParent = window.parent;
      windowParent.postMessage({
        mceAction: 'noop',
        id: file.id,
        name: file.name,
        url: file.urls.source
      }, '*');
    },
    openConfirmDeleteFileModal: function openConfirmDeleteFileModal(file) {
      this.file = file;
      this.$bvModal.show('file-delete-modal');
    },
    deleteFile: function deleteFile(file) {
      var that = this;
      that.submitting = true;
      axios({
        method: 'DELETE',
        url: that.url.path.resource + '/' + that.file.id
      }).then(function () {
        that.file = {
          id: '',
          name: ''
        };
        that.clear();
        that.submitting = false;
        var content = 'File [' + file.name + '] has been deleted.';
        that.$bvToast.toast(content, {
          title: 'Success',
          variant: 'success',
          solid: true
        });
      })["catch"](function () {
        that.submitting = false;
        var content = 'Failed to delete the file [' + file.name + '].';
        that.$bvToast.toast(content, {
          title: 'Error',
          variant: 'danger',
          solid: true
        });
      });
    }
  },
  mounted: function mounted() {
    var that = this;
    that.clear();
    jQuery(window).bind('scroll', function () {
      if (jQuery(document).height() <= jQuery(window).scrollTop() + jQuery(window).height()) {
        ++that.query.page;
        that.turn();
      }
    });
    jQuery(document).bind('dragover', function () {
      if (!that.fileUpload.isUploading) {
        jQuery('#dropzone').removeClass('d-none').addClass('d-flex');
      }
    });
    jQuery(document).bind('dragleave drop', function () {
      if (!that.fileUpload.isUploading) {
        jQuery('#dropzone').removeClass('d-flex').addClass('d-none');
      }
    });
    jQuery(document).bind('drop', function (e) {
      e.preventDefault();

      if (!that.fileUpload.isUploading) {
        var files = e.originalEvent.dataTransfer.files;
        that.upload(files);
      }
    });
    jQuery(document).bind('dragover dragleave drop', function (e) {
      e.preventDefault();
    });
  }
});

/***/ }),

/***/ 37:
/*!*************************************************************!*\
  !*** multi ./resources/js/components/mediamanager/index.js ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /home/james/Projects/Web/GTC015 Quizmaster/resources/js/components/mediamanager/index.js */"./resources/js/components/mediamanager/index.js");


/***/ })

/******/ });