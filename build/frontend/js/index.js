/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "jquery":
/*!*************************!*\
  !*** external "jQuery" ***!
  \*************************/
/***/ ((module) => {

module.exports = window["jQuery"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!*******************************!*\
  !*** ./src/frontend/index.js ***!
  \*******************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);

(function ($) {
  "use strict";

  $('.pwb-dropdown-widget').on('change', function () {
    var href = $(this).find(":selected").val();
    location.href = href;
  });
  if (typeof $.fn.slick === 'function') {
    $('.pwb-carousel').slick({
      slide: '.pwb-slick-slide',
      infinite: true,
      draggable: false,
      prevArrow: '<div class="slick-prev"><span>' + pwb_ajax_object.carousel_prev + '</span></div>',
      nextArrow: '<div class="slick-next"><span>' + pwb_ajax_object.carousel_next + '</span></div>',
      speed: 300,
      lazyLoad: 'progressive',
      responsive: [{
        breakpoint: 1024,
        settings: {
          slidesToShow: 4,
          draggable: true,
          arrows: false
        }
      }, {
        breakpoint: 600,
        settings: {
          slidesToShow: 3,
          draggable: true,
          arrows: false
        }
      }, {
        breakpoint: 480,
        settings: {
          slidesToShow: 2,
          draggable: true,
          arrows: false
        }
      }]
    });
    $('.pwb-product-carousel').slick({
      slide: '.pwb-slick-slide',
      infinite: true,
      draggable: false,
      prevArrow: '<div class="slick-prev"><span>' + pwb_ajax_object.carousel_prev + '</span></div>',
      nextArrow: '<div class="slick-next"><span>' + pwb_ajax_object.carousel_next + '</span></div>',
      speed: 300,
      lazyLoad: 'progressive',
      responsive: [{
        breakpoint: 1024,
        settings: {
          slidesToShow: 3,
          draggable: true,
          arrows: false
        }
      }, {
        breakpoint: 600,
        settings: {
          slidesToShow: 2,
          draggable: true,
          arrows: false
        }
      }, {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          draggable: true,
          arrows: false
        }
      }]
    });
  }

  /* ··························· Filter by brand widget ··························· */

  var PWBFilterByBrand = function () {
    var baseUrl = [location.protocol, '//', location.host, location.pathname].join('');
    var currentUrl = window.location.href;
    var marcas = [];
    $('.pwb-filter-products input[type="checkbox"]').each(function (index) {
      if ($(this).prop('checked')) marcas.push($(this).val());
    });
    marcas = marcas.join();
    if (marcas) {
      //removes previous "pwb-brand" from url
      currentUrl = currentUrl.replace(/&?pwb-brand-filter=([^&]$|[^&]*)/i, "");

      //removes pagination
      currentUrl = currentUrl.replace(/\/page\/\d*\//i, "");
      if (currentUrl.indexOf("?") === -1) {
        currentUrl = currentUrl + '?pwb-brand-filter=' + marcas;
      } else {
        currentUrl = currentUrl + '&pwb-brand-filter=' + marcas;
      }
    } else {
      currentUrl = baseUrl;
    }
    location.href = currentUrl;
  };
  var PWBRemoveFilterByBrand = function () {
    var baseUrl = [location.protocol, '//', location.host, location.pathname].join('');
    var currentUrl = window.location.href;
    //removes previous "pwb-brand" from url
    currentUrl = currentUrl.replace(/&?pwb-brand-filter=([^&]$|[^&]*)/i, "");
    //removes pagination
    currentUrl = currentUrl.replace(/\/page\/\d*\//i, "");
    location.href = currentUrl;
  };
  $('.pwb-apply-filter').on('click', function () {
    PWBFilterByBrand();
  });
  $('.pwb-remove-filter').on('click', function () {
    PWBRemoveFilterByBrand();
  });
  $('.pwb-filter-products.pwb-hide-submit-btn input').on('change', function () {
    PWBFilterByBrand();
  });
  var brands = PWBgetUrlParameter('pwb-brand-filter');
  if (brands != null) {
    var brands_array = brands.split(',');
    $('.pwb-filter-products input[type="checkbox"]').prop('checked', false);
    for (var i = 0, l = brands_array.length; i < l; i++) {
      $('.pwb-filter-products input[type="checkbox"]').each(function (index) {
        if ($(this).val()) {
          if (brands_array[i] == $(this).val()) {
            $(this).prop('checked', true);
          }
        }
      });
    }
  } else {
    $('.pwb-filter-products input[type="checkbox"]').prop('checked', false);
  }

  /* ··························· /Filter by brand widget ··························· */
})((jquery__WEBPACK_IMPORTED_MODULE_0___default()));
var PWBgetUrlParameter = function PWBgetUrlParameter(sParam) {
  var sPageURL = decodeURIComponent(window.location.search.substring(1)),
    sURLVariables = sPageURL.split('&'),
    sParameterName,
    i;
  for (i = 0; i < sURLVariables.length; i++) {
    sParameterName = sURLVariables[i].split('=');
    if (sParameterName[0] === sParam) {
      return sParameterName[1] === undefined ? true : sParameterName[1];
    }
  }
};
})();

/******/ })()
;
//# sourceMappingURL=index.js.map