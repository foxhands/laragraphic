var sTimerFlag = false;
var sTimerCount = 0;

function fToolTipOpen(event, text){

    if (sTimerFlag==false) {
	sTimerFlag = true;
	$("#dd_div").html(text);
	var grp_div=document.getElementById('dd_div');

	if(!event)event=window.event;

	coord_x = event.clientX;
	coord_y = event.clientY+10;

	left = false; 
	right = false;
	if(coord_x+grp_div.clientWidth>document.body.clientWidth){
	    coord_x = document.body.clientWidth-grp_div.clientWidth;
	    left = true;
	}
	if(coord_y+grp_div.clientHeight>document.body.clientHeight){
	    coord_y = document.body.clientHeight-grp_div.clientHeight;
	    right = true;
	}
	if(left&&right)coord_y = document.body.clientHeight-grp_div.clientHeight;

	zY = self.pageYOffset || (document.documentElement && document.documentElement.scrollTop) || (document.body && document.body.scrollTop);
	zX = self.pageXOffset || (document.documentElement && document.documentElement.scrollLeft) || (document.body && document.body.scrollLeft);

	grp_div.style.left = coord_x+zX+'px';
	grp_div.style.top = coord_y+zY+'px';
	$("#dd_div").fadeIn(250);
	sTimerCount = setTimeout("GroupClose(2)", 6000);
    }
}

    function GroupClose(value) {
	var grp_div=document.getElementById('dd_div');
	    if (value==0) {
		sTimerCount = setTimeout("GroupClose(2)", 700);    
	    }
	    if (value==1) {
		clearTimeout(sTimerCount);
	    }
	    //immediately close dd_div
	    if (value==2) {
		clearTimeout(sTimerCount);
		$("#dd_div").fadeOut(200);
		grp_div.style.display = "none";
		sTimerFlag = false;
	    }
    }
