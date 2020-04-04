var net={
    postNull:function(url,iNet){
        net.post(url,'',iNet);
    },

    post:function (url,param,iNet) {
        layui.use(['jquery','layer'],function () {
            var $=layui.jquery
            $.post(url,param,function (result) {
                //   try {
                if (result.code==1){
                    if (result.url!=null&&result.url!=''){
                        window.location.href=result.url;
                    }
                    if (result.data==null||result.data=='') {
                       // layer.msg(result.msg, {time: 1800, icon: 1});
                    }else {
                        iNet(result.data);
                    }

                }else {
                    if (result.msg==null){
                        layer.msg('error', {time: 1800, icon: 2});
                    } else {
                        layer.msg(result.msg, {time: 1800, icon: 2});
                    }
                }
                // }catch (e) {
                //     console.log(e);
                //     layer.msg('我错了', {time: 1800, icon: 5});
                // }

            });
        });
    }

};


