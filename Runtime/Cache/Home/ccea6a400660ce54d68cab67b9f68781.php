<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title><?php echo ($title); ?></title>
<meta name="description" content="User login page" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<!-- basic styles -->
<link href="/zebra/Public/css/bootstrap.min.css" rel="stylesheet" />
<link href="/zebra/Public/css/bootstrap-responsive.min.css"
	rel="stylesheet" />
<link rel="stylesheet" href="/zebra/Public/css/font-awesome.min.css" />
<!--[if IE 7]>
		  <link rel="stylesheet" href="css/font-awesome-ie7.min.css" />
		<![endif]-->
<!-- page specific plugin styles -->

<!-- ace styles -->
<link rel="stylesheet" href="/zebra/Public/css/ace.min.css" />
<link rel="stylesheet" href="/zebra/Public/css/ace-responsive.min.css" />
<!--[if lt IE 9]>
		  <link rel="stylesheet" href="css/ace-ie.min.css" />
		<![endif]-->
</head>
<body class="login-layout">

	<div class="container-fluid" id="main-container">
		<div id="main-content">
			<div class="row-fluid">
				<div class="span12">

					<div class="login-container">
						<div class="row-fluid">
							<div class="center">
								<h1>
									<i class="icon-leaf green"></i> <span class="red">统计</span> <span
										class="white">后台</span>
								</h1>
								<h4 class="blue">&copy; TIME NEWS</h4>
							</div>
						</div>
						<div class="space-6"></div>
						<div class="row-fluid">
							<div class="position-relative">
								<div id="login-box" class="visible widget-box no-border">
									<div class="widget-body">
										<div class="widget-main">
											<h4 class="header lighter bigger">
												<i class="icon-coffee green"></i> 请输入
											</h4>

											<div class="space-6"></div>
											
											<form class="login-form" name="login" id="login" action="login" method="post">
												<fieldset>
													<label> <span
														class="block input-icon input-icon-right"> <input
															type="text" class="span12" placeholder="用户名" /> <i
															class="icon-user"></i>
													</span>
													</label> <label> <span
														class="block input-icon input-icon-right"> <input
															type="password" class="span12" placeholder="密码" /> <i
															class="icon-lock"></i>
													</span>
													</label>
													<div class="space"></div>
													<div class="row-fluid">
														<label class="span8"> <input type="checkbox"><span
															class="lbl">记住我</span>
														</label>
														<button onclick="login();"
															class="span4 btn btn-small btn-primary">
															<i class="icon-key"></i> 登陆
														</button>
													</div>

												</fieldset>
											</form>
										</div>
										<!--/widget-main-->


										<div class="toolbar clearfix">
											<div>
												<a href="#" onclick="show_box('forgot-box'); return false;"
													class="forgot-password-link"><i class="icon-arrow-left"></i>
													忘记密码</a>
											</div>
											<div>
												<a href="#" onclick="show_box('signup-box'); return false;"
													class="user-signup-link">注册<i class="icon-arrow-right"></i></a>
											</div>
										</div>
									</div>
									<!--/widget-body-->
								</div>
								<!--/login-box-->






								<div id="forgot-box" class="widget-box no-border">
									<div class="widget-body">
										<div class="widget-main">
											<h4 class="header red lighter bigger">
												<i class="icon-key"></i>找回密码
											</h4>

											<div class="space-6"></div>

											<p>输入接收密码邮箱</p>
											<form>
												<fieldset>
													<label> <span
														class="block input-icon input-icon-right"> <input
															type="email" class="span12" placeholder="邮箱" /> <i
															class="icon-envelope"></i>
													</span>
													</label>

													<div class="row-fluid">
														<button onclick="return false;"
															class="span5 offset7 btn btn-small btn-danger">
															<i class="icon-lightbulb"></i> 发给我
														</button>
													</div>

												</fieldset>
											</form>
										</div>
										<!--/widget-main-->

										<div class="toolbar center">
											<a href="#" onclick="show_box('login-box'); return false;"
												class="back-to-login-link">返回登陆界面<i
												class="icon-arrow-right"></i></a>
										</div>
									</div>
									<!--/widget-body-->
								</div>
								<!--/forgot-box-->



								<div id="signup-box" class="widget-box no-border">
									<div class="widget-body">
										<div class="widget-main">
											<h4 class="header green lighter bigger">
												<i class="icon-group blue"></i> 新注册
											</h4>
											<div class="space-6"></div>
											<p>请输入以下信息:</p>

											<form>
												<fieldset>
													<label> <span
														class="block input-icon input-icon-right"> <input
															type="email" class="span12" placeholder="邮箱" /> <i
															class="icon-envelope"></i>
													</span>
													</label> <label> <span
														class="block input-icon input-icon-right"> <input
															type="text" class="span12" placeholder="用户名" /> <i
															class="icon-user"></i>
													</span>
													</label> <label> <span
														class="block input-icon input-icon-right"> <input
															type="password" class="span12" placeholder="密码" /> <i
															class="icon-lock"></i>
													</span>
													</label> <label> <span
														class="block input-icon input-icon-right"> <input
															type="password" class="span12" placeholder="重复密码" /> <i
															class="icon-retweet"></i>
													</span>
													</label> <label> <input type="checkbox"><span
														class="lbl"> 我接受以下<a href="#">用户协议</a></span>
													</label>

													<div class="space-24"></div>

													<div class="row-fluid">
														<button type="reset" class="span6 btn btn-small">
															<i class="icon-refresh"></i> 重置
														</button>
														<button onclick="return false;"
															class="span6 btn btn-small btn-success">
															注册 <i class="icon-arrow-right icon-on-right"></i>
														</button>
													</div>

												</fieldset>
											</form>
										</div>

										<div class="toolbar center">
											<a href="#" onclick="show_box('login-box'); return false;"
												class="back-to-login-link"><i class="icon-arrow-left"></i>返回登陆界面</a>
										</div>
									</div>
									<!--/widget-body-->
								</div>
								<!--/signup-box-->


							</div>
							<!--/position-relative-->

						</div>
					</div>
				</div>
				<!--/span-->
			</div>
			<!--/row-->
		</div>
	</div>
	<!--/.fluid-container-->
	<!-- basic scripts -->
	<script src="/zebra/Public/1.9.1/jquery.min.js"></script>
	<script type="text/javascript">
		window.jQuery
				|| document
						.write("<script src='/zebra/Public/js/jquery-1.9.1.min.js'>\x3C/script>");
	</script>
	<!-- page specific plugin scripts -->

	<!-- inline scripts related to this page -->

	<script type="text/javascript">
		function show_box(id) {
			$('.widget-box.visible').removeClass('visible');
			$('#' + id).addClass('visible');
		}
		
		function login(){
			$('#login').submit();
		}
	</script>
</body>
</html>