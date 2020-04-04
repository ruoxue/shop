net.post('/index/index/getNav', {'pid': 0}, function (data) {
    var html = '';
    var html2 = '';
    data.forEach(v => {
        html += ' <li class="layui-nav-item menuItem" style="line-height: normal;padding:10px;margin-left: 15px">' +
            '  <a style="color:white" ' +
            'link=' + v.id +
            ' ' +
            'href="/goods/clazz?pid='


            + v.id + '">' + v.title + ' </a>' +
            '<ul class="ul" style="width: 100%;background:red;text-align: center;position: absolute;z-index: 10" id="ul'+v.id+'"></ul>'+
            '    </li>';


        html2 +=
            ' <li class="layui-nav-item  " style="background: orangered;padding: 5px">' +
            '                    <a style="color: white;font-size: larger" ' +

            'href="/goods/clazz?pid=' + v.id + '">' + v.title + '</a></li> ';
    });
    view.appendHtml('left_clazz', html2);
    view.appendHtml('nav', html);


    loadMenu();
});


function loadMenu() {
    layui.use(['jquery', 'layer'], function () {
        var $ = layui.jquery;

        $(".menuItem").hover(function () {

            var pid = this.getElementsByTagName('a')[0].getAttribute('link');
            var uid = this.getElementsByTagName('ul')[0].getAttribute('id');

            var html='';

            $('.ul').empty();

            net.post('/index/index/getNav', {'pid': pid}, function (ret) {

                ret.forEach(item=>{
                    if (item.isLink==1){
                        html+='<li style="size: 18px;font-size: 18px;padding: 6px"><a style="font-size: 16px;color: white" href="'+item.link+'">'+item.title+'</a></li>';

                    }else {
                        html+='<li style="size: 18px;font-size: 18px;padding: 6px"><a style="font-size: 16px;color: white" href="/goods/clazz?pid='+item.id+'">'+item.title+'</a></li>';

                    }


                });

                $('#'+uid).html(html);
                // view.appendHtml(uid,html);

            });


            // layer.tips('<img src="'+link+'" alt="">', '#leftAd', {
            //     tips: [1,'#f2f2f2'],
            //     time: 4000,
            //     tipsMore: true
            // });
        },function () {
            $('.ul').empty();
        });


    });


}


net.postNull('/index/index/getAd', function (data) {
    let html = '';
    data.forEach(v => {

        html += '       <div><a href="' + v.ad_link +
            '"><img src="' + v.ad_image + '"></a>' +
            '            </div>';

    });

    view.appendHtml('bannerItem', html);
    layui.use('carousel', function () {
        var carousel = layui.carousel;
        carousel.render({
            elem: '#banner'
            , width: '100%' //设置容器宽度
            , arrow: 'always' //始终显示箭头
            //, anim: 'fade' //切换动画方式
        });
    });

});

//
// net.postNull('/index/index/getHomeClass', function (data) {
//
//     let html = '';
//     data.forEach(v => {
//
//         html+='<div>';
//         html += '<div class="home-class"  >' + v.name + '</div>';
//         html += '<div   id="home'+v.id+'">'  + '</div>';
//
//
//         net.post('/index/index/getHomeGoods', {'classId': v.id}, function (result) {
//             let item = '';
//            result.forEach(v=>{
//                item += view.createItem(v.title, 'http://51qiwo.com/upload/common/1569417399.png');
//            });
//
//             view.appendHtml('home'+v.id, item);
//         });
//
//         html+=    '</div>';
//
//
//     });
//     view.appendHtml('homeClass', html);


///
//Bt-Panel-URL: http://182.61.185.49:8888/d748a393
// username: ibdmece4
// password: 9df04add
// });

net.post('/index/index/getHomeGoods', {'classId': 0}, function (result) {
    let item = '';
    result.forEach(v => {
        item += view.createItem(v.title,
            v.thumb, v.id, v.description, v.price,v.clazzTitle,v.unit);
    });
    //console.log(item)
    view.appendHtml('homeClass', item);
});



