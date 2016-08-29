<template>
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <li class="paginate-nav to-first" :class="{
                'disabled': currentPage < 3  || currentPage > lastPage
            }" @click="goToPage(1)"
            >
                <a href="#" aria-label="First page"><i class="fa fa-angle-double-left"></i></a>
            </li>
            <li class="paginate-nav prev"
                :class="{
                'disabled': (currentPage - 1) < 1 || currentPage > lastPage
            }"
                @click="goToPage(currentPage - 1)"
            >
                <a href="#" aria-label="Previous"><i class="fa fa-angle-left"></i></a>
            </li>
            <li class="paginate-link"
                v-for="page in paginatedPages"
                :class="{
                'active': currentPage === page,
                'disabled': page > lastPage
            }"
                @click="goToPage(page)"
            >
                <a href="#" aria-label="Go to single page">{{ page }}</a>
            </li>
            <li class="paginate-nav next"
                :class="{
                    'disabled': currentPage >= lastPage
               }"
                @click="goToPage(currentPage + 1)"
            >
                <a href="#" aria-label="Next"><i class="fa fa-angle-right"></i></a>
            </li>
            <li class="paginate-nav to-last"
                :class="{
                   'disabled': currentPage > (lastPage - 2)
               }"
                @click="goToPage(lastPage)"
            >
                <a href="#" aria-label="Last page"><i class="fa fa-angle-double-right"></i></a>
            </li>
        </ul>
    </nav>
</template>
<script>
    export default {
        data: function () {
            return {}
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
        }
    }
</script>