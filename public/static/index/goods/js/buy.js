
net.post("/goods/buy",
    {'pid': 1},
    function (result) {

        layui.use(['jquery','layer'],function () {
            var $=layui.jquery;

            let item = '';
            result.forEach(v=>{
                item += view.createPayItem(v.title,
                    v.img,v.id,v.title);
            });
            console.log(item)
            view.appendHtml('goodsList', item);

        });
    });