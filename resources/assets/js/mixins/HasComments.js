module.exports = {
    data() {
        return {
            comments: [],
            loadingComments: false
        };
    },
    methods: {
        addComment(body) {
            if (!body) return;       // no empty comments,
            this.$http.post(this.commentsUrl, {
                body: body
            }, {
                before(xhr) {
                    RequestsMonitor.pushOntoQueue(xhr);
                }
            }).then((response) => {
                // success
                this.comments.push(response.json());
            }, (response) => {
                // error
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
