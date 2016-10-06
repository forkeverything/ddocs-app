module.exports = {
    queue: {
        fetch: [],
        update: []
    },

    // Loop through and abort each request.
    abortRequests(queueContainer) {
        for (let i = 0; i < queueContainer.length; i) {
            queueContainer.shift().abort();
        }
    },

    // Clear our queue(s).
    flushQueue(container) {
        if (container) {
            this.abortRequests(this.queue[container]);
        } else {
            for (let type in this.queue) {
                if (this.queue.hasOwnProperty(type)) {
                    this.abortRequests(this.queue[type]);
                }
            }
        }
    },

    // Check if we have any requests in the queue.
    hasPendingRequests(container) {
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

    // Figure out which queue to push onto based on request method.
    pushOntoQueue(xhr) {
        let container = this.getContainer(xhr);
        this.queue[container].push(xhr);
    },

    // Find a request and remove it from the queue
    removeFromQueue(xhr){
        let container = this.getContainer(xhr);
        let index = _.indexOf(this.queue[container], xhr);
        if (index !== -1) this.queue[container].splice(index, 1);
    },

    // Figure out which queue container to put request in
    getContainer(xhr){
        return xhr.method === 'GET' ? 'fetch' : 'update';
    },

    // Add a listener to warn users their data might not be saved.
    addUnloadEventListener(){
        window.addEventListener("beforeunload", (e) => {
            let confirmationMessage = "\o/";
            if(this.hasPendingRequests('update')) {
                (e || window.event).returnValue = confirmationMessage; //Gecko + IE
                return confirmationMessage;                            //Webkit, Safari, Chrome
            }
        });
    }
};