var timestampToDate = function(timestamp) {
    var date = new Date(timestamp);

    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var day = date.getDate();

    month = (month < 10 ? '0' : '') + month;
    day = (day < 10 ? '0' : '') + day;

    return day + '.' + month + '.' + year;
};
