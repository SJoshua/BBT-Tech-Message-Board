/* Main functions
 * Author: SJoshua
 */
function _GET() {
    var url = location.search;
    var t = new Object();
    if (url.indexOf("?") != -1) {
       var str = url.substr(1);
       strs = str.split("&");
       for(var i = 0; i < strs.length; i ++) {
           t[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
       }
    }
    return t;
}

function updatePosts() {
	var page = _GET()["page"];
	if (!page) {
		page = 1;
	}
	$.get("/api/getpost.php?page=" + page, function(data, status) {
		if (status == "success") {
			var ret = JSON.parse(data);
			if (ret.status == "ok") {
				$("#view").empty();
				$("#view").append("<h2>Page " + ret.page + "</h2><br>");
				for (var i = 0; i < ret.arr.length; i++) {
					$("#view").append("<p>#" + ret.arr[i].id + " by <b>" 
						+ ret.arr[i].author + "</b> at <i>" 
						+ ret.arr[i].timestamp + "</i></p><pre>"
						+ ret.arr[i].content + "</pre><br>");
				}
			} else {
				$("#info").text(ret.errmsg);
				$("#info").css("color", "red");
			}
		} else {
			$("#info").text("Oops, something was wrong...");
			$("#info").css("color", "red");
		}
	});
}

function updateStatus(redirect) {
	$.get("/api/status.php", function(data, status) {
		if (status == "success") {
			var ret = JSON.parse(data);
			if (ret.status == "guest") {
				$("#login").show();
				$("#register").show();
			} else {
				if (redirect) {
					$(location).attr("href", "index.html");
				}
				if (ret.admin) {
					$("#admin").show();
				}
				$("#info").text("Hello, " + ret.user_id);
				$("#logout").show();
				$("#newpost").show();
			}
		} else {
			$("#info").text("Oops, something was wrong...");
			$("#info").css("color", "red");
		}
	});
	return updatePosts();
}

function register() {
	// TODO: pre-check
	var info = {
		username: $("#username").val(),
		password: $("#password").val(),
		checkpwd: $("#checkpwd").val()
	};
	$.post("./api/register.php", info, function(data, status) {
		if (status == "success") {
			var ret = JSON.parse(data);
			if (ret.status == "ok") {
				$(location).attr("href", "index.html");
			} else {
				$("#notice").text(ret.errmsg);
			}
		} else {
			$("#info").text("Oops, something was wrong...");
			$("#info").css("color", "red");
		}
	});
}

function login() {
	// TODO: pre-check
	var info = {
		username: $("#username").val(),
		password: $("#password").val()
	};
	$.post("./api/login.php", info, function(data, status) {
		if (status == "success") {
			var ret = JSON.parse(data);
			if (ret.status == "ok") {
				$(location).attr("href", "index.html");
			} else {
				$("#notice").text(ret.errmsg);
			}
		} else {
			$("#info").text("Oops, something was wrong...");
			$("#info").css("color", "red");
		}
	});
}

function logout() {
	$.get("/api/logout.php", function(data) {
		$(location).attr("href", "index.html");
	});
}
