@extends('layouts.app')

@section('content')
    <checklist-make inline-template v-cloak>
        <div id="checklist-make" class="container">
            <ol class="breadcrumb">
                <li><a href="/checklist">My Lists</a></li>
                <li class="active">New Checklist</li>
            </ol>

            <h1 class="text-center">New Checklist</h1>
            <p class="text-center">Create a list of all the files that you need along with their due dates (if any) and we'll handle the rest.</p>
            <br>


            <div class="row">
                <div class="col-sm-8 col-sm-offset-2">
                    <form id="form-checklist-make">
                        <div id="new-checklist-recipient">
                                <label class="text-muted">To: </label>
                                <input id="input-recipient-email" type="email" class="form-control" placeholder="Email" v-model="checklistRecipient">
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
                            <input id="input-checklist-name" type="text" class="form-control" v-show="editingName" @blur="toggleEditingName" v-model="checklistName">
                            <textarea id="textarea-new-checklist-description" rows="2" class="autosize form-control borderless" placeholder="description" v-model="checklistDescription"></textarea>
                        <hr>
                        <h4>Files List (@{{ fileCount }})</h4>
                        <p class="text-muted">Hit Enter/Return to insert a new file. Delete files by clearing it's name.</p>
                        <ul class="list-files list-unstyled">
                            <li v-for="file in files" class="single-file">
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
                                >
                                <input type="text"
                                       class="input-due line-el"
                                       v-model="file.due"
                                       v-datepicker
                                       placeholder="Due date"
                                       tabindex="-1"
                                       :class="{'filled': file.due}"
                                >
                            </li>
                        </ul>
                    </form>
                    <div class="text-right">
                        <button type="button" class="btn btn-solid-green" @click="sendChecklist" :disabled="! canSendChecklist">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </checklist-make>
@endsection
