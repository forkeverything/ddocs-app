module.exports = {
    name: 'FetchesDataFromEloquentRepository',
    data: function () {
        return {
            ajaxReady: true,
            repoSearch: '',
            request: '',
            response: {},
            showFiltersDropdown: false,
            urlHistory: true,
            infScroll: true,
            loadingRepoResults: true
        };
    },
    computed: {
        params() {
            return this.response.query_parameters;
        }
    },
    methods: {
        checkSetup() {
            if (!this.requestUrl) throw new Error("No Request URL set as 'requestUrl' ");
            if (this.hasFilter && _.isEmpty(this.filterOptions)) throw new Error("Need filterOptions[] defined to use filters");
        },
        fetchResults(query) {
            this.loadingRepoResults = true;
            let url = this.requestUrl;

            // If we're making a new query parameter, use it. Otherwise, if we're on page-load and have
            // one in our address bar, we'll use that one.
            query = query || window.location.href.split('?')[1];

            // Attach query to URL
            if (query) url = url + '?' + query;

            // this.finishLoading = false;

            this.$http.get(url, {
                before: function (xhr) {
                    if(this.request) RequestsMonitor.abortRequest(this.request);
                    this.request = xhr;
                    RequestsMonitor.pushOntoQueue(xhr);
                }
            }).then((response) => {
                this.loadingRepoResults = false;
                // update data
                this.response = response.json();
                // push state (if query is different from url)
                if(this.urlHistory) pushStateIfDiffQuery(query);
                // scroll top
                if (this.container) {
                    document.getElementById(this.container).scrollTop = 0;
                } else {
                    document.getElementsByTagName('body')[0].scrollTop = 0;
                }
                // ready again
            }, (response) => {
                console.log("Error fetching results: " + url);
            });
        },
        changeSort: function (sort) {
            if (this.params.sort === sort) {
                let order = (this.params.order === 'asc') ? 'desc' : 'asc';
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
        searchTerm: _.throttle(function () {
            let term = this.repoSearch || null;
            this.fetchResults(updateQueryString({
                search: term,
                page: 1
            }))
        }, 250),
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
        removeFilter: function (filter) {
            var queryObj = {
                page: 1
            };
            queryObj[filter] = null;
            this.fetchResults(updateQueryString(queryObj));
        },
        removeAllFilters: function () {
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
            this.loadingRepoResults = true;
            let query = updateQueryString('page', this.response.current_page + 1);
            let url = this.requestUrl + '?' + query;
            if (!this.ajaxReady) return;
            this.ajaxReady = false;
            this.$http.get(url, {
                before(xhr) {
                    RequestsMonitor.pushOntoQueue(xhr);
                }
            }).then((response) => {
                this.loadingRepoResults = false;
                this.response.current_page = response.json().current_page;
                let data = response.json().data;
                for (let i = 0; i < data.length; i++) {
                    this.response.data.push(data[i]);
                }
                this.ajaxReady = true;
            }, (response) => {
                console.log('error fetching data');
                console.log(response);
            })
        },
        scrollList: _.throttle(function (event) {
            let el = document.getElementById(this.container);
            if ($(el).innerHeight() + $(el).scrollTop() >= (el.scrollHeight - 100)) this.fetchNextPage();
        }, 1000)
    },
    created(){
        this.checkSetup();
    },
    mounted() {
        this.fetchResults();
        if(this.urlHistory) onPopCallFunction(this.fetchResults);
        if(this.infScroll) this.$nextTick(this.scrollList);
    }
};