// ---- KieCoo.js : Cookies (using JQuery) ----

function bake_cookie(name, value) {
    $.cookie(name,JSON.stringify(value),{ expires : 124*365 });
}

function read_cookie(name) {
    var cookie = $.cookie(name);
    return (typeof cookie !== 'undefined' ) ? JSON.parse($.cookie(name)) : null;
}

function delete_cookie(name) {
    $.removeCookie(name);
}