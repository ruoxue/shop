console.log(document.getElementById('goods_id').value);
net.post("/goods/clazz",
    {'pid': document.getElementById('goods_id').value},
    function (result) {

    layui.use(['jquery','layer'],function () {
        var $=layui.jquery;

        let item = '';
        result.forEach(v=>{
            item += view.createItem(v.title,
                v.thumb,v.id,v.description,v.price,v.clazzTitle,v.unit);
        });
        view.appendHtml('goodsList', item);

    });
});