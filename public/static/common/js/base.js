var view= {
    appendHtml:function (id,html) {
        layui.use(['jquery','layer'],function () {
            var $=layui.jquery
            $('#'+id).append(html);
        });

    },
    createItem:function (title,img,id,description,price,clazzTitle,unit) {
        if (img){

            return  '<li class="layui-col-xs6 layui-col-sm6 layui-col-md4"  ' +
                'style="background: white;padding:10px;margin: 10px auto;border:  1px solid #F2F2F2;text-align:center">' +

                ' <a><div class="goods_item_bg" > <img style="width:100%;height: 100% " src="'+img+'"/>   </div></a>' +
                '        <br>' +'<div style="text-align: center;margin-top:10px"><span style="color: red">'+'</span></div>'+
                '        <div class="center-align" style="text-align: center;margin-top: 10px">'+
                '<span class="layui-btn layui-bg-red " style="margin-left: 10px">' +
                '<a style="color: white" class="layui-btn-danger" href="/goods/detail/id/'+id+'">' +
                '立即购买</a> </span></div>' +

                '    </li>'

        }else{

            return  '<li class="layui-col-xs6 layui-col-sm6 layui-col-md4"  ' +
                'style="background: white;padding:10px;margin: 10px auto;border:  1px solid #F2F2F2;text-align:center">' +

                ' <a><div class="goods_item_bg" ><span>'+title+'</span><span>'+clazzTitle+'</span><span class="prePrice">'+price+'/'+unit+'</span></div></a>' +
                '        <br>' +'<div style="text-align: center;margin-top:10px"><span style="color: red">'+'</span></div>'+
                '        <div class="center-align" style="text-align: center;margin-top: 10px">'+
                '<span class="layui-btn layui-bg-red " style="margin-left: 10px">' +
                '<a style="color: white" class="layui-btn-danger" href="/goods/detail/id/'+id+'">' +
                '立即购买</a> </span></div>' +

                '    </li>'

        }





    },
    createImg:function(title,id,description,price,clazzTitle,unit) {

        return  '<li class="layui-col-xs8 layui-col-sm8 layui-col-md8"  ' +
            'style="background: white;padding:10px;margin: 10px auto;border:  1px solid #F2F2F2;text-align:center">' +

            ' <a><div class="goods_item_bg" ><span>'+title+'</span><span>'+clazzTitle+'</span><span class="prePrice">'+price+'/'+unit+'</span></div></a>' +
            '        <br>' +'<div style="text-align: center;margin-top:10px"><span style="color: red">'+'</span></div>'+
            '        <div class="center-align" style="text-align: center;margin-top: 10px"></div></li>';





    },

    createPayItem:function (title,img,id,description) {
        return '<div class="layui-col-xs6 layui-col-sm6 layui-col-md3  " style="background: white;padding: 3px ; border: white solid 1px">' +
            '      <a   onclick="view.pay('+id+')" >' +
            ' <img src="' + img + '" style="width: 80%;margin-left: 10%"></a>' +
            '        <br>' +
            '        <div class="center-align " style="margin-top: 16px;text-align: center;font-size: 26px;">' + description + '</div>' +
            '    </div>'


    },
    createMiniItem:function (title,img,id,link) {


        return '  <div style="background:#ffffff;margin:5px;text-align:center;color: white"' +
            '                 class="layui-icon-image" >' +
            '                <a href="'+link+'" style="text-align: center;font-size: 22px"> ' +
            '<img   src="'+img+'" link="'+img+'" style="width: 40px;height: 40px"><br>'+title+'</a> ' +
            '</div>';


    },


    createlinkItem(title, url, id, description) {
        return '  <span> <a class="" style="size: 18px;color: black;margin: 10px" href="'+url+'">'+title+'</a></span>' ;
    }
    ,
    pay(channelId){
        console.log(channelId);

        layui.use(['jquery','layer'],function () {
            var $=layui.jquery;

            var sign=$('#sign').val();
            var money=$('#money').val();
            var skuId=$('#skuId').val();

            net.post('',{'pid':channelId,'sign':sign},function (ret) {

                if (ret.code==1){
                    console.log(ret.url)
                }
            }) ;


        });
        // href="/pay/goods/sku/channelId' + id + '"
    }
};

var pay={
    buy:function (skuId,) {
        var href="{:url('/goods/buy',['id'=>])}";

    }
}