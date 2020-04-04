layui.use(['form', 'table'], function () {
    var $ = layui.jquery;
    var form = layui.form;
    $.post('','',function (ret) {
        console.log(ret);
        form.val("form", ret.data);
    });

});