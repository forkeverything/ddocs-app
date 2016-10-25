<template>
    <div class="pf-downloader">
        <form v-if="canDownload"
              :action=" awsUrl + uploads[0].path"
        >
            <div class="btn-group">

                <button type="submit"
                        class="btn btn-primary btn-sm"
                >
                    <i class="fa fa-download"></i>Download
                </button>

                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <div class="dropdown-menu dropdown-menu-right" role="menu" @click.stop="">
                    <h4>Previous Versions</h4>
                    <form :action=" awsUrl + versionPath" class="form-inline">
                    <select class="form-control" v-model="versionPath">
                        <option value="" disabled>Select one</option>
                        <option v-for="upload in uploads" :value="upload.path">{{ upload.created_at | dateTime }}</option>
                    </select>
                        <button type="submit" class="btn btn-info" :disabled="! versionPath">
                            <i class="fa fa-download"></i>
                        </button>
                    </form>
                </div>
            </div>
        </form>

        <button v-if="! canDownload"
                type="button"
                disabled
                class="btn btn-primary btn-sm"
        >
            <i class="fa fa-download"></i>Download
        </button>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                awsUrl: awsURL,
                versionPath: ''
            }
        },
        props: ['uploads'],
        watch: {
            uploads() {
                this.versionPath = '';
            }
        },
        computed: {
            canDownload() {
                return this.uploads && this.uploads.length > 0;
            }
        },
        methods: {
            downloadLatest() {

            }
        }
    }
</script>