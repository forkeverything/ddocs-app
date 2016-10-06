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
        if(container) {
            this.abortRequests(this.queue[container]);
        } else {
            for(let type in this.queue) {
                if(this.queue.hasOwnProperty(type)) {
                    this.abortRequests(this.queue[type]);
                }
            }
        }
    },

    // Check if we have any requests in the queue.
    hasPendingRequests() {
        let hasRequests = false;
        for(let type in this.queue) {
            if(this.queue.hasOwnProperty(type)) {
                if(this.queue[type].length > 0) {
                    hasRequests = true;
                    break;
                }
            }
        }
        return hasRequests;
    },

    // Figure out which queue to push onto based on request method.
    pushOntoQueue(xhr) {
        if(xhr.method === 'GET') {
            this.queue.fetch.push(xhr);
        } else {
            this.queue.update.push(xhr);
        }
    }
};