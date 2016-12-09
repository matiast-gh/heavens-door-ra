	<div class="row">
		<div class="col-sm-5">
		<h3 style="position:relative; vertical-align:middle; margin-bottom:20px; padding-bottom:5px; border-bottom:1px solid #ccc;"><span class="glyphicon glyphicon-tasks" style="vertical-align:middle; margin-top:-0.12em;" aria-hidden="true"></span> Status Monitor<span id="monitor-loading" class="glyphicon glyphicon-refresh glyphicon-refresh-animate" style="position:absolute; top:9px; right:9px;"></span></h3>
		<p id="monitor"></p>
		</div>
		<div class="col-sm-7">
			<h3 style="position:relative; vertical-align:middle; margin-bottom:20px; padding-bottom:5px; border-bottom:1px solid #ccc;"><span class="glyphicon glyphicon-edit" style="vertical-align:middle; margin-top:-0.12em;" aria-hidden="true"></span> HDRA Configuration<span id="monitor-configuration" class="glyphicon glyphicon-refresh glyphicon-refresh-animate" style="position:absolute; top:9px; right:9px; display:none;"></span></h3>
			<form method="post">
				<input type="hidden" name="action" id="action" value="configuration">
				{msg}
				<div class="form-group"><select class="form-control" name="router" id="router">{routers}</select></div>
				<div class="form-group"><div class="input-group"><span class="input-group-addon" id="sizing-addon1"><span class="glyphicon glyphicon-option-horizontal" aria-hidden="true"></span></span><input type="text" name="router-ip" id="router-ip" maxlength="15" class="form-control" placeholder="Router IP Address" title="Router IP Address" aria-describedby="sizing-addon1" value="{routerip}"><div class="input-group-btn"><input class="btn btn-primary" id="btn-test-login" type="button" value="Test Login"></div></div></div>
				<div class="form-group"><div class="input-group"><span class="input-group-addon" id="sizing-addon2"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></span><input type="text" name="user" id="user" class="form-control" placeholder="Router Usename" title="Router Usename" aria-describedby="sizing-addon2" value="{user}"></div></div>
				<div class="form-group"><div class="input-group"><span class="input-group-addon" id="sizing-addon3"><span class="glyphicon glyphicon-asterisk" aria-hidden="true"></span></span><input type="password" name="pass" id="pass" class="form-control" placeholder="Router Password" title="Router Password" aria-describedby="sizing-addon3" value="{pass}"></div></div>
				<div class="form-group"><div class="input-group"><span class="input-group-addon" id="sizing-addon4"><span class="glyphicon glyphicon-option-horizontal" aria-hidden="true"></span></span><input type="text" name="remote-ip" id="remote-ip" maxlength="15" class="form-control" placeholder="Local IP (for Remote Access)" title="Local IP (for Remote Access)" aria-describedby="sizing-addon4" value="{remoteip}"></div></div>
				<div class="form-group"><div class="input-group"><span class="input-group-addon" id="sizing-addon5"><span class="glyphicon glyphicon-transfer" aria-hidden="true"></span></span><input type="text" name="port" id="port" maxlength="5" class="form-control" placeholder="Local Port (for Remote Access)" title="Local Port (for Remote Access)" aria-describedby="sizing-addon5" value="{port}"></div></div>
				<div class="form-group"><div class="input-group"><span class="input-group-addon" id="sizing-addon6"><span class="glyphicon glyphicon-off" aria-hidden="true"></span></span><input type="text" name="mac" id="mac" maxlength="17" class="form-control" placeholder="MAC Address [00:00:00:00:00:00] (for Wake On Lan)" title="MAC Address [00:00:00:00:00:00] (for Wake On Lan)" aria-describedby="sizing-addon6" value="{mac}"><div class="input-group-btn"><input class="btn btn-primary" id="btn-get-mac" type="button" value="Get MAC from Local IP"></div></div></div>
				<div class="form-group"><button id="btn-submit" type="submit" class="btn btn-primary pull-right">Save</button></div>
			</form>
		</div>
	</div>
	<script>
	function monitor() {
		$.ajax({
			url: "monitor.php",
			method: 'POST',
			data: { action : 'monitor' },
			cache: false,
			beforeSend: function() {
				$("#monitor_loading").show();
			},
			success: function(html) {
				$("#monitor").html(html);
				$("#monitor-loading").fadeOut();
			}
		});
	}
	$("#btn-test-login").click(function() {
		$("#router-ip").parent(".input-group").removeClass("has-success");
		$("#user").parent(".input-group").removeClass("has-success");
		$("#pass").parent(".input-group").removeClass("has-success");
		$("#router-ip").parent(".input-group").removeClass("has-warning");
		$("#user").parent(".input-group").removeClass("has-warning");
		$("#pass").parent(".input-group").removeClass("has-warning");
		process = true;
		$("#router").parent(".form-group").removeClass("has-error");
		if ($("#router").val() === null) {
			process = false;
			$("#router").parent(".form-group").addClass("has-error");
		}
		$("#router-ip").parent(".input-group").removeClass("has-error");
		if ({regexpip}.test($.trim($("#router-ip").val())) == false) {
			process = false;
			$("#router-ip").parent(".input-group").addClass("has-error");
		}
		$("#user").parent(".input-group").removeClass("has-error");
		if ($.trim($("#user").val()) == "") {
			process = false;
			$("#user").parent(".input-group").addClass("has-error");
		}
		if (process) {
			$.ajax({
				url: "monitor.php",
				method: 'POST',
				data: { action: 'test-login', router: $("#router").val(), routerip: $("#router-ip").val(), user: $("#user").val(), pass: $("#pass").val() },
				cache: false,
				beforeSend: function() {
					$("#monitor-configuration").show();
				},
				success: function(html) {
					if (html == '1') {
						$("#router-ip").parent(".input-group").addClass("has-success");
						$("#user").parent(".input-group").addClass("has-success");
						$("#pass").parent(".input-group").addClass("has-success");
					}
					else {
						$("#router-ip").parent(".input-group").addClass("has-warning");
						$("#user").parent(".input-group").addClass("has-warning");
						$("#pass").parent(".input-group").addClass("has-warning");
					}
					$("#monitor-configuration").fadeOut();
				}
			});
		}
	});
	$("#btn-get-mac").click(function() {
		$("#mac").parent(".input-group").removeClass("has-success");
		$("#mac").parent(".input-group").removeClass("has-warning");
		process = true;
		$("#remote-ip").parent(".input-group").removeClass("has-error");
		if ({regexpip}.test($.trim($("#remote-ip").val())) == false) {
			process = false;
			$("#remote-ip").parent(".input-group").addClass("has-error");
		}
		if (process) {
			$.ajax({
				url: "monitor.php",
				method: 'POST',
				data: { action: 'get-mac', remoteip: $("#remote-ip").val() },
				cache: false,
				beforeSend: function() {
					$("#monitor-configuration").show();
				},
				success: function(html) {
					if (html != '') {
						$("#mac").val(html);
						$("#mac").parent(".input-group").addClass("has-success");
					}
					else {
						$("#mac").parent(".input-group").addClass("has-warning");
					}
					$("#monitor-configuration").fadeOut();
				}
			});
		}
	});
	$("#btn-submit").click(function() {
		process = true;
		$("#router").parent(".form-group").removeClass("has-error");
		if ($("#router").val() === null) {
			process = false;
			$("#router").parent(".form-group").addClass("has-error");
		}
		$("#router-ip").parent(".input-group").removeClass("has-error");
		if ({regexpip}.test($.trim($("#router-ip").val())) == false) {
			process = false;
			$("#router-ip").parent(".input-group").addClass("has-error");
		}
		$("#user").parent(".input-group").removeClass("has-error");
		if ($.trim($("#user").val()) == "") {
			process = false;
			$("#user").parent(".input-group").addClass("has-error");
		}
		$("#remote-ip").parent(".input-group").removeClass("has-error");
		if ($.trim($("#remote-ip").val()) != "") {
			if ({regexpip}.test($.trim($("#remote-ip").val())) == false) {
				process = false;
				$("#remote-ip").parent(".input-group").addClass("has-error");
			}
		}
		$("#port").parent(".input-group").removeClass("has-error");
		if ($.trim($("#port").val()) != "") {
			if ({regexpport}.test($.trim($("#port").val())) == false) {
				process = false;
				$("#port").parent(".input-group").addClass("has-error");
			}
			else {
				if (($.trim($("#port").val()) < 1) || ($.trim($("#port").val()) > 65535)) {
					process = false;
					$("#port").parent(".input-group").addClass("has-error");
				}
			}
		}
		$("#mac").parent(".input-group").removeClass("has-error");
		if ($.trim($("#mac").val()) != "") {
			if ({regexpmac}.test($.trim($("#mac").val())) == false) {
				process = false;
				$("#mac").parent(".input-group").addClass("has-error");
			}
		}
		if (process) {
			$("#btn-submit").submit();
		}
		else {
			return false;
		}
	});
	$("#sizing-addon3").hover(function() {
		$("#pass").attr("type", "text");
	},
	function() {
		$("#pass").attr("type", "password");
	});
	$(document).ready(function() {
		$("#router").val('{router}');
		setTimeout(function() { $("#msg").fadeOut(); }, 5000);
		monitor();
		setInterval(function() { monitor(); }, 8000);
	});
	</script>
