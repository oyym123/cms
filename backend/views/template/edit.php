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

<script src="/codemirror/lib/codemirror.js"></script>
<script src="/codemirror/addon/edit/closetag.js"></script>
<script src="/codemirror/mode/xml/xml.js"></script>
<script src="/codemirror/mode/javascript/javascript.js"></script>
<script src="/codemirror/mode/css/css.js"></script>
<script src="/codemirror/mode/htmlmixed/htmlmixed.js"></script>
<script src="/code-blast.js"></script>

<script>
    window.cm = CodeMirror(document.body, {
        lineNumbers: true,
        mode:  "htmlmixed",
        theme: 'monokai',
        lineWrapping: true,
        autofocus: true,
        tabSize: 2,
        value: "",
        autoCloseTags: true,
        blastCode: { effect: 2 },
    });
</script>

</body>
</html>
