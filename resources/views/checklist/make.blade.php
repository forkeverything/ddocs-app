@extends('layouts.app')

@section('content')
    <checklist-make inline-template v-cloak>
        <div id="checklist-make" class="container">
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
                                :class="{
                                    'filled': checklistName
                                }"
                            >@{{ checklistNameText }}</h1>
                            <input id="input-checklist-name" type="text" class="form-control" v-show="editingName" @blur="toggleEditingName" v-model="checklistName">
                            <textarea id="textarea-new-checklist-description" rows="2" class="autosize form-control borderless" placeholder="description" v-model="checklistDescription"></textarea>
                        <hr>
                        <h4>Files List</h4>
                        <ul class="list-files list-unstyled">
                            <li v-for="file in files" class="single-file">
                                <input type="text"
                                       class="form-control input-file-name"
                                       v-model="file.name"
                                       placeholder="File name"
                                       @keyup.enter="addAnotherFileAfter(file)"
                                       @keyup.delete="removeFile(file)"
                                       @keyup.up="goTo('prev', file)"
                                       @keyup.down="goTo('next', file)"
                                >
                                <input type="text"
                                       class="input-due"
                                       v-model="file.due"
                                       v-datepicker
                                       placeholder="Due date"
                                       tabindex="-1"
                                       :class="{'filled': file.due}"
                                >
                            </li>
                        </ul>


                        <div class="text-right">
                            <button type="button" class="btn btn-solid-green">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </checklist-make>
@endsection
