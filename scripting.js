/*function cc_sel(el) {el.style.backgroundColor = '#fcfc81';}
function cc_des(el) {el.style.backgroundColor = 'transparent';}*/
function cl(href){location.href=href;}
function doubles(el) {
        el.style.backgroundColor = '#0FC0C0';
        var div = document.getElementById('z');
        var elems = div.getElementsByTagName('abbr');
        var k=0;
        for(var i=8; i<elems.length; i=i+10) {
                for (var j=10; j<elems.length;j=j+10) {
                        if (i != j) {if (elems[i].innerHTML==elems[j].innerHTML) {elems[j].style.textShadow='#ff0000 -1px -1px 3px,red 1px 1px 3px';k++;}}}}
        alert('Дубликатов: '+ k/2);}
function butclick(el) {el.style.boxShadow='0px 0px 2px 1px black';}
function p() {
	var pr=document.getElementById('p');
	var pl=document.getElementById('plus');
        if(pr.style.display == 'none'){
//		pl.style.backgroundColor = '#80ff86';
		pl.style.position = 'absolute';
		pr.style.display = 'block';
		pl.style.top = "393px";}
        else {
//                pl.style.backgroundColor = '#ffc086';
		pl.style.position = 'fixed';
                pr.style.display = 'none';
		pl.style.top = "-2px";}}
function insvk(dv1,dv2,bt1,bt2) {
	var b=document.getElementById("b");
	bt2.style.backgroundColor = 'grey';
	bt1.style.backgroundColor = 'green';
	dv2.style.display = 'none';
	dv1.style.display = 'block';
	if (b.value == '1') {b.value = '3';} else {b.value='1';}}
