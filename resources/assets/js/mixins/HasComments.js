module.exports = {
    data() {
        return {
            comments: [],
            loadingComments: false,
            saving: false
        };
    },
    methods: {
        addComment(body) {
            if (!body) return;       // no empty comments,
            this.saving = true;
            this.$http.post(this.commentsUrl, {
                body: body
            }, {
                before(xhr) {
                    RequestsMonitor.pushOntoQueue(xhr);
                }
            }).then((response) => {
                // success
                this.comments.push(response.json());
                this.$nextTick(() => {
                    this.saving = false;
                });
            }, (response) => {
                // error
                this.saving = false;
                console.log('error adding comment.');
                console.log(response);
            });
        },
        fetchComments(){
            this.comments = [];
            this.loadingComments = true;
            this.$http.get(this.commentsUrl, {
                before(xhr) {
                    RequestsMonitor.pushOntoQueue(xhr);
                }
            }).then((res) => {
                this.comments = res.json();
                this.loadingComments = false;
            }, (res) => {
                console.log('error fetching comments');
                console.log(res);
            });
        }
    }
};
