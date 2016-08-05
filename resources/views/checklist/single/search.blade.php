<form id="form-checklist-search" @submit.prevent="searchTerm">
    <div class="input-group">
        <div class="input-group-btn">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Filters <span class="caret"></span></button>
            @include('checklist.single.filters.menu')
        </div>
        <input class="form-control input-search"
               type="text"
               placeholder="Search..."
        @keyup="searchTerm"
        v-model="params.search"
        :class="{
                                    'active': params.search && params.search.length > 0
                               }"   ``
        >
    </div>
</form>