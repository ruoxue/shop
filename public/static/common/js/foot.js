net.post('/index/index/getLink', {'classId': 0}, function (result) {
    let item = '';
    result.forEach(v=>{
        item += view.createlinkItem(v.name,
            v.url,v.id,v.description);
    });
    console.log(item)
    view.appendHtml('link', item);
});
