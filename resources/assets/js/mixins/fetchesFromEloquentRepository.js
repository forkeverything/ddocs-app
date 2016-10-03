module.exports = {
    name: 'FetchesDataFromEloquentRepository',
    data: function() {
        return {
            ajaxReady: true,
            request: {},
            response: {},
            showFiltersDropdown: false
        };
    },
    computed: {
        params() {
            return this.response.query_parameters;
        }
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

            self.$http.get(url, {
                before: function(xhr) {
                    this.request = xhr;
                }
            }).then((response) => {
                // update data
                self.response = response.json();
                // push state (if query is different from url)
                pushStateIfDiffQuery(query);
                // scroll top
                if(this.container) {
                    document.getElementById(this.container).scrollTop = 0;
                } else {
                    document.getElementsByTagName('body')[0].scrollTop = 0;
                }
                // ready again
                self.ajaxReady = true;
            }, (response) => {
                console.log(response);
                self.ajaxReady = true;
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
            if (this.request && this.request.readyState != 4) {
                this.request.abort();
                this.ajaxReady = true;
            }
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
        addFilter: function (filter) {
            let queryObj = {
                page: 1
            };
            queryObj[filter.name] = fiter.value || [filter.minValue, filter.maxValue];
            this.fetchResults(updateQueryString(queryObj));
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
        },
        fetchNextPage() {
            // If we are at last page - return
            if (this.response.current_page === this.response.last_page) return;
            let query = updateQueryString('page', this.response.current_page + 1);
            let url = this.requestUrl + '?' + query;
            if (!this.ajaxReady) return;
            this.ajaxReady = false;
            this.$http.get(url).then((response) => {
                this.response.current_page = response.json().current_page;
                let data = response.json().data;
                for(let i = 0; i < data.length; i++) {
                    this.response.data.push(data[i]);
                }
                this.ajaxReady = true;
            }, (response) => {
                console.log('error fetching data');
                console.log(response);
            })
        },
        infLoadNextPage() {
        }
    },
    created(){
        this.checkSetup();
    },
    mounted() {
        this.fetchResults();
        onPopCallFunction(this.fetchResults);
    }
};