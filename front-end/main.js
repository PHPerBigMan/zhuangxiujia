require('normalize.css');
require('bootstrap/dist/css/bootstrap.min.css');
require('./css/style.scss');
$ = require('expose-loader?jQuery!expose-loader?$!jquery');
require('bootstrap');
require('./lib/jqPaginator.min');

$(() => {
    // 导航hover
    $('.nav-item-container').hover(
        function () {
            $(this).find('.nav-hidden').slideDown(200);
        },
        function () {
            $(this).find('.nav-hidden').slideUp(200);
        }
    );

    $('.m-index .section-2 .index-icon').hover(
        function () {
            $(this).find('.img-wrap').animate({top: '-190px'}, 200);
        },
        function () {
            $(this).find('.img-wrap').animate({top: 0}, 200);
        }
    );

    // 协议勾选
    $('#protocol-checkbtn').click(() => {
        let $check = $('#protocol-checkbox');
        let nowVal = $check.attr('checked');
        $check.attr('checked', !nowVal);
    });
});require('normalize.css');
require('bootstrap/dist/css/bootstrap.min.css');
require('./css/style.scss');
$ = require('expose-loader?jQuery!expose-loader?$!jquery');
require('bootstrap');
require('./lib/jqPaginator.min');

$(() => {
    // 导航hover
    $('.nav-item-container').hover(
        function () {
            $(this).find('.nav-hidden').slideDown(200);
        },
        function () {
            $(this).find('.nav-hidden').slideUp(200);
        }
    );

    $('.m-index .section-2 .index-icon').hover(
        function () {
            $(this).find('.img-wrap').animate({top: '-190px'}, 200);
        },
        function () {
            $(this).find('.img-wrap').animate({top: 0}, 200);
        }
    );

    // 协议勾选
    $('#protocol-checkbtn').click(() => {
        let $check = $('#protocol-checkbox');
        let nowVal = $check.attr('checked');
        $check.attr('checked', !nowVal);
    });
});