/**
 * Takes string and capitalizes the first letter
 * of each word.
 */
window.strCapitalize = function (str) {
    return str.replace(/\w\S*/g, function (txt) {
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
};

/**
 * Escapes html entities for a string to be inserted
 * into the DOM.
 *
 * @type {{&: string, <: string, >: string, ": string, ': string, /: string}}
 */
let entityMap = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': '&quot;',
    "'": '&#39;',
    "/": '&#x2F;'
};

/**
 * Escapes a given string that has HTML elements.
 *
 * @param string
 * @returns {string}
 */
window.escapeHtml = function (string) {
    return String(string).replace(/[&<>"'\/]/g, function (s) {
        return entityMap[s];
    });
};

/**
 * Takes an AJAX response and vue instance
 * and emits form errors to be caught by
 * 'form-errors' Vue Component.
 *
 * @param response
 * @param eventBus
 */
window.vueValidation = function (response, eventBus = vueGlobalEventBus) {
    if (response.status === 422) {
        eventBus.$emit('new-errors', JSON.parse(response.body));
    }
};

/**
 * Broadcasts clear errors event.
 *
 * @param eventBus
 */
window.vueClearValidationErrors = function (eventBus = vueGlobalEventBus) {
    eventBus.$emit('clear-errors');
};

/**
 * Takes an string and tells you if it's a valid email!
 *
 * @returns {boolean}
 * @param string
 */
window.validateEmail = function (string) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(string);
};

/**
 * Returns whether given string is all
 * alphanumeric (no symbols).
 *
 * @returns {boolean}
 * @param string
 */
window.alphaNumeric = function (string) {
    var re = /^[A-Za-z\d\s]+$/;
    return re.test(string);
};

/**
 * Retrieves the Query String Value by
 * Name
 *
 * @param name
 * @param url
 * @returns {*}
 */
window.getParameterByName = function (name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)", "i"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
};

/**
 * Takes a 2 Strings (name, value) pair or an Object containing
 * several name-value pairs and updates the current query
 * and returns it.
 *
 * @returns {string}
 */
window.updateQueryString = function () {
    // Get and prep existing query so we can make changes to it
    var fullQuery = window.location.href.split('?')[1];         // into pairs
    var queryArray = fullQuery ? fullQuery.split('&') : [];     // into key-values
    var queryObj = {};                                          // empty object

    // Build up object
    queryArray.forEach(function (item) {
        var x = item.split('=');
        queryObj[x[0]] = x[1];
    });

    /**
     * Make Updates to query
     * TO DO ::: CHECK HERE
     */
    if (typeof arguments[0] === 'string' && arguments.length > 1) {
        // Only update single query name - set the new name and value
        queryObj[arguments[0]] = URIEncoder(arguments[1]);
    } else if (typeof arguments[0] === 'object') {
        // Received an object with key-value pairs of query names and value to update
        _.forEach(arguments[0], function (value, key) {
            if (value) {
                queryObj[key] = URIEncoder(value);
            } else {
                delete queryObj[key];
            }
        });
    } else {
        // only received a key - delete from query
        delete queryObj[arguments[0]];
    }

    // Make new query to return
    var newQuery = '';
    // Go through object and add everything back as a string
    _.forEach(queryObj, function (value, name) {
        newQuery += name + '=' + value + '&';
    });
    // Finally - return our new string!
    return newQuery.substring(0, newQuery.length - 1);  // Trim last '&'
};

/**
 * Wrapper function for encodeURI that also accepts
 * an array and encodes each part before joining
 * them together with a '+'
 *
 * @param value
 * @returns {*}
 * @constructor
 */
window.URIEncoder = function (value) {
    if (value.constructor === Array) {
        value = _.map(value, function (i) {
            if (i && i.replace(/\s/g, "").length > 0) return encodeURI(i);
        }).join('+');
    } else {
        value = encodeURI(value)
    }
    return value;
};

/**
 * When browser has pop-state (ie back / forward)
 * run this function to re-retrieve the data
 *
 * @param callback
 */
window.onPopCallFunction = function (callback) {
    window.onpopstate = function (e) {
        if (e.state) {
            callback();
        }
    }
};

/**
 * Takes a query string and if it is  different to
 * the current query string, it will update the
 * browsers state, so we can use nav buttons
 *
 * @param query
 */
window.pushStateIfDiffQuery = function (query) {
    if (query && query !== window.location.href.split('?')[1]) {
        window.history.pushState({}, "", '?' + query);
    }
};

/**
 * Formats a number into comma-separated thousands
 * @param val
 * @returns {*}
 */
window.formatNumber = function (val) {
    if (isNaN(parseFloat(val))) return val;
    //Seperates the components of the number
    var n = val.toString().split(".");
    //Comma-fies the first part
    n[0] = n[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    //Combines the two sections
    return n.join(".");
};

/**
 * Checks if a given value is a numeric (ie. a number)
 * @param n
 * @returns {boolean}
 */
window.isNumeric = function (n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
};

/**
 * Generates a random string [a-zA-Z0-9]
 *
 * @param length
 * @returns {string}
 */
window.randomString = function (length = 8) {
    let text = "";
    let possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for (let i = 0; i < length; i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
};