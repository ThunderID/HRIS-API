<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width"/>
		<link rel="stylesheet" href='https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700'>
		<style type="text/css">
			body {
				font-family: 'Roboto', sans-serif;
				font-size: 14px;
				line-height: 30px;
				font-weight:300;
			}
			.title-circle {
				width: 55px;
			}
			.title-circle:after {
				content: "";
				display: block;
				width: 100%;
				height: 0;
				padding-bottom: 100%;
				background: #999;
				border-radius: 50%;
			}
			.title-circle div {
				font-weight: 100;
				font-size: 13px;
				float: left;
				width: 100%;
				padding-top: 50%;
				line-height: 1em;
				margin-top: -0.4em;
				text-align: center;
				color: white;
			}
			.title-circle.active:after {
				background: #000;
			}

			a:link, a:visited { color:#555;}
			a.btn:link, a.btn:link { 
				text-decoration:none; 
				letter-spacing:2px; 
				padding-top:15px; 
				padding-bottom:15px; 
				padding-right:15%; 
				padding-left:15%; 
				color:black !important;
				border:1px solid black;
			}

			a.btn:hover { 
				background:#eee;
			}

		</style>
	</head>
	<body>
		<div class="row" style="margin:50px;">
			@yield('content')
			<div class="clearfix">&nbsp;</div>
			<div class="clearfix">&nbsp;</div>
		</div>
	</body>
</html>
