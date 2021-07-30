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
/******/ 	return __webpack_require__(__webpack_require__.s = 20);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/components/quizzes/quizzes/questionnaire.js":
/*!******************************************************************!*\
  !*** ./resources/js/components/quizzes/quizzes/questionnaire.js ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

new Vue({
  el: '#component',
  data: {
    name: {
      singular: 'Quiz',
      plural: 'Quizzes'
    },
    loading: {
      sections: false,
      passages: false,
      questions: false
    },
    keys: {
      section: {
        description: _.uniqueId('_')
      },
      passage: {
        description: _.uniqueId('_'),
        content: _.uniqueId('_')
      },
      question: {
        description: _.uniqueId('_')
      },
      choice: {
        choice: _.uniqueId('_')
      }
    },
    submitting: false,
    url: {
      path: '/quizzes/quizzes'
    },
    config: {
      description: {
        height: 350,
        menubar: false,
        branding: false,
        plugins: ['lists link image charmap anchor', 'table paste help wordcount'],
        toolbar: 'bold italic under strikethrough | superscript subscript | bullist numlist outdent indent | link image charmap | removeformat | help',
        convert_urls: false,
        file_picker_callback: function file_picker_callback(callback, value, meta) {
          tinymce.activeEditor.windowManager.openUrl({
            title: 'Media Manager',
            url: '/mediamanager/modal',
            onMessage: function onMessage(api, data) {
              callback(data.url, {
                'alt': 'Image',
                'data-media-id': data.id
              });
              api.close();
            }
          });
        }
      },
      content: {
        height: 400,
        menubar: false,
        branding: false,
        plugins: ['lists link image charmap anchor', 'table paste help wordcount'],
        toolbar: 'bold italic under strikethrough | superscript subscript | bullist numlist outdent indent | link image charmap | table | removeformat | help',
        file_picker_callback: function file_picker_callback(callback, value, meta) {
          tinymce.activeEditor.windowManager.openUrl({
            title: 'Media Manager',
            url: '/mediamanager/modal',
            onMessage: function onMessage(api, data) {
              callback(data.url, {
                'alt': 'Image',
                'data-media-id': data.id
              });
              api.close();
            }
          });
        },
        relative_urls: false,
        remove_script_host: false,
        convert_urls: true,
        content_css: '/css/frontend-tinymce.css'
      },
      question: {
        height: 350,
        menubar: false,
        branding: false,
        plugins: ['lists link image charmap anchor', 'media table paste help wordcount'],
        toolbar: 'bold italic underline strikethrough | superscript subscript | bullist numlist outdent indent | media link image charmap | removeformat | help',
        file_picker_callback: function file_picker_callback(callback, value, meta) {
          var url = '/mediamanager/modal';

          if (meta.filetype === 'image') {
            url += '/image';
          }

          if (meta.filetype === 'media') {
            url += '/video';
          }

          tinymce.activeEditor.windowManager.openUrl({
            title: 'Media Manager',
            url: url,
            onMessage: function onMessage(api, data) {
              callback(data.url, {
                'alt': 'Image',
                'data-media-id': data.id
              });
              api.close();
            }
          });
        },
        relative_urls: false,
        remove_script_host: false,
        convert_urls: true,
        content_css: '/css/frontend-tinymce.css'
      },
      choice: {
        height: 250,
        menubar: false,
        branding: false,
        plugins: ['lists link image charmap anchor', 'table paste help wordcount'],
        toolbar: 'bold italic underline strikethrough | superscript subscript | bullist numlist outdent indent | link image charmap | removeformat | help',
        file_picker_callback: function file_picker_callback(callback, value, meta) {
          tinymce.activeEditor.windowManager.openUrl({
            title: 'Media Manager',
            url: '/mediamanager/modal',
            onMessage: function onMessage(api, data) {
              callback(data.url, {
                'alt': 'Image',
                'data-media-id': data.id
              });
              api.close();
            }
          });
        },
        relative_urls: false,
        remove_script_host: false,
        convert_urls: true,
        content_css: '/css/frontend-tinymce.css'
      }
    },
    selections: {
      question_types: [{
        id: 1,
        name: 'Multiple choice, single answer'
      }, {
        id: 2,
        name: 'Multiple choice, multiple answers'
      }, {
        id: 3,
        name: 'Text, short'
      }, {
        id: 4,
        name: 'Text, long'
      }, {
        id: 5,
        name: 'Order of answers'
      }]
    },
    current: {
      section: {
        id: '',
        name: '',
        description: '',
        passages: []
      },
      passage: {
        id: '',
        name: '',
        description: '',
        content: '',
        questions: []
      },
      question: {
        id: '',
        question: '',
        type: {
          id: 1,
          name: 'Multiple choice, single answer'
        },
        problem_types: [],
        points: 0,
        difficulty: 1,
        choices: [{
          ud: _.uniqueId('_'),
          td: _.uniqueId('td-'),
          id: '',
          choice: '',
          is_correct: 0,
          points: 0
        }]
      },
      mmtarget: ''
    },
    quiz: {
      id: '',
      name: '',
      description: '',
      sections: []
    },
    section: {
      types: {
        url: '/quizzes/types/sections/list',
        items: []
      },
      tags: {
        url: '/quizzes/types/problems/list',
        items: []
      }
    },
    question_layouts: [{
      value: 'nqa',
      text: 'Question and Answer Only'
    }, {
      value: 'pqa',
      text: 'Passage | Question and Answer'
    }]
  },
  methods: {
    openSectionFormModal: function openSectionFormModal(section) {
      if (section === undefined || section === null) {
        this.current.section = {
          id: '',
          name: '',
          description: '',
          passages: []
        };
      } else {
        this.current.section = section;
        this.current.section.passages = [];
      }

      this.$bvModal.show('section-form-modal');
    },
    selectSection: function selectSection(section) {
      this.current.section = section;
      this.current.section.passages = [];
      this.current.passage = {
        id: '',
        name: '',
        description: '',
        content: '',
        questions: []
      };
      this.current.question = {
        id: '',
        question: '',
        type: '',
        points: 0,
        difficulty: 1,
        choices: [{
          ud: _.uniqueId('_'),
          id: '',
          choice: '',
          is_correct: 0,
          points: 0
        }]
      };
      this.refreshPassages();
    },
    refreshSections: function refreshSections() {
      var that = this;
      that.loading.sections = true;
      axios({
        method: 'GET',
        url: that.url.path + '/' + that.quiz.id + '/' + 'sections?l=0'
      }).then(function (response) {
        that.quiz.sections = response.data;
        that.loading.sections = false;
      })["catch"](function (error) {
        var content = 'Failed to load the Sections. Reason: ';
        that.$bvToast.toast(content, {
          title: 'Error',
          variant: 'danger',
          solid: true
        });
        that.loading.sections = false;
      });
    },
    sortSections: function sortSections(event) {
      var that = this;
      that.quiz.sections.splice(event.newIndex, 0, that.quiz.sections.splice(event.oldIndex, 1)[0]);
      that.quiz.sections.forEach(function (item, index) {
        if (item !== undefined) {
          item.ordering = index;
        }
      });
      axios({
        method: 'PUT',
        url: that.url.path + '/' + that.quiz.id + '/' + 'sections',
        data: {
          action: 'sort',
          sections: that.quiz.sections
        }
      }).then(function () {
        var content = 'Sections has been sorted.';
        that.$bvToast.toast(content, {
          title: 'Success',
          variant: 'success',
          solid: true
        });
      })["catch"](function (error) {
        var content = 'Failed to sort the Sections. Reason: ' + error.response.data.reason;
        that.$bvToast.toast(content, {
          title: 'Error',
          variant: 'danger',
          solid: true
        });
      });
    },
    submitSection: function submitSection(event) {
      var that = this;
      that.submitting = true;
      this.$validator.validateAll('section').then(function (result) {
        if (result) {
          axios({
            method: that.current.section.id !== '' ? 'PUT' : 'POST',
            url: that.url.path + '/' + that.quiz.id + '/' + 'sections' + (that.current.section.id !== '' ? '/' + that.current.section.id : ''),
            data: that.current.section
          }).then(function () {
            that.refreshSections();
            jQuery('#section-form-modal').modal('hide');
            var content = 'Section has been ' + (that.current.section.id !== '' ? 'updated' : 'created') + '.';
            that.$bvToast.toast(content, {
              title: 'Success',
              variant: 'success',
              solid: true
            });
            that.submitting = false;
            that.current.section = {
              id: '',
              name: '',
              description: '',
              passages: []
            };
            event.preventDefault();
          })["catch"](function () {
            jQuery('#section-form-modal').modal('hide');
            var content = 'Failed to submit the Section.';
            that.$bvToast.toast(content, {
              title: 'Error',
              variant: 'danger',
              solid: true
            });
            that.submitting = false;
            this.current.section = {
              id: '',
              name: '',
              description: ''
            };
            event.preventDefault();
          });
        } else {
          that.submitting = false;
          event.preventDefault();
        }
      })["catch"](function () {
        var content = 'Validation failed. Please check the form.';
        that.$bvToast.toast(content, {
          title: 'Error',
          variant: 'danger',
          solid: true
        });
        that.submitting = false;
        event.preventDefault();
      });
    },
    openConfirmDeleteSectionModal: function openConfirmDeleteSectionModal(section) {
      this.current.section = section;
      this.$bvModal.show('section-delete-modal');
    },
    deleteSection: function deleteSection(section) {
      var that = this;
      that.submitting = true;
      axios({
        method: 'DELETE',
        url: that.url.path + '/' + that.quiz.id + '/' + 'sections' + '/' + section.id
      }).then(function () {
        that.current.section = {
          id: '',
          name: '',
          description: '',
          passages: []
        };
        that.refreshSections();
        that.submitting = false;
        var content = 'Section has been deleted.';
        that.$bvToast.toast(content, {
          title: 'Success',
          variant: 'success',
          solid: true
        });
      })["catch"](function () {
        that.submitting = false;
        var content = 'Failed to delete the Section.';
        that.$bvToast.toast(content, {
          title: 'Error',
          variant: 'danger',
          solid: true
        });
      });
    },
    openPassageFormModal: function openPassageFormModal(passage) {
      if (passage === undefined || passage === null) {
        this.current.passage = {
          id: '',
          name: '',
          description: '',
          content: '',
          questions: []
        };
      } else {
        this.current.passage = passage;
        this.current.passage.questions = [];
      }

      this.$bvModal.show('passage-form-modal');
    },
    selectPassage: function selectPassage(passage) {
      this.current.passage = passage;
      this.current.passage.questions = [];
      this.current.question = {
        id: '',
        question: '',
        type: '',
        points: 0,
        difficulty: 1,
        choices: [{
          ud: _.uniqueId('_'),
          id: '',
          choice: '',
          is_correct: 0,
          points: 0
        }]
      };
      this.refreshQuestions();
    },
    refreshPassages: function refreshPassages() {
      var that = this;
      that.loading.passages = true;
      axios({
        method: 'GET',
        url: that.url.path + '/' + that.quiz.id + '/' + 'sections' + '/' + that.current.section.id + '/' + 'passages?l=0'
      }).then(function (response) {
        that.current.section.passages = response.data;
        that.loading.passages = false;
      })["catch"](function () {
        var content = 'Failed to load the Passages.';
        that.$bvToast.toast(content, {
          title: 'Error',
          variant: 'danger',
          solid: true
        });
        that.loading.passages = false;
      });
    },
    sortPassages: function sortPassages(event) {
      var that = this;
      that.current.section.passages.splice(event.newIndex, 0, that.current.section.passages.splice(event.oldIndex, 1)[0]);
      that.current.section.passages.forEach(function (item, index) {
        if (item !== undefined) {
          item.ordering = index;
        }
      });
      axios({
        method: 'PUT',
        url: that.url.path + '/' + that.quiz.id + '/' + 'sections' + '/' + that.current.section.id + '/' + 'passages',
        data: {
          action: 'sort',
          passages: that.current.section.passages
        }
      }).then(function (response) {
        var content = 'Passages has been sorted.';
        that.$bvToast.toast(content, {
          title: 'Success',
          variant: 'success',
          solid: true
        });
      })["catch"](function (error) {
        var content = 'Failed to sort the Passages. Reason: ' + error.response.data.reason;
        that.$bvToast.toast(content, {
          title: 'Error',
          variant: 'danger',
          solid: true
        });
      });
    },
    submitPassage: function submitPassage(event) {
      var that = this;
      that.submitting = true;
      this.$validator.validateAll('passage').then(function (result) {
        if (result) {
          axios({
            method: that.current.passage.id !== '' ? 'PUT' : 'POST',
            url: that.url.path + '/' + that.quiz.id + '/sections/' + that.current.section.id + '/' + 'passages' + (that.current.passage.id !== '' ? '/' + that.current.passage.id : ''),
            data: that.current.passage
          }).then(function () {
            that.refreshPassages();
            jQuery('#passage-form-modal').modal('hide');
            var content = 'Passage has been ' + (that.current.passage.id !== '' ? 'updated' : 'created') + '.';
            that.$bvToast.toast(content, {
              title: 'Success',
              variant: 'success',
              solid: true
            });
            that.submitting = false;
            that.current.passage = {
              id: '',
              name: '',
              description: '',
              content: '',
              questions: []
            };
            event.preventDefault();
          })["catch"](function () {
            jQuery('#passage-form-modal').modal('hide');
            var content = 'Failed to submit the Passage.';
            that.$bvToast.toast(content, {
              title: 'Error',
              variant: 'danger',
              solid: true
            });
            that.submitting = false;
            that.current.passage = {
              id: '',
              name: '',
              description: ''
            };
            event.preventDefault();
          });
        } else {
          that.submitting = false;
          event.preventDefault();
        }
      })["catch"](function () {
        var content = 'Validation failed. Please check the form.';
        that.$bvToast.toast(content, {
          title: 'Error',
          variant: 'danger',
          solid: true
        });
        that.submitting = false;
        event.preventDefault();
      });
    },
    openConfirmDeletePassageModal: function openConfirmDeletePassageModal(passage) {
      this.current.passage = passage;
      this.$bvModal.show('passage-delete-modal');
    },
    deletePassage: function deletePassage(passage) {
      var that = this;
      that.submitting = true;
      axios({
        method: 'DELETE',
        url: that.url.path + '/' + that.quiz.id + '/' + 'sections' + '/' + that.current.section.id + '/' + 'passages' + '/' + passage.id
      }).then(function () {
        that.current.passage = {
          id: '',
          name: '',
          description: '',
          content: '',
          questions: []
        };
        that.refreshPassages();
        jQuery('#passage-delete-modal').modal('hide');
        that.submitting = false;
        var content = 'Passage has been deleted.';
        that.$bvToast.toast(content, {
          title: 'Success',
          variant: 'success',
          solid: true
        });
      })["catch"](function () {
        jQuery('#passage-delete-modal').modal('hide');
        that.submitting = false;
        var content = 'Failed to delete the Passage.';
        that.$bvToast.toast(content, {
          title: 'Error',
          variant: 'danger',
          solid: true
        });
      });
    },
    openQuestionFormModal: function openQuestionFormModal(question) {
      if (question === undefined || question === null) {
        this.current.question = {
          id: '',
          question: '',
          type: {
            id: 1,
            name: 'Multiple choice, single answer'
          },
          points: 1,
          difficulty: 1,
          choices: [{
            ud: _.uniqueId('_'),
            id: '',
            choice: '',
            is_correct: 0,
            points: 0
          }]
        };
      } else {
        this.current.question = question;
      }

      this.$bvModal.show('question-form-modal');
    },
    refreshQuestions: function refreshQuestions() {
      var that = this;
      that.loading.questions = true;
      var url = that.url.path + '/' + that.quiz.id + '/' + 'sections' + '/' + that.current.section.id + '/' + 'passages' + '/' + that.current.passage.id + '/' + 'questions?l=0';
      axios({
        method: 'GET',
        url: url
      }).then(function (response) {
        that.current.passage.questions = response.data;
        that.loading.questions = false;
      })["catch"](function () {
        var content = 'Failed to load the Questions.';
        that.$bvToast.toast(content, {
          title: 'Error',
          variant: 'danger',
          solid: true
        });
        that.loading.questions = false;
      });
    },
    sortQuestions: function sortQuestions(event) {
      var that = this;
      that.current.passage.questions.splice(event.newIndex, 0, that.current.passage.questions.splice(event.oldIndex, 1)[0]);
      that.current.passage.questions.forEach(function (item, index) {
        if (item !== undefined) {
          item.ordering = index;
        }
      });
      var url = that.url.path + '/' + that.quiz.id + '/' + 'sections' + '/' + that.current.section.id + '/' + 'passages' + '/' + that.current.passage.id + '/' + 'questions';
      axios({
        method: 'PUT',
        url: url,
        data: {
          action: 'sort',
          questions: that.current.passage.questions
        }
      }).then(function (response) {
        var content = 'Questions has been sorted.';
        that.$bvToast.toast(content, {
          title: 'Success',
          variant: 'success',
          solid: true
        });
      })["catch"](function (error) {
        var content = 'Failed to sort the Questions. Reason: ' + error.response.data.reason;
        that.$bvToast.toast(content, {
          title: 'Error',
          variant: 'danger',
          solid: true
        });
      });
    },
    submitQuestion: function submitQuestion(event) {
      var that = this;
      that.submitting = true;
      var url = that.url.path + '/' + that.quiz.id + '/' + 'sections' + '/' + that.current.section.id + '/' + 'passages' + '/' + that.current.passage.id + '/' + 'questions';
      this.$validator.validateAll('question').then(function (result) {
        if (result) {
          axios({
            method: that.current.question.id !== '' ? 'PUT' : 'POST',
            url: url + (that.current.question.id !== '' ? '/' + that.current.question.id : ''),
            data: that.current.question
          }).then(function () {
            that.refreshQuestions();
            jQuery('#question-form-modal').modal('hide');
            var content = 'Question has been ' + (that.current.question.id !== '' ? 'updated' : 'created') + '.';
            that.$bvToast.toast(content, {
              title: 'Success',
              variant: 'success',
              solid: true
            });
            that.submitting = false;
            that.current.question = {
              id: '',
              question: '',
              type: {
                id: 1,
                name: 'Multiple choice, single answer'
              },
              points: '',
              difficulty: 1,
              choices: [{
                ud: _.uniqueId('_'),
                id: '',
                choice: '',
                is_correct: 0,
                points: 1
              }]
            };
            event.preventDefault();
          })["catch"](function () {
            jQuery('#question-form-modal').modal('hide');
            var content = 'Failed to submit the Question.';
            that.$bvToast.toast(content, {
              title: 'Error',
              variant: 'danger',
              solid: true
            });
            that.submitting = false;
            that.current.question = {
              id: '',
              question: '',
              type: {
                id: 1,
                name: 'Multiple choice, single answer'
              },
              points: '',
              difficulty: 1,
              choices: [{
                ud: _.uniqueId('_'),
                id: '',
                choice: '',
                is_correct: 0,
                points: 1
              }]
            };
          });
          event.preventDefault();
        } else {
          that.submitting = false;
          event.preventDefault();
        }
      })["catch"](function () {
        var content = 'Validation failed. Please check the form.';
        that.$bvToast.toast(content, {
          title: 'Error',
          variant: 'danger',
          solid: true
        });
        that.submitting = false;
        event.preventDefault();
      });
    },
    openConfirmDeleteQuestionModal: function openConfirmDeleteQuestionModal(question) {
      this.current.question = question;
      this.$bvModal.show('question-delete-modal');
    },
    deleteQuestion: function deleteQuestion(question) {
      var that = this;
      that.submitting = true;
      var url = that.url.path + '/' + that.quiz.id + '/' + 'sections' + '/' + that.current.section.id + '/' + 'passages' + '/' + that.current.passage.id + '/' + 'questions' + '/' + question.id;
      axios({
        method: 'DELETE',
        url: url
      }).then(function () {
        that.current.question = {
          id: '',
          question: '',
          type: {
            id: 1,
            name: 'Multiple choice, single answer'
          },
          points: '',
          difficulty: 1,
          choices: [{
            ud: _.uniqueId('_'),
            id: '',
            choice: '',
            is_correct: false,
            points: 0
          }]
        };
        that.refreshQuestions();
        jQuery('#question-delete-modal').modal('hide');
        that.submitting = false;
        var content = 'Question has been deleted.';
        that.$bvToast.toast(content, {
          title: 'Success',
          variant: 'success',
          solid: true
        });
      })["catch"](function () {
        jQuery('#question-delete-modal').modal('hide');
        that.submitting = false;
        var content = 'Failed to delete the Question.';
        that.$bvToast.toast(content, {
          title: 'Error',
          variant: 'danger',
          solid: true
        });
      });
    },
    addChoice: function addChoice() {
      var emptyChoice = {
        ud: _.uniqueId('_'),
        td: _.uniqueId('td-'),
        id: '',
        choice: '',
        is_correct: false,
        points: 0
      };
      this.current.question.choices.push(emptyChoice);
      setTimeout(function () {
        tinymce.execCommand('mceFocus', false, emptyChoice.td);
      }, 100);
    },
    deleteChoice: function deleteChoice(index) {
      this.current.question.choices.splice(index, 1);
    },
    sortChoices: function sortChoices(event) {
      var that = this;
      that.current.question.choices.splice(event.newIndex, 0, that.current.question.choices.splice(event.oldIndex, 1)[0]);
      that.current.question.choices.forEach(function (item, index) {
        if (item !== undefined) {
          item.ordering = index;
        }
      });
    },
    changePoints: function changePoints(event, choice) {
      if (event === true) {
        choice.points = 1;
      } else {
        choice.points = 0;
      }
    },
    openMediaManager: function openMediaManager(target) {
      this.current.mmtarget = target;
      this.$bvModal.show('mediamanager-modal');
    }
  },
  created: function created() {
    var that = this;
    window.addEventListener('message', function (event) {
      if (that.current.mmtarget === 'explain-video') {
        that.current.question.explain_video = event.data.url;
        that.$bvModal.hide('mediamanager-modal');
      }
    }, false);
  },
  beforeMount: function beforeMount() {
    var that = this;
    that.quiz = Quiz;
    axios({
      method: 'GET',
      url: that.section.types.url + '?l=0'
    }).then(function (data) {
      that.section.types.items = data.data;
    });
    axios({
      method: 'GET',
      url: that.section.tags.url + '?l=0'
    }).then(function (data) {
      that.section.tags.items = data.data;
    });
    that.refreshSections();
  },
  mounted: function mounted() {
    var that = this;
    that.$root.$on('bv::modal::shown', function (bvEvent, modalId) {
      switch (modalId) {
        case 'section-form-modal':
          that.keys.section.description = _.uniqueId('_');
          break;

        case 'passage-form-modal':
          that.keys.passage.description = _.uniqueId('_');
          that.keys.passage.content = _.uniqueId('_');
          break;

        case 'question-form-modal':
          that.keys.question.description = _.uniqueId('_');

          for (var i = 0; i < that.current.question.choices.length; i++) {
            that.current.question.choices[i].ud = _.uniqueId('_');
            that.current.question.choices[i].td = _.uniqueId('td-');
          }

          break;
      }
    });
    document.addEventListener('focusin', function (e) {
      var closest = e.target.closest(".tox-tinymce-aux, .tox-dialog, .moxman-window, .tam-assetmanager-root");

      if (closest !== null && closest !== undefined) {
        e.stopImmediatePropagation();
      }
    });
  }
});

/***/ }),

/***/ 20:
/*!************************************************************************!*\
  !*** multi ./resources/js/components/quizzes/quizzes/questionnaire.js ***!
  \************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /home/james/Projects/Web/GTC015 Quizmaster/resources/js/components/quizzes/quizzes/questionnaire.js */"./resources/js/components/quizzes/quizzes/questionnaire.js");


/***/ })

/******/ });