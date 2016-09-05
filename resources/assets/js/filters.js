Vue.filter('date', function (value) {
    if(! value || value == '') return;
        if (value !== '0000-00-00 00:00:00') {
            return moment(value, "YYYY-MM-DD HH:mm:ss").format('DD/MM/YYYY');
        }
        return value;
    });

    Vue.filter('diffHuman', function (value) {
        if(! value || value == '') return;
        if (value !== '0000-00-00 00:00:00') {
            return moment(value, "YYYY-MM-DD HH:mm:ss").fromNow();
        }
        return value;
    });

    Vue.filter('dateTime', function (value) {
        if(! value || value == '') return;
        if (value !== '0000-00-00 00:00:00') {
            return moment(value, "YYYY-MM-DD HH:mm:ss").format('DD MMM YYYY, h:mm a');
        }
        return value;
    });

    Vue.filter('easyDate', function (value) {
        if(! value || value == '') return;
        if (value !== '0000-00-00 00:00:00') {
            return moment(value, "YYYY-MM-DD HH:mm:ss").format('DD MMM YYYY');
        }
        return value;
    });

