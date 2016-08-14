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
Vue.component('date-range-field', {
    name: 'dateRangeField',
    template: '<div class="date-range-field" @click.stop="">' +
    '<div class="starting">' +
    '<label>starting</label>'+
    '<input type="text" class="filter-datepicker" v-model="min | properDateModel" placeholder="date" v-datepicker>'+
    '</div>' +
    '<span class="dash">-</span>' +
    '<div class="ending">' +
    '<label>Ending</label>' +
    '<input type="text" class="filter-datepicker" v-model="max | properDateModel" placeholder="date" v-datepicker>' +
    '</div>'+
    '</div>',
    props: ['min', 'max']
});
var fetchesFromEloquentRepository = Vue.extend({
    name: 'FetchesDataFromEloquentRepository',
    data: function() {
        return {
            ajaxReady: true,
            request: {},
            response: {},
            params: {},
            showFiltersDropdown: false,
            filter: '',
            filterValue: '',
            minFilterValue: '',
            maxFilterValue: ''
        };
   },
    methods: {
        checkSetup: function() {
            if(!this.requestUrl) throw new Error("No Request URL set as 'requestUrl' ");
            if(this.hasFilter && _.isEmpty(this.filterOptions)) throw new Error("Need filterOptions[] defined to use filters");
        },
        fetchResults: function(query) {
            var self = this,
                url = this.requestUrl;

            // If we got a new query parameter, use it in our request - otherwise, try get query form address bar
            query = query || window.location.href.split('?')[1];
            // If we had a query (arg or parsed) - attach it to our url
            if (query) url = url + '?' + query;

            // self.finishLoading = false;

            if (!self.ajaxReady) return;
            self.ajaxReady = false;
            self.request = $.ajax({
                url: url,
                method: 'GET',
                success: function (response) {
                    // Update data
                    self.response = response;

                    // Attach filters
                    // Reset obj
                    self.params = {};
                    // Loop through and attach everything (Only pre-defined keys in data obj above will be accessible with Vue)
                    _.forEach(response.data.query_parameters, function (value, key) {
                        self.params[key] = value;
                    });


                    // push state (if query is different from url)
                    pushStateIfDiffQuery(query);

                    document.getElementsByTagName('body')[0].scrollTop = 0;

                    self.ajaxReady = true;
                },
                error: function (res, status, req) {
                    console.log(status);
                    self.ajaxReady = true;
                }
            });
        },
        changeSort: function (sort) {
            if (this.params.sort === sort) {
                var order = (this.params.order === 'asc') ? 'desc' : 'asc';
                this.fetchResults(updateQueryString({
                    order: order,
                    page: 1
                }));
            } else {
                this.fetchResults(updateQueryString({
                    sort: sort,
                    order: 'asc',
                    page: 1
                }));
            }
        },
        searchTerm: _.debounce(function () {
            if (this.request && this.request.readyState != 4) this.request.abort();
            var term = this.params.search || null;
            this.fetchResults(updateQueryString({
                search: term,
                page: 1
            }))
        }, 200),
        clearSearch: function () {
            this.params.search = '';
            this.searchTerm();
        },
        resetFilterInput: function() {
            this.filter = '';
            this.filterValue = '';
            this.minFilterValue = '';
            this.maxFilterValue = '';
        },
        addFilter: function () {
            var queryObj = {
                page: 1
            };
            queryObj[this.filter] = this.filterValue || [this.minFilterValue, this.maxFilterValue];
            this.fetchResults(updateQueryString(queryObj));
            this.resetFilterInput();
            this.showFiltersDropdown = false;
        },
        removeFilter: function(filter) {
            var queryObj = {
                page: 1
            };
            queryObj[filter] = null;
            this.fetchResults(updateQueryString(queryObj));
        },
        removeAllFilters: function() {
            var self = this;
            var queryObj = {};
            _.forEach(self.filterOptions, function (option) {
                queryObj[option.value] = null;
            });
            this.fetchResults(updateQueryString(queryObj));
        }
    },
    ready: function() {
        this.checkSetup();
        this.fetchResults();
        onPopCallFunction(this.fetchResults);
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
Vue.component('integer-range-field', {
    name: 'integerRangeField',
    template: '<div class="integer-range-field">'+
    '<input type="number" class="form-control" v-model="min" min="0">'+
    '<span class="dash">-</span>'+
    '<input type="number" class="form-control" v-model="max" min="0">'+
    '</div>',
    props: ['min', 'max']
});
Vue.component('paginator', {
    name: 'paginator',
    template: '<nav aria-label="Page navigation">' +
    '<ul class="pagination">' +
    '   <li class="paginate-nav to-first"' +
    '       :class="{' +
    "           'disabled': currentPage < 3  || currentPage > lastPage" +
    '       }"' +
    '       @click="goToPage(1)"' +
    '   >'+
    '       <a href="#" aria-label="First page"><i class="fa fa-angle-double-left"></i></a>' +
    '   </li>'+
    '   <li class="paginate-nav prev"' +
    '       :class="{'+
    "           'disabled': (currentPage - 1) < 1 || currentPage > lastPage" +
    '       }"'+
    '       @click="goToPage(currentPage - 1)"'+
    '   >'+
    '       <a href="#" aria-label="Previous"><i class="fa fa-angle-left"></i></a>'+
    '   </li>'+
    '   <li class="paginate-link"'+
    '       v-for="page in paginatedPages"'+
    '       :class="{' +
                "'active': currentPage === page,"+
                "'disabled': page > lastPage"+
    '       }"'+
    '       @click="goToPage(page)"'+
    '   >'+
    '       <a href="#" aria-label="Go to single page">{{ page }}</a>'+
    '   </li>'+
    '   <li class="paginate-nav next"'+
    '       :class="{'+
                "'disabled': currentPage >= lastPage"+
    '       }"'+
    '       @click="goToPage(currentPage + 1)"'+
    '    >'+
    '       <a href="#" aria-label="Next"><i class="fa fa-angle-right"></i></a>'+
    '   </li>'+
    '   <li class="paginate-nav to-last"'+
    '       :class="{'+
    "           'disabled': currentPage > (lastPage - 2)"+
    '       }"'+
    '       @click="goToPage(lastPage)"'+
    '   >'+
    '       <a href="#" aria-label="Last page"><i class="fa fa-angle-double-right"></i></a>'+
    '   </li>'+
    '</ul>'+
    '</nav>',
    data: function() {
        return {

        };
    },
    props: ['response', 'reqFunction', 'event-name'],
    computed: {
        currentPage: function() {
            return this.response.current_page;
        },
        lastPage: function() {
            return this.response.last_page
        },
        paginatedPages: function () {
            var startPage;
            var endPage;
            switch (this.currentPage) {
                case 1:
                case 2:
                    // First 2 pages - always return first 5 pages
                    return this.makePagesArray(1, 5);
                    break;
                case this.lastPage:
                case this.lastPage - 1:
                    // Last 2 pages - return last 5 pages
                        // If we have more than 5 pages count back 4 pages. Else start at page 1
                        startPage = (this.lastPage > 5) ? this.lastPage - 4 : 1;
                        endPage = (this.lastPage > 5 ) ? this.lastPage : 5;
                    return this.makePagesArray(startPage, endPage);
                    break;
                default:
                    startPage = this.currentPage - 2;
                    endPage = this.currentPage + 2;
                    return this.makePagesArray(startPage, endPage);
            }
        }
    },
    methods: {
        makePagesArray: function (startPage, endPage) {
            var pagesArray = [];
            for (var i = startPage; i <= endPage; i++) {
                pagesArray.push(i);
            }
            return pagesArray;
        },
        goToPage: function (page) {
            // if we get a custom event name - fire it
            if(this.eventName) vueGlobalEventBus.$emit(this.eventName, page);
            vueGlobalEventBus.$emit('go-to-page', page);
            this.$dispatch('go-to-page', page);         // TODO ::: REMOVE WILL BE DEPRACATED Vue 2.0 <
            if (0 < page && page <= this.lastPage && typeof(this.reqFunction) == 'function') this.reqFunction(updateQueryString('page', page));
        }
    },
    events: {

    },
    ready: function() {

    }
});
Vue.component('per-page-picker', {
    name: 'itemsPerPagePicker',
    template: '<div class="per-page-picker">' +
    // '<select-picker :name.sync="newItemsPerPage" :options.sync="itemsPerPageOptions" :function="changeItemsPerPage"></select-picker>' +
    '<select class="form-control" @change="changeItemsPerPage" v-model="newItemsPerPage">' +
    '<option v-for="option in itemsPerPageOptions">{{ option }}</option>' +
    '</select>' +
    '<span>per page</span>' +
    '</div>',
    el: function () {
        return ''
    },
    data: function () {
        return {
            newItemsPerPage: '',
            itemsPerPageOptions: [
                20, 50, 100
            ]
        };
    },
    props: ['response', 'reqFunction'],
    computed: {
        currentItemsPerPage: function () {
            return this.response.per_page;
        }
    },
    methods: {
        changeItemsPerPage: function () {
            var self = this;
            if (self.newItemsPerPage !== self.currentItemsPerPage) {
                self.reqFunction(updateQueryString({
                    page: 1, // Reset to page 1
                    per_page: self.newItemsPerPage // Update items per page
                }));
            }
        }
    },
    ready: function () {
        this.$watch('currentItemsPerPage', function (numItems) {
            this.newItemsPerPage = numItems;
        });
    }
});
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
//# sourceMappingURL=dependencies.js.map
