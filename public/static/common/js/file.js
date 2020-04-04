/**
 *  * load 文件上传
 * 传入一个原始元素 一个图片接收元素  一个url接收元素
 * @type {{init: file.init}}
 */
var file = {

    getImgs: function (id) {
        let imgs= [];
        layui.use(['jquery', 'layer', 'upload'], function () {
            let $ = layui.jquery;
            let img = $('#' + id)[0].childNodes;

            img.forEach(v => {
                console.log(v.dataset.value)
                imgs.push(v.dataset.value)
            });

        });
        return imgs;
    },


    /**
     *
     * @param url
     * @param id
     * @param picId
     * @param urlId
     * @param notice
     */
    init: function (url, id, picId, urlId, notice) {
        layui.use(['jquery', 'layer', 'upload'], function () {
            let $ = layui.jquery;
            let upload = layui.upload;
            let uploadInt = upload.render({
                elem: id,
                multiple: true
                , url: url
                , before: function (obj) {
                    //预读本地文件示例，不支持ie8
                    obj.preview(function (index, file, result) {
                        //       $('#'+picId).append('<img class="layui-upload-img" style=" float: left" src="' + result + '">');
                    });
                },
                done: function (res) {
                    if (res.code > 0) {
                        $('#' + picId).append('<img class="layui-upload-img" style=" float: left" src="' + res.url + '"  data-value="' + res.id + '"  >');
                        $(urlId).val(res.url);
                    } else {
                        //如果上传失败
                        return layer.msg('上传失败');
                    }
                }
                , error: function () {
                    //演示失败状态，并实现重传
                    var notice = $(notice);
                    notice.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-mini demo-reload">重试</a>');
                    notice.find('.demo-reload').on('click', function () {
                        uploadInt.upload();
                    });
                }
            });


        });


    },


};
