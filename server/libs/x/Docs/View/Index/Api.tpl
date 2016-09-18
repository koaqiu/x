<!DOCTYPE html>
<html lang="cn">
<head>
    <!-- Required meta tags always come first -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>API 文档 -- {$title}</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/4.0.0-alpha.3/css/bootstrap.min.css" crossorigin="anonymous">
    <link href="//cdn.bootcss.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">
    <style type="text/css">
        html,body{
            font-size: 12pt;
        }
        nav.navbar{
            border-radius: 0;
        }
        nav.breadcrumb{
            margin-top: 1rem;
        }
        footer .copyrights{
            font-size: small;
            text-align: center;
            margin: 1.5rem 0 0 0;
            border-top: 3px solid #222;
            padding: .5rem 0;
        }
        .fa.fa-hidden {
            visibility: hidden;
        }
        table.tg { }
        table.tg th.nowrap, table.tg td.nowrap {
            white-space: nowrap;
        }
        table.tg tr > th {
            background: #dedede;
            text-shadow: #FFF 1px 1px 1px;
            color: #333;
            border-bottom: #9d9d9d 1px solid !important;
            border-top: #eee 1px solid !important;
            border-right: #9d9d9d 1px solid !important;
        }
        table.tg tr > td:first-child {
            background: #ddd;
            border-right: #ABABAB 1px solid !important;
            border-bottom: #DDDDDD 1px solid !important;
            color: #777;
            text-shadow: #FFF 1px 1px 1px;
        }

        .list-group .table {
            margin-bottom: 0;
        }

        .list-group .list-group-item.heading {
            background-color: #eceeef;
            font-weight: bold;
            border-top: 3px solid #169e16;
        }
        .list-group .table td,.list-group .table th{
            min-width: 6rem;
            padding: 5px;
        }
        .list-group .table td.num,.list-group .table th.num{
            max-width: 1.3rem;text-align: center;
        }
        .list-group .table tbody td{
            vertical-align: middle;
        }
        .list-group .table tbody tr:last-child td {
            border-bottom: none;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-dark bg-inverse">
    <div class="container">
        <a class="navbar-brand" href="#">{$title}</a>
        <ul class="nav navbar-nav">
            <li class="nav-item"><a class="nav-link" href="/Docs">框架</a></li>
            <li class="nav-item active"><a class="nav-link" href="javascript:;">API</a></li>
        </ul>
    </div>
</nav>
<div class="container">
    <!-- Content here -->

    <nav class="breadcrumb">
        <a class="breadcrumb-item" href="/Docs">文档中心</a>
        <a class="breadcrumb-item" href="/Docs/Api">API说明</a>
        <span class="breadcrumb-item active">Bootstrap</span>
    </nav>

    <div class="row">
        <div class="col-sm-4">
            <div class="list-group">
                <a href="#" class="list-group-item disabled">
                    API 清单
                </a>
                {foreach $apiList as $item}
                    <a href="#" data-api="{$item["name"]}" class="list-group-item api-item">
                        <h4>{$item["name"]}</h4>
                        <p>{$item["note"]}</p>
                    </a>
                {/foreach}
            </div>
        </div>
        <div class="col-sm-8">
            <div class="list-group">
                <div class="list-group-item heading" id="api-name">-</div>
                <div class="list-group-item" id="api-note">-</div>
                <div class="list-group-item">包：<span id="api-package"></span></div>
                <div class="list-group-item heading">请求参数</div>
                <table class="table table-bordered">
                    <thead class="thead-inverse">
                    <tr>
                        <th>名称</th>
                        <th>类型</th>
                        <th>是否可选</th>
                        <th>默认值</th>
                        <th>说明</th>
                    </tr>
                    </thead>
                    <tbody id="request-list">
                    <tr><td colspan="5">无</td></tr>
                    </tbody>
                </table>
                <div class="list-group-item heading">返回值</div>
                <table class="table table-bordered tg">
                    <thead class="thead-inverse">
                    <tr>
                        <th class="num"></th>
                        <th class="nowrap">名称</th>
                        <th>类型</th>
                        <th>默认值</th>
                        <th>说明</th>
                    </tr>
                    </thead>
                    <tbody id="result-list"><tr><td class="num"></td><td colspan="4">无</td></tr></tbody>
                </table>
                <div class="list-group-item heading">错误代码</div>
                <table class="table table-bordered">
                    <thead class="thead-inverse">
                    <tr>
                        <th>代码（Code）</th>
                        <th>说明</th>
                    </tr>
                    </thead>
                    <tbody id="error-code-list">
                    <tr><td colspan="2">无</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<footer class="bd-footer">
    <div class="container">
        <div class="copyrights">2016&copy;x Frame</div>
    </div>
</footer>
<!-- jQuery first, then Tether, then Bootstrap JS. -->
<script src="//cdn.bootcss.com/jquery/3.0.0/jquery.min.js"
        crossorigin="anonymous"></script>
<script src="//cdn.bootcss.com/tether/1.2.0/js/tether.min.js"
        crossorigin="anonymous"></script>
<script src="//cdn.bootcss.com/bootstrap/4.0.0-alpha.3/js/bootstrap.min.js" crossorigin="anonymous"></script>

<script type="text/javascript">
    (function (window) {
        window.api = new (function () {
            this.url = '/Api/';

            this.request = function (api, data) {
                var $this = this;
                return new Promise(function (resolve, reject) {
                    var r = new XMLHttpRequest();
                    r.onreadystatechange = function () {
                        if (this.readyState == 4) {
                            if (this.status == 200) {
                                //console.log(this.responseText, this.responseType);
                                var obj = JSON.parse(this.responseText);
                                resolve(obj);
                            } else {
                                reject(this.status, this.statusText)
                            }
                        }
                    };
                    r.open('POST', $this.url + api);
                    r.send(JSON.stringify(data));
                });
            };
            this.getApiDtd = function (api) {
                return this.request('Core.Docs', {
                    "api": api
                }).then(function(data){
                    if(data.code == 0){
                        return data.response_data;
                    }
                    return Promise.reject(data);
                });
            }
        })
        var line = 1;
        function showRequest(data) {
            var tbody = $('tbody#request-list').empty();
            if(data == null || data.properties == null || data.properties.length < 1){
                tbody.append('<tr><td colspan="5">无</td></tr>');
                return;
            }

            for(var i=0;i<data.properties.length ;i++){
                var p = data.properties[i];
                var tr = $('<tr></tr>');
                tr.append("<td>"+p['name']+"</td>");
                tr.append("<td><code>"+p['type']+"</code></td>");
                if(p['optional']) {
                    tr.append('<td><span class="tag tag-default">可选</span></td>');
                }else {
                    tr.append('<td><span class="tag tag-primary">必填</span></td>');
                }
                if(p['defaultValue'] != undefined) {
                    tr.append("<td>" + p['defaultValue'] + "</td>");
                }else{
                    tr.append('<td>-</td>');
                }
                tr.append("<td>"+p['note']+"</td>");
                tbody.append(tr);
            }
        }
        function showResultLine(tbody, p, level) {
            var tr = $('<tr></tr>');
            var isFolder = !(!p.properties);
            tr.append('<td class="num">'+line+'</td>');
            line++;
            var tdFirst = $('<td class="nowrap"></td>');
            for(var i = 0; i < level; i++) {
                tdFirst.append('<i class="fa fa-hidden fa-folder-open"></i>');
            }
            if(isFolder) {
                tdFirst.append('<a href="javascript:;"><i class="fa fa-caret-down fcaret"></i> <i class="fa fa-folder-open-o icon"></i></a>');
                tdFirst.append(" "+p['name']);
            }else {
                tdFirst.append('<i class="fa fa-hidden fa-folder-open"></i>');
                tdFirst.append("<i class='fa fa-file-o icon'></i> " + p['name'] + "");
            }
            tr.append(tdFirst);
            tr.append("<td><code>"+p['type']+"</code></td>");
            if(p['defaultValue']) {
                tr.append("<td>" + p['defaultValue'] + "</td>");
            }else{
                tr.append('<td>-</td>');
            }
            tr.append("<td>"+p['note']+"</td>");
            tbody.append(tr);
            if(isFolder){
                for(var i = 0; i < p.properties.length ;i++){
                    var item = p.properties[i];
                    showResultLine(tbody, item, level + 1);
                }
            }
        }

        function showResult(data) {
            line = 1;
            var tbody = $('tbody#result-list').empty();
            if(data == null || data.properties == null || data.properties.length < 1){
                tbody.append('<tr><td class="num"></td><td colspan="4">无</td></tr>');
                return;
            }
            for(var i = 0;i<data.properties.length; i++){
                var p = data.properties[i];
                showResultLine(tbody, p, 0);
            }
        }
        function showErrorCode(list) {
            var tbody = $('tbody#error-code-list').empty();
            if(list == null){
                tbody.append('<tr><td>无</td><td></td></tr>');
                return;
            }
            for (var k in list){
                var tr = $('<tr></tr>')
                tr.append("<td>"+list[k]+"</td>");
                tr.append("<td>"+k+"</td>");
                tbody.append(tr);
            }
        }
        function loadAndShowApi(apiName) {
            api.getApiDtd(apiName)
                    .then(function(data){
                        $('#api-name').text(data['name']);
                        $('#api-note').text(data['note']);
                        $('#api-package').text(data['package']);
                        showRequest(data['request']);
                        showResult(data['result']);
                        showErrorCode(data['errorCode']);
                    });
        }
        $('a.api-item').click(function(e){
            e.preventDefault();
            var link = $(this);
            link.parent().find('a.api-item').removeClass('active');
            link.addClass('active');
            var apiName = link.data('api');
            loadAndShowApi(apiName);
            return false;
        });
        $('a.api-item').eq(0).click();
        //loadAndShowApi('Core.Docs');
    })(window);
</script>
</body>
</html>