net.post('/goods/buy', {'pid': 1}, function (result) {
    let item = '';
    result.forEach(v=>{
        item += view.createMiniItem(v.title,
            v.icon,v.id,v.link);
    });

    view.appendHtml('leftAd', item);



    loadtip();

});

function  loadtip() {

    layui.use(['jquery','layer'],function () {
        var $=layui.jquery;

        $(".layui-icon-image").hover(function() {

            layer.closeAll();
            var link= this.getElementsByTagName('img')[0].getAttribute('link');

            layer.tips('<img src="'+link+'" alt="" style="width:60px;height:60px">', '#leftAd', {
                tips: [1,'#f2f2f2'],
                time: 4000,
                tipsMore: true
            });
        },function(){
            layer.closeAll();
        });



    });

}