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

function replaceQueryParam(param, newval, search) {
    var regex = new RegExp("([?;&])" + param + "[^&;]*[;&]?");
    var query = search.replace(regex, "$1").replace(/&$/, '');
    return (query.length > 2 ? query + "&" : "?") + (newval ? param + "=" + newval : '');
}

function updatePosts(page) {
	if (!page) {
		page = _GET()["page"];
		if (!page) {
			page = 1;
		}
	}
	$.get("/api/getpost.php?page=" + page, function(data, status) {
		if (status == "success") {
			var ret = JSON.parse(data);
			if (ret.status == "ok") {
				$("#view").empty();
				$("#view").append("<h2>Page " + ret.page + "</h2><br>");
				for (var i = 0; i < ret.arr.length; i++) {
					var extra = "";
					if (ret.user_id) {
						extra += ' [<a href="javascript:showReplyEditor(' + ret.arr[i].id + ')">REPLY</a>]';
						if (ret.user_id == ret.arr[i].author) {
							extra += ' [<a href="javascript:showPostEditor(' + ret.arr[i].id + ')">EDIT</a>] ';
							extra += ' [<a href="javascript:deletePost(' + ret.arr[i].id + ')">DELETE</a>] ';
						}
					}
					$("#view").append("<p>#" + ret.arr[i].id + " by <b>" 
						+ ret.arr[i].author + "</b> at <i>" 
						+ ret.arr[i].timestamp + "</i>" 
						+ extra + "</p><pre id=\"m"
						+ ret.arr[i].id + "\">"
						+ ret.arr[i].content + "</pre><div id=\"r"
						+ ret.arr[i].id + "\"></div><br>");
					if (ret.arr[i].comments.length > 0) {
						var comm = "";
						comm += "<h4>Comments</h4><div class=\"comments\">";
						for (var j = 0; j < ret.arr[i].comments.length; j++) {
							comm += "<p>#" + ret.arr[i].comments[j].id + " by <b>" 
								+ ret.arr[i].comments[j].author + "</b> at <i>" 
								+ ret.arr[i].comments[j].timestamp + "</i></p><pre>"
								+ ret.arr[i].comments[j].content + "</pre><br>";
						}
						$("#view").append(comm + "</div><br>");
					}
				}
				if (ret.page > 1) {
					$("#view").append('<a href="javascript:updatePosts(' + (ret.page - 1) + ')"> [< NEXT] </a>');
				}
				if (!ret.last) {
					$("#view").append('<a href="javascript:updatePosts(' + (ret.page + 1) + ')"> [> PREV] </a>');
				}
				history.replaceState(null, null, window.location.pathname 
					+ replaceQueryParam('page', ret.page, window.location.search));
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

function newpost() {
	var info = {
		message: $("#message").val()
	};
	$.post("./api/action.php?method=submit", info, function(data, status) {
		if (status == "success") {
			var ret = JSON.parse(data);
			if (ret.status == "ok") {
				$("#message").val("Your message");
				updatePosts(1);
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

function deletePost(id) {
	$.get("/api/action.php?method=delete&id=" + id, function(data, status) {
		if (status == "success") {
			var ret = JSON.parse(data);
			if (ret.status == "ok") {
				updatePosts();
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

function showPostEditor(id) {
	$("#r" + id).empty();
	$("#r" + id).append('<form id="ep' + id + '" action="#" method="post">'
		+ '<h4> Edit your message </h4>'
		+ '<textarea rows="10" cols="80" id="ept' + id + '" name="message" required>'
		+ $("#m" + id).text()
		+ '</textarea><br>'
		+ '<button type="submit"> Submit </button>'
		+ '</form>');
	$("#ep" + id).submit(function(e) {
		e.preventDefault();
		editPost(id);
	});
}

function editPost(id) {
	var info = {
		message: $("#ept" + id).val()
	};
	$.post("./api/action.php?method=edit&id=" + id, info, function(data, status) {
		if (status == "success") {
			var ret = JSON.parse(data);
			if (ret.status == "ok") {
				updatePosts();
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

function showReplyEditor(id) {
	$("#r" + id).empty();
	$("#r" + id).append('<form id="rp' + id + '" action="#" method="post">'
		+ '<h4> Reply </h4>'
		+ '<textarea rows="10" cols="80" id="rpt' + id + '" name="message" required>'
		+ 'Your message</textarea><br>'
		+ '<button type="submit"> Submit </button>'
		+ '</form>');
	$("#rp" + id).submit(function(e) {
		e.preventDefault();
		replyPost(id);
	});
}

function replyPost(id) {
	var info = {
		message: $("#rpt" + id).val()
	};
	$.post("./api/action.php?method=reply&post_id=" + id, info, function(data, status) {
		if (status == "success") {
			var ret = JSON.parse(data);
			if (ret.status == "ok") {
				updatePosts();
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