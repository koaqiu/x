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
        pre{
            background: #333;
            color: #fff;
            padding: .5rem;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-dark bg-inverse">
    <div class="container">
        <a class="navbar-brand" href="#">{$title}</a>
        <ul class="nav navbar-nav">
            <li class="nav-item active"><a class="nav-link" href="/Docs">框架</a></li>
            <li class="nav-item"><a class="nav-link" href="/Docs/Api">API</a></li>
        </ul>
    </div>
</nav>
<div class="container">
    <!-- Content here -->

    <nav class="breadcrumb">
        <a class="breadcrumb-item" href="/Docs">文档中心</a>
        <span class="breadcrumb-item active">说明</span>
    </nav>

<div>
    {$mdContent}
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

</script>
</body>
</html>