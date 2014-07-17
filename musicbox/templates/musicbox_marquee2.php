<html>

<head>
<meta charset="UTF-8">
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="css/day.css" />
<link rel="stylesheet" type="text/css" href="musicbox.css" />

</head>
<body id="bdy">

{% include  %}

<div id="style-panel">
	<div id="star" class="btn btn-default glyphicon glyphicon-star" onclick="ChangeCSS()";> </div>
</div>

<audio id="player" src="music/%E3%80%90GUMI%E3%80%91%E8%A2%AB%E5%AE%B3%E5%A6%84%E6%83%B3%E6%90%BA%E5%B8%AF%E5%A5%B3%E5%AD%90%EF%BC%88%E7%AC%91%EF%BC%89%E3%80%90%E3%82%AA%E3%83%AA%E3%82%B8%E3%83%8A%E3%83%AB%E3%80%91.mp3">
FireFox GG!!<br>
Try to use chrome?<br>
</audio>

<div style="margin: 10px;">
	<div id="back_button" class="glyphicon glyphicon-backward button control-button"></div>
	<div id="play_button" class="glyphicon glyphicon-play button control-button"></div>
	<div id="volume_bar"> </div>
</div>

<div id="playlist" class="table table-condensed">
</div>

<script language="javascript">
var g_playlist = [
<?php
	$path = './music';
	$dir = opendir($path);
	$first = true;
	while(false !== ($file = readdir($dir))){
		if($file != "." && $file != ".."){
			if(!is_dir($path."/".$file)){
				echo(sprintf("%s{url: 'music/%s', name: '%s'%s}\n",
							$first?"":",",
							urlencode($file),
							addslashes(substr($file, 0, strlen($file)-4)),
							isset($_GET["q"])&&strpos($file, $_GET["q"])!==false?",init: true":"" ));
				$first = false;
			}
		}
	}
	closedir($dir);
?>];
</script>
<script langauge="javascript">

var progress_bar;

playlist.play = function(x){
	if(playlist.prev_play_item)
	{
		playlist.prev_play_item.className = playlist.prev_play_item.className.replace(" playing-item", "");
		playlist.prev_play_item.removeChild(progress_bar);
		var text = playlist.prev_play_item.getElementsByTagName("marquee")[0].textContent;
		playlist.prev_play_item.textContent = text;
	}
	x.className = x.className + " playing-item";
	var text = x.textContent;
	x.innerHTML = "<marquee style='width: 300px;' direction='right'>" + text + "</marquee>";
	playlist.prev_play_item = x;
	var tar = x.url.replace(/\+/g, "%20");

	var pro = progress_bar || createProgressBar();
	pro.progress.style.width = "0%";
	x.appendChild(pro);

	player.pause();
	setTimeout(function(){player.setAttribute("src", tar); player.play();}, 1000);
}
// player
player.addEventListener('ended', function(){
<?php
	// TODO refactor this part
	if(isset($_GET['rep'])){
?>
	playlist.play(playlist.prev_play_item);
<?php
	}
	else{
?>
	if(playlist.prev_play_item.nextSibling.nextSibling.url){
		playlist.play(playlist.prev_play_item.nextSibling.nextSibling);
	}
	else{
		playlist.play(playlist.firstElementChild.nextSibling);
	}
<?php
	}
?>
}, false);

player.addEventListener('pause', function(e){
	play_button.className = play_button.className.replace("glyphicon-pause", "glyphicon-play");
}, false);

player.addEventListener('play', function(){
	play_button.className = play_button.className.replace("glyphicon-play", "glyphicon-pause");
}, false);

function createProgressBar(){
	var pro = document.createElement("div");
	pro.id = "progress_bar";
	pro.appendChild(pro.progress = document.createElement("div"));
	pro.progress.id = "played_progress";
	pro.progress.style.width = "0%";
	pro.addEventListener('click' ,function(event){
		player.currentTime =
		player.duration * (event.clientX - this.getBoundingClientRect().left) / this.clientWidth;
		event.stopPropagation();
	}, false);
	pro.addEventListener('mousedown' ,function(e){e.stopPropagation();});
	bdy.appendChild(pro);
	progress_bar = pro;
	return pro;
}

player.addEventListener('timeupdate', function(){
	if(progress_bar) progress_bar.progress.style.width = (this.currentTime / this.duration) * 100 + '%';
}, false);

player.addEventListener('progress', function(){
	//var endVal = this.seekable && this.seekable.length ? this.seekable.end(0) : 0;
	//buffered_progress.style.width = (endVal / this.duration) * 100 + '%';
}, false);

// play_button
play_button.addEventListener('click' ,function(event){
	if(this.className.search("glyphicon-play") != -1){
		player.play();
	}
	else if(this.className.search("glyphicon-pause") != -1){
		player.pause();
	}
}, false);

// back_button
back_button.addEventListener('click', function(event){
	player.currentTime = 0;
}, false);

// volume_bar
{
	for(var i = 0 ; i < 12 ; i ++){
		volume_bar.innerHTML += "<div class='vbar vbar-on' style='margin-top: " + (21 - i) + "px;height: " + (10 + i) + "px'></div>";
	}
	volume_bar.addEventListener('click', function(e){
		var r = this.getBoundingClientRect();
		var bars = this.getElementsByTagName("div");
		for(i in bars){
			if(!(bars[i].getBoundingClientRect))
				continue;
			if(bars[i].getBoundingClientRect().left < e.clientX){
				bars[i].className = bars[i].className.replace("vbar-off", "vbar-on");
			}
			else{
				bars[i].className = bars[i].className.replace("vbar-on", "vbar-off");
			}
		}
		player.volume = (e.clientX - r.left + 0.0) / (r.right - r.left);

	});
}

