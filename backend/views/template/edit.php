<!-- 爱收集资源网-->
<html>
<head>
    <meta charset="utf-8">
    <title>HTML5网页在线代码编辑器</title>

    <link rel="stylesheet" href="/codemirror/lib/codemirror.css">
    <link rel="stylesheet" href="/codemirror/theme/monokai.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #222;
        }

        /* Codemirror */
        .Codemirror {
            width: 100%;
            height: 100%;
            font-size: 20px;
        }

    </style>

</head>
<body>

<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
<script src="/codemirror/lib/codemirror.js"></script>
<script src="/codemirror/addon/edit/closetag.js"></script>
<script src="/codemirror/mode/xml/xml.js"></script>
<script src="/codemirror/mode/javascript/javascript.js"></script>
<script src="/codemirror/mode/css/css.js"></script>
<script src="/codemirror/mode/htmlmixed/htmlmixed.js"></script>
<script src="/code-blast.js"></script>

<script>
    $.ajax({
        url: "/index.php/template/content?id=<?= $id ?>",
        type: "get",
        success: function (data) {
            // console.log(data);
            window.cm = CodeMirror(document.body, {
                lineNumbers: true,
                mode: "htmlmixed",
                theme: 'monokai',
                lineWrapping: true,
                autofocus: true,
                tabSize: 2,
                value: data,
                autoCloseTags: true,
                blastCode: {effect: 2},
            });

        }
    });


    $(document).keydown(function (e) {
        // ctrl + s
        if (e.ctrlKey == true && e.keyCode == 83) {
            console.log('ctrl+s');
            $.ajax({
                url: "/index.php/template/save-content",
                type: "post",
                data: {content: window.cm.getValue(), id: <?= $id ?>},
                success: function (data) {
                    window.location.href= "/index.php/template/update?id=<?= $id ?>&update=1";
                }
            });
            return false; // 截取返回false就不会保存网页了
        }
    });
</script>

</body>
</html>