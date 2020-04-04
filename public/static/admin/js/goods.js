var goods={
    /**
     *
     */
    'createSku':function () {
        console.log(11);
        let view='';
        layui.use(['jquery'],function () {
            var $=layui.jquery;
           let i= $('#sku')[0].childElementCount;
           console.log(i);
            view+='  <div>' +
                '                                <input name="sku['+i+'][name]" placeholder="规格名称">' +
                '                                <input name="sku['+i+'][price]" placeholder="价格">' +
                '                                <input placeholder="原价">' +
                '                                <input placeholder="市场价">' +
                '                                <input placeholder="规格图片">' +
                '                                <button type="button" class="layui-btn layui-btn-primary" id="addSkuImgBtn"><i' +
                '                                        class="icon icon-upload3"></i>点击上传' +
                '                                </button>' +
                '                                <label type="button" onclick="goods.createSku()" class="layui-btn layui-btn-primary" >添加规格</label>' +
                '                            </div>';
            
            $('#sku').append(view);

        });

    },


};