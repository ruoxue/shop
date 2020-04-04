<?php
return [
    \think\middleware\LoadLangPack::class,

    \think\middleware\SessionInit::class,
    //日志
    \app\admin\middleware\AdminLog::class,
];

