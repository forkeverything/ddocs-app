<template>
    <div class="email-example">
        <div class="text-center">
            <button id="button-show-example" type="button" class="btn btn-solid-green" @click="showEmailExample">
                Example
            </button>
        </div>
        <form id="form-email" v-show="emailExample">
            <h5 class="text-muted text-center">Put a - before each file name. Keep each file on a single line. And put
                due dates in [square brackets]</h5>
            <div class="form-group inline-label">
                <label>To:</label>
                <input type="text" value="john@example.com" class="form-control" disabled>
            </div>

            <div class="form-group inline-label">
                <label>Cc:</label>
                <input type="text" value="list@in.filescollector.com" class="form-control" tabindex="-1">
            </div>

            <div class="form-group inline-label">
                <label>Subject:</label>
                <input type="text" value="Files Needed For Project Update" class="form-control" disabled>
            </div>

            <textarea class="form-control" rows="15" tabindex="-1">Hi John,

Great work at the investors meeting, you really brought your A-game.

As we discussed last Tuesday, here is the list of documents I'll need you to prepare:

- Land survey report [12/09/2016]
- Budget reports for last quarter
- Sales contract with Apex co. ltd. [01/10/2016]

Thanks,
Mike</textarea>

            <br>
            <br>
        </form>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                emailExample: false
            }
        },
        methods: {
            showEmailExample: function () {
                this.emailExample = true;
            }
        }
    }
</script>