{
	function ChangeCSS(){
		var old = document.getElementsByTagName("link")[1];
		var lnk = document.createElement("link");
		lnk.setAttribute("type", "text/css");
		lnk.setAttribute("rel", "stylesheet");
		lnk.setAttribute("href", "css/" + (old.href.search("night") != -1 ?"day":"night") + ".css");
		old.parentNode.replaceChild(lnk, old);
	}
}

{
	/* support single drag-drop currently */
	var g_movingTar = null;
	function cloneMouseEvent(e){
			var e2 = document.createEvent("MouseEvents");
			e2.initMouseEvent(e.type, true, true, window, 1, e.screenX, e.screenY, e.clientX, e.clientY,
        						e.ctrlKey, e.altKey, e.shiftKey, e.metaKey, 0, null);
			return e2;
	}
	bdy.addEventListener('mousemove', function(e){
		if(g_movingTar){
			// offset 20, 10
			g_movingTar.style.left = (e.pageX + 20) + "px";
			g_movingTar.style.top = (e.pageY + 10) + "px";
			if(!e.in_p && playlist.activePlacer) playlist.handleMouseExit();
		}
	});
	bdy.addEventListener('mouseup', function(e){
		if(playlist.pressTimer) clearTimeout(playlist.pressTimer);
	});

	function createSeparator(){
		//echo "<div class='playitem-separator'></div>";
		var x = document.createElement("div");
		x.className = "playitem-separator";
		return x;
	}

	function createPlayItem(name, url){
		var x = document.createElement("div");
		x.className = "playitem playable";
		x.textContent = name;
		x.url = url;
		x.setAttribute("onclick", "playlist.play(this);");
		x.addEventListener('mousedown', function(e){
			if(!g_movingTar){
				if(playlist.pressTimer) clearTimeout(playlist.pressTimer);
				var div = this;
				playlist.pressTimer = window.setTimeout(function(){
					var p = div.parentNode;
					p.removeChild(div.previousSibling);
					p.removeChild(div);
					div.className = div.className + " moving";
					div.style.left = (e.pageX + 20) + "px";
					div.style.top = (e.pageY + 10) + "px";
					bdy.appendChild(div);
					g_movingTar = div;
					var e2 = document.createEvent("MouseEvents");
					e2.initMouseEvent("mousemove", true, true, window, 1, e.screenX, e.screenY, e.clientX, e.clientY,
										e.ctrlKey, e.altKey, e.shiftKey, e.metaKey, 0, null);
					bdy.dispatchEvent(e2);
				}, 1000);
			}
		});
		playlist.addEventListener('mousemove', function(e){
			e.in_p = true;
		});
		x.addEventListener('mousemove', function(e){
			if(g_movingTar){
				var x = createItemPlacer();
				x.active();
				if(playlist.activePlacer){
					// replace itself with placer
					playlist.replaceChild(x, this);
					// replaced with previous placer
					if(playlist.activePlacer == playlist.lastChild)
					{
						playlist.insertBefore(this, playlist.lastChild);
						playlist.insertBefore(createSeparator(), playlist.lastChild);
						playlist.lastChild.inactive();
					}
					else
					{
						playlist.replaceChild(this, playlist.activePlacer);
					}
					playlist.activePlacer = x;
				}
				else{
					playlist.insertBefore(playlist.activePlacer = x, this);
					playlist.insertBefore(createSeparator(), this);
				}
				if(x.nextSibling.nextSibling == playlist.lastChild)
				{
					playlist.removeChild(playlist.lastChild); // placer
					playlist.removeChild(playlist.lastChild); // separator
					playlist.setLastPlacer();
				}
			}
        });
		return x;
	}

	function createItemPlacer(){
		var x = document.createElement("div");
		x.className = "playitem placer-inactive";
		x.inactive = function(){
			this.className = this.className.replace("-active", "-inactive");
		};
		x.active = function(){
			this.className = this.className.replace("-inactive", "-active");
		};
		x.addEventListener('click', function(){
			if(g_movingTar){
				g_movingTar.parentNode.removeChild(g_movingTar);
				g_movingTar.className = g_movingTar.className.replace(" moving", "");
				var p = this.parentNode;
				if(this == p.lastChild){
					p.insertBefore(g_movingTar, this);
					p.insertBefore(createSeparator(), this);
					this.inactive();
				}
				else
					p.replaceChild(g_movingTar, this);
				g_movingTar = null;
			}
		});
		return x;
	}

	function createSeparator(){
		var x = document.createElement("div");
		x.className = "playitem-separator";
		return x;
	}
	/* playlist */

	playlist.addPlayItem = function(item){
		this.appendChild(createSeparator());
		this.appendChild(item);
	};

	playlist.setLastPlacer = function(){
		playlist.lastChild.addEventListener('mousemove', function(e){
			if(g_movingTar && playlist.activePlacer != this) (playlist.activePlacer = this).active();
		});
	};
	
	playlist.handleMouseExit = function(){
		var x = this.activePlacer;
		delete this.activePlacer;
		if(x == this.lastChild)
			x.inactive();
		else{
			this.removeChild(x.previousSibling); // separator
			this.removeChild(x);
		}
	};

	var initialItem = null;
	for(var idx in g_playlist){
        var x = createPlayItem(g_playlist[idx].name, g_playlist[idx].url);
        playlist.addPlayItem(x);
		if(g_playlist[idx].init) initialItem = x;
	}
	playlist.addPlayItem(createItemPlacer());
	playlist.setLastPlacer();
	if(initialItem) playlist.play(initialItem);
}


</script>
</body>

</html>
