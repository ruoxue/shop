var address = {
    'provice': function (province, city, areaId) {
        net.post('/user/getArea', {'pid': 0}, function (res) {
            layui.use(['jquery'], function () {
                var $ = layui.jquery;
                var html = '';

                res.forEach(item => {

                    if (province == item.id) {

                        html += '  <option  selected  value="' + item.id + '">' + item.shortname + '</option>'
                    } else {

                        html += '  <option    value="' + item.id + '">' + item.shortname + '</option>'
                    }
                });
                $('.province').empty();
                $('.province').html(html);
                if (res.length >= 1) {
                    if (city == 0) {
                        address.city(province, res[0].id, areaId);
                    } else {
                        address.city(province, city, areaId);
                    }
                }

            })


        })
    },
    city: function (province, city, areaId) {
        net.post('/user/getArea', {'pid': province}, function (res) {
            layui.use(['jquery'], function () {
                var $ = layui.jquery;
                var html = '';

                res.forEach(item => {

                    if (item.id == city) {
                        html += '  <option selected value="' + item.id + '">' + item.shortname + '</option>'
                    } else {
                        html += '  <option value="' + item.id + '">' + item.shortname + '</option>'
                    }

                });

                $('.city').empty();
                $('.city').html(html);


                if (res.length >= 1) {

                    if (areaId == 0) {
                        address.area(province, res[0].id, areaId);
                    } else {
                        address.area(province, city, areaId);
                    }

                }

            })
        })
    },
    area: function (province, city, areaId) {
        net.post('/user/getArea', {'pid': city}, function (res) {
            layui.use(['jquery'], function () {
                var $ = layui.jquery;
                var html = '';

                res.forEach(item => {
                    if (areaId == item.id) {
                        html += '  <option selected value="' + item.id + '">' + item.shortname + '</option>'
                    } else {
                        html += '  <option value="' + item.id + '">' + item.shortname + '</option>'
                    }

                });
                $('.area').empty();
                $('.area').html(html);

            })


        })

    },
}