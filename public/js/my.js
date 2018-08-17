webpackJsonp([2],{

/***/ 254:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(50);


/***/ }),

/***/ 50:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "my", function() { return my; });
var _this = this;

var my = {
    axios: function (_axios) {
        function axios() {
            return _axios.apply(this, arguments);
        }

        axios.toString = function () {
            return _axios.toString();
        };

        return axios;
    }(function () {
        var load = Loading.service({ fullscreen: true });
        axios({
            method: 'get',
            url: '/json/' + newFormName + '.json'
        }).then(function (response) {
            load.close();
            console.log(response);
            if ('msg' in response.data) {
                _this.$message({
                    showClose: true,
                    message: response.data.msg,
                    type: 'error',
                    duration: 0
                });
            } else {
                _this.configs = response.data;
                _this.$message({
                    showClose: true,
                    message: '保存成功!',
                    type: 'success'
                });
            }
        }).catch(function (error) {
            load.close();
            _this.$message({
                showClose: true,
                message: '保存失败!' + error,
                type: 'error',
                duration: 0
            });
        });
    })
};

/***/ })

},[254]);