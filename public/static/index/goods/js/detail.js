net.post("/goods/detail",
    {'id': document.getElementById('goods_id').value},
    function (ret) {

    layui.use(['jquery', 'layer'], function () {
        var $ = layui.jquery;

        $('#title').html(ret.title);
        $('#content').html(ret.content);

        $('#desc').html(ret.description);

        if (!ret.thumb) {
            $('#imgTitle').html(
                view.createImg(ret.title, ret.id, ret.description, ret.price, ret.clazzTitle, ret.unit));

        } else {

            $('#imgTitle').html('<img id="imgThumb" style="width: 60%" src="' + ret.thumb + '"/>');
            img.big(ret.thumb);
        }

        $('#price').html(ret.price + '/' + ret.unit);
        $('#stock').html(ret.stock);





    });
});

function buy() {
    layui.use(['jquery', 'layer'], function () {
        var $ = layui.jquery;

        var click = document.getElementsByClassName('click');
        if (click&&click.length==1){

            var sku=click[0].getAttribute('skuId');
            window.location.href = '/index/shop/buy/skuId/' + sku + "/money/-1";
        }else {
            layer.tips('未选择规格', '.layui-btn', {
                tips: [1, '#000'],
                time: 5000,
                tipsMore: true
            });
        }


        // net.post('/index/shop/buy',{'skuId':sku,'money':-1},function (ret) {
        //
        //     console.log(ret);
        // })


    });


}




var sku = {
    init:function(){

        net.post('/goods/getSku',{'id': document.getElementById('goods_id').value},
            function (ret) {


            layui.use(['jquery'],function () {
                $=layui.jquery;
                var  html='';
                ret.forEach(item=>{
                    html+='   <li onclick="sku.click(\'sku_li'+item.id+'\')" id="sku_li'+item.id+'"   class="sku_li" skuId="'+item.id+'">\n' +
                        '                        <a href="javascript:void(0);" data-spm-anchor-id="2013.1.iteminfo.3">\n' +
                        '                            <span data-spm-anchor-id="2013.1.iteminfo.i0.2138592eadNvKh">'+item.title+'</span>\n' +
                        '                        </a>\n' +
                        '                        <i></i>\n' +
                        '                    </li>';
                });
                $('#ul_sku').empty();
                $('#ul_sku').html(html);


            });


        });

    },

    click: function (id) {



        layui.use(['jquery'], function () {

            var $ = layui.jquery;
            var isClick = $('#'+id).hasClass('click');
            $('.sku_li').removeClass('click');
            if (isClick) {
                $('#'+id).removeClass('click');
            } else {
                $('#'+id).addClass('click');
            }
        });


    }


}
sku.init();



var img={
    big:function (imgUrl) {
        layui.use(['jquery'],function () {
            $ = layui.jquery;

            var vh = $(window).height()
            var vw = $(window).width()
            var imgh = $(".wrap img").height()
            var imgw = $(".wrap img").width()
            var beginX = vw * 2 / 10
            var endX = beginX + imgw
            var beginY = (vh - imgh) / 2
            var endY = beginY + imgh
            $(".wrap").css("margin-top", 0 + "px")
            //鼠标经过
            document.addEventListener("mousemove", loupe, false);
            //触屏模式触发
            document.addEventListener("touchmove", loupe, false);
            document.addEventListener("touchstart", loupe, false);
            document.addEventListener("touchend", function (e) {
                $(".loupe").css("visibility", "hidden")
            }, false);

            function loupe(e) {
                var x, y
                if (e.type != "mousemove") {
                    x = e.touches[0].pageX
                    y = e.touches[0].pageY
                }
                //如果支持触摸事件，则屏蔽鼠标经过事件，避免影响touchstart的效果
                else if ("ontouchend" in document) {
                    return false;
                }
                //如果不支持触摸事件，则让鼠标经过事件正常触发
                else {
                    x = e.clientX
                    y = e.clientY
                }
                //判断鼠标或触摸点在图片区域，是则显示放大镜div层
                if (x > beginX && x < endX && y > beginY && y < endY) {
                    var mx = 100 - (x - beginX) / imgw * 1920 //1920为原图片宽度
                    var my = 100 - (y - beginY) / imgh * 1200 //1200为原图片高度
                    var bg = "url("+imgUrl+") " + mx + "px " + my + "px no-repeat #fff"
                    $(".loupe").css("left", x - 103 + "px").
                    css("top", 0 + "px").css('background', bg)
                    $(".loupe").css("visibility", "visible")
                } else {
                    $(".loupe").css("visibility", "hidden")
                }
            }

        });
    }

};








