Vue.filter('date', function (value) {
    if (!value || value == '') return;
    if (value !== '0000-00-00 00:00:00') {
        return moment(value, "YYYY-MM-DD HH:mm:ss").format('DD/MM/YYYY');
    }
    return value;
});

Vue.filter('diffHuman', function (value) {
    if (!value || value == '') return;
    if (value !== '0000-00-00 00:00:00') {
        return moment(value, "YYYY-MM-DD HH:mm:ss").fromNow();
    }
    return value;
});

Vue.filter('dateTime', function (value) {
    if (!value || value == '') return;
    if (value !== '0000-00-00 00:00:00') {
        return moment(value, "YYYY-MM-DD HH:mm:ss").format('DD MMM YYYY, h:mm a');
    }
    return value;
});

Vue.filter('easyDate', function (value) {
    if (!value || value == '') return;
    if (value !== '0000-00-00 00:00:00') {
        return moment(value, "YYYY-MM-DD HH:mm:ss").format('DD MMM YYYY');
    }
    return value;
});

Vue.filter('smartDate', function (value) {
    if (!value || value == '') return;
    if (value !== '0000-00-00 00:00:00') {
        let today = moment();
        let tomorrow = today.clone().add(1, 'days').startOf('day');
        let yesterday = today.clone().subtract(1, 'days').startOf('day');
        let nextSunday = today.clone().day(7).startOf('day');
        let date = moment(value, ["DD/MM/YYYY", "YYYY-MM-DD HH:mm:ss"]);


        // TODO ::: Find a locale friendly way of doing this
        if(date.isSame(today, 'd')) return 'Today';
        if(date.isSame(yesterday, 'd')) return 'Yesterday';
        if(date.isSame(tomorrow, 'd')) return 'Tomorrow';
        // Within the week -> what day: Wednesday, Thursday, Friday ...
        if(today < date && date < nextSunday) return date.format('dddd');
        // Within the year -> month and date
        if(date.year() === today.year()) return date.format('MMM D');
        // Not this year -> month date, year
        return date.format('MMM D, YYYY');
    }
    return value;
});

