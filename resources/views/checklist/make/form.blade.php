<form id="form-checklist-make" action="/checklist/make" method="POST">
    {{ csrf_field() }}
    <div class="inline-label">
        <label class="text-muted">To: </label>
        <input id="input-recipient-email" type="email" class="form-control" placeholder="Email" v-model="checklistRecipient" name="recipient">
    </div>
    <h2 id="title-checklist-name"
        v-show="! editingName"
    @click="toggleEditingName"
    @focus="toggleEditingName"
    :class="{
                                    'filled': checklistName
                                }"
    tabindex="0"
    >@{{ checklistNameText }}</h1>
    <input id="input-checklist-name" type="text" class="form-control" v-show="editingName" @blur="toggleEditingName" v-model="checklistName" name="name">
    <textarea id="textarea-new-checklist-description" rows="2" class="autosize form-control borderless" placeholder="description" v-model="checklistDescription" name="description"></textarea>
    <hr>
    <h4>Files List (@{{ fileCount }})</h4>
    <p class="text-muted">Hit Enter/Return to insert a new file. Delete files by clearing it's name.</p>
    <ul class="list-files list-unstyled">
        <li v-for="(index, file) in files" class="single-file">
            <button type="button"
                    class="btn btn-unstyled button-required line-el"
                    :class="{
                                            'required': file.required
                                        }"
            @click="toggleRequired(file)"
            >
            <i class="fa fa-file"></i></button>
            <input type="text"
                   class="form-control input-file-name line-el"
                   v-model="file.name"
                   placeholder="File name"
                   @keyup.enter="addAnotherFileAfter(file)"
                   @keyup.delete="removeFile(file)"
                   @keyup.up="goTo('prev', file)"
                   @keyup.down="goTo('next', file)"
                   :name="'files[' + index + '][name]'"
            >
            <input type="text"
                   class="input-due line-el"
                   v-model="file.due"
                   v-datepicker
                   placeholder="Due date"
                   tabindex="-1"
                   :class="{'filled': file.due}"
                   :name="'files[' + index + '][due]'"
            >
        </li>
    </ul>
</form>