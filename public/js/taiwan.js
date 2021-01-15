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
/******/ 	return __webpack_require__(__webpack_require__.s = 3);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/taiwan.js":
/*!********************************!*\
  !*** ./resources/js/taiwan.js ***!
  \********************************/
/*! no static exports found */
/***/ (function(module, exports) {

var app = new Vue({
  el: "#app",
  data: {
    filter: "",
    place_data: {
      taipei_city: {
        place: "臺北市",
        weather: "Rainy"
      },
      new_taipei_city: {
        place: "新北市",
        weather: "Rainy"
      },
      taichung_city: {
        place: "台中市",
        weather: "Rainy"
      },
      tainan_city: {
        place: "臺南市",
        weather: "Rainy"
      },
      kaohsiung_city: {
        place: "高雄市",
        weather: "Rainy"
      },
      keelung_city: {
        place: "基隆市",
        weather: "Rainy"
      },
      taoyuan_country: {
        place: "桃園市",
        weather: "Rainy"
      },
      hsinchu_city: {
        place: "新竹市",
        weather: "Rainy"
      },
      hsinchu_country: {
        place: "新竹縣",
        weather: "Rainy"
      },
      miaoli_country: {
        place: "苗栗縣",
        weather: "Rainy"
      },
      changhua_country: {
        place: "彰化縣",
        weather: "Rainy"
      },
      nantou_country: {
        place: "南投縣",
        weather: "Rainy"
      },
      yunlin_country: {
        place: "雲林縣",
        weather: "Cloudy"
      },
      chiayi_city: {
        place: "嘉義市",
        weather: "Rainy"
      },
      chiayi_country: {
        place: "嘉義縣",
        weather: "Cloudy"
      },
      pingtung_country: {
        place: "屏東縣",
        weather: "Cloudy"
      },
      yilan_country: {
        place: "宜蘭縣",
        weather: "Cloudy"
      },
      hualien_country: {
        place: "花蓮縣",
        weather: "Sunny"
      },
      taitung_country: {
        place: "台東縣",
        weather: "Sunny"
      },
      penghu_country: {
        place: "澎湖縣",
        weather: "Cloudy"
      },
      kinmen_country: {
        place: "金門縣",
        weather: "Sunny"
      },
      lienchiang_country: {
        place: "連江縣",
        weather: "Rainy"
      }
    },
    h1: '縣市中文',
    h2: '縣市英文'
  }
});
$("path").click(function (e) {
  var tagname = $(this).attr("data-name");
  app.h1 = app.place_data[tagname].place;
  app.h2 = tagname; // console.log(app.place_data[tagname].place);
});

/***/ }),

/***/ 3:
/*!**************************************!*\
  !*** multi ./resources/js/taiwan.js ***!
  \**************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\xampp\htdocs\weather_fun\resources\js\taiwan.js */"./resources/js/taiwan.js");


/***/ })

/******/ });