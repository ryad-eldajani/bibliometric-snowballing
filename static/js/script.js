var sqlDateTimeToJs = function(datetime) {
    // Split timestamp into [ Y, M, D, h, m, s ]
    var timeParts = datetime.split(/[- :]/);

    return timeParts[2] + '.' + timeParts[1] + '.' + timeParts[0];
};
