$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'Authorization': 'Bearer ' + localStorage.getItem('token')
    }
});
$(document).ready(function () {
    autosize($('.autosize'));
});

/**
 * Takes string and capitalizes the first letter
 * of each word.
 */
function strCapitalize(str) {
    return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
}

/**
 * Escapes html entities for a string to be inserted
 * into the DOM.
 *
 * @type {{&: string, <: string, >: string, ": string, ': string, /: string}}
 */
var entityMap = {
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
function escapeHtml(string) {
    return String(string).replace(/[&<>"'\/]/g, function (s) {
        return entityMap[s];
    });
}

/**
 * Takes an AJAX response and vue instance
 * and emits form errors to be caught by
 * 'form-errors' Vue Component.
 *
 * @param response
 * @param vue
 */
function vueValidation(response, vue) {
    if(response.status === 422) {
        var eventBus = vue || vueGlobalEventBus;
        eventBus.$emit('new-errors', response.responseJSON);
    }
}

/**
 * Broadcasts clear errors event.
 *
 * @param vue
 */
function vueClearValidationErrors(vue) {
    var eventBus = vue || vueGlobalEventBus;
    eventBus.$emit('clear-errors');
}

/**
 * Takes an string and tells you if it's a valid email!
 *
 * @returns {boolean}
 * @param string
 */
function validateEmail(string) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(string);
}

/**
 * Returns whether given string is all
 * alphanumeric (no symbols).
 *
 * @returns {boolean}
 * @param string
 */
function alphaNumeric(string) {
    var re = /^[A-Za-z\d\s]+$/;
    return re.test(string);
}

/**
 * Retrieves the Query String Value by
 * Name
 * 
 * @param name
 * @param url
 * @returns {*}
 */
function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)", "i"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

/**
 * Takes a 2 Strings (name, value) pair or an Object containing
 * several name-value pairs and updates the current query
 * and returns it.
 * 
 * @returns {string}
 */
function updateQueryString() {
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
    } else if(typeof arguments[0] === 'object'){
        // Received an object with key-value pairs of query names and value to update
        _.forEach(arguments[0], function (value, key) {
            if(value) {
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
}

/**
 * Wrapper function for encodeURI that also accepts
 * an array and encodes each part before joining
 * them together with a '+'
 *
 * @param value
 * @returns {*}
 * @constructor
 */
function URIEncoder(value) {
    if(value.constructor === Array)  {
        value = _.map(value, function (i) { if(i && i.replace(/\s/g, "").length > 0) return encodeURI(i); }).join('+');
    } else {
        value = encodeURI(value)
    }
    return value;
}

/**
 * When browser has pop-state (ie back / forward)
 * run this function to re-retrieve the data
 *
 * @param callback
 */
function onPopCallFunction(callback)
{
    window.onpopstate = function (e) {
        if (e.state) {
            callback();
        }
    }
}

/**
 * Takes a query string and if it is  different to
 * the current query string, it will update the
 * browsers state, so we can use nav buttons
 * 
 * @param query
 */
function pushStateIfDiffQuery(query) {
    if (query && query !== window.location.href.split('?')[1]) {
        window.history.pushState({}, "", '?' + query);
    }
}

/**
 * Formats a number into comma-separated thousands
 * @param val
 * @returns {*}
 */
function formatNumber(val) {
    if(isNaN(parseFloat(val))) return val;
    //Seperates the components of the number
    var n = val.toString().split(".");
    //Comma-fies the first part
    n[0] = n[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    //Combines the two sections
    return n.join(".");
}

/**
 * Checks if a given value is a numeric (ie. a number)
 * @param n
 * @returns {boolean}
 */
function isNumeric(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})
Vue.directive('datepicker', {
    params: ['button-only'],
    bind: function() {
        if(this.params.buttonOnly) {
            $(this.el).datepicker({
                dateFormat: "dd/mm/yy",
                minDate: 0,
                buttonImage: '/images/icons/calendar.png',
                buttonImageOnly: true,
                showOn: 'both'
            });
        } else {
            $(this.el).datepicker({
                dateFormat: "dd/mm/yy",
                minDate: 0
            });
        }
    }
});
Vue.directive('modal', {
    twoWay: true,
    update: function () {
        var self = this;
        
        $(this.el).click(function () {
            self.set(false);
        });

        $(this.el).children().click(function (e) {
            e.stopPropagation();
        });
    }
});
Vue.filter('diffHuman', function (value) {
    if(! value || value == '') return;
    if (value !== '0000-00-00 00:00:00') {
        return moment(value, "YYYY-MM-DD HH:mm:ss").fromNow();
    }
    return value;
});
Vue.filter('properDateModel', {
    // model -> view
    // formats the value when updating the input element.
    read: function (value) {
        if (value.replace(/\s/g, "").length > 0) {
            return moment(value, "YYYY-MM-DD").format('DD/MM/YYYY');
        }
        return value;
    },
    // view -> model
    // formats the value when writing to the data.
    write: function (val, oldVal) {
        if(val.replace(/\s/g, "").length > 0) {
            return moment(val, "DD/MM/YYYY").format("YYYY-MM-DD");
        }
        return val;
    }
});
Vue.filter('dateTime', function (value) {
    if(! value || value == '') return;
    if (value !== '0000-00-00 00:00:00') {
        return moment(value, "YYYY-MM-DD HH:mm:ss").format('DD MMM YYYY, h:mm a');
    }
    return value;
});

Vue.filter('date', function (value) {
    if (value !== '0000-00-00 00:00:00') {
        return moment(value, "YYYY-MM-DD HH:mm:ss").format('DD/MM/YYYY');
    }
    return value;
});
Vue.filter('easyDate', function (value) {
    if(!value) return;
    if (value !== '0000-00-00 00:00:00') {
        return moment(value, "YYYY-MM-DD HH:mm:ss").format('DD MMM YYYY');
    }
    return value;
});

Vue.filter('easyDateModel', {
    // model -> view
    // formats the value when updating the input element.
    read: function (value) {
        console.log(value);
        var date = moment(value, "DD-MM-YYYY");
        if (value && date) {
            return moment(value, "DD-MM-YYYY").format('DD MMM YYYY');
        }
        return value;
    },
    // view -> model
    // formats the value when writing to the data.
    write: function (val, oldVal) {
        return val;
    }
});
Vue.component('form-errors', {
    template: '<div class="validation-errors" v-show="errors.length > 0">' +
    '<ul class="errors-list list-unstyled"' +
    'v-show="errors.length > 0"' +
    '>' +
    '<li v-for="error in errors">{{ error }}</li>' +
    '</ul>' +
    '</div>',
    data: function () {
        return {
            errors: []
        }
    },
    props: ['event-bus'],
    ready: function() {
        var self = this;

        var eventBus = this.eventBus || vueGlobalEventBus;

        eventBus.$on('new-errors', function (errors) {
            var newErrors = [];
            _.forEach(errors, function (error) {
                if (newErrors.indexOf(error[0]) == -1) newErrors.push(error[0]);
            });
            self.errors = newErrors;
        });

        eventBus.$on('clear-errors', function(errors) {
            self.errors = [];
        });
    }
});
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-82446082-1', 'auto');
ga('send', 'pageview');
//# sourceMappingURL=dependencies.js.map
