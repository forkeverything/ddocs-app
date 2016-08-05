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