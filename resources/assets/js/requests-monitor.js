module.exports = {
    queue: {
        fetch: [],
        update: []
    },

    /**
     * Give every request a unique ID so when we need to push a request off
     * the queue, we can be sure we're dealing with the right request.
     * @private
     */

    _pushRequestIdInterceptor() {
        Vue.http.interceptors.push((request, next) => {
            request._uid = randomString(10);
            next();
        });
    },

    /**
     * Abort all pending requests in queue container.
     *
     * @param queueContainer
     * @private
     */

    _abortRequests(queueContainer) {
        for (let i = 0; i < queueContainer.length; i) {
            queueContainer.shift().abort();
        }
    },

    /**
     * Check for pending requests in queue or specific container.
     *
     * @param container
     * @returns {boolean}
     * @private
     */

    _hasPendingRequests(container) {
        if(container) {
            return this.queue[container].length > 0;
        } else {
            let hasRequests = false;
            for (let type in this.queue) {
                if (this.queue.hasOwnProperty(type)) {
                    if (this.queue[type].length > 0) {
                        hasRequests = true;
                        break;
                    }
                }
            }
            return hasRequests;
        }
    },


    /**
     * Which container to put request in.
     *
     * @param xhr
     * @returns {string}
     * @private
     */

    _getContainer(xhr){
        return xhr.method === 'GET' ? 'fetch' : 'update';
    },

    /**
     * Push request onto queue.
     *
     * @param xhr
     */

    pushOntoQueue(xhr) {
        let container = this._getContainer(xhr);
        this.queue[container].push(xhr);
    },

    /**
     * Remove request from queue.
     *
     * @param xhr
     */

    removeFromQueue(xhr){
        let container = this._getContainer(xhr);
        let index = _.indexOf(this.queue[container], xhr);
        if (index !== -1) this.queue[container].splice(index, 1);
    },

    /**
     * Ask user to confirm if they are about to leave page
     * with pending requests.
     *
     * @private
     */

    _addUnloadEventListener(){
        window.addEventListener("beforeunload", (e) => {
            let confirmationMessage = "\o/";
            if(this._hasPendingRequests('update')) {
                (e || window.event).returnValue = confirmationMessage; //Gecko + IE
                return confirmationMessage;                            //Webkit, Safari, Chrome
            }
        });
    },

    /**
     * Flush / Clear queue.
     *
     * @param container
     */

    flushQueue(container) {
        if (container) {
            this._abortRequests(this.queue[container]);
        } else {
            for (let type in this.queue) {
                if (this.queue.hasOwnProperty(type)) {
                    this._abortRequests(this.queue[type]);
                }
            }
        }
    },

    /**
     * Run the functions we need to run on load.
     * Called once from 'bootstrap.js'
     */

    setup() {
        this._pushRequestIdInterceptor();
        this._addUnloadEventListener();
    }
};