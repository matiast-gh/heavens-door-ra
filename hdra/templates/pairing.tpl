	<div class="row">
		<div class="col-sm-4">
		</div>
		<div class="col-sm-4">
			<form method="post">
				<input type="hidden" name="action" id="action" value="pairing">
				{msg}
				<div class="form-group"><div class="input-group input-group-lg"><span class="input-group-addon" id="sizing-addon1"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span></span><input type="text" name="pairing-code" id="pairing-code" class="form-control" placeholder="Latch Pairing Code" aria-describedby="sizing-addon1"></div></div>
				<div class="form-group"><button id="btn-submit" type="submit" class="btn btn-primary pull-right">Submit</button></div>
			</form>
		</div>
		<div class="col-sm-4">
		</div>
	</div>
	<script>
	$("#btn-submit").click(function() {
		process = true;
		$("#pairing-code").parent(".input-group").removeClass("has-error");
		$("#pairing-code").val($.trim($("#pairing-code").val()));
		if ($("#pairing-code").val().length < 6) {
			process = false;
			$("#pairing-code").parent(".input-group").addClass("has-error");
		}
		if (process) {
			$("#btn-submit").submit();
		}
		else {
			return false;
		}
	});
	$(document).ready(function() {
		$("#pairing-code").focus();
	});
	</script>
