$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'Authorization': 'Bearer ' + localStorage.getItem('token')
    }
});
Vue.directive('modal', {
    twoWay: true,
    update: function () {
        var self = this;
        
        $(this.el).click(function () {
            self.set(false);
        });

        $(this.el).children().click(function (e) {
            e.stopPropagation();
        });
    }
});
//# sourceMappingURL=dependencies.js.map
