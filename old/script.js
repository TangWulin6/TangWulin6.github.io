var a = 0;




function Fan(){
    if(a == 0) {
        document.getElementById("e").innerHTML ="有时候，知道太多并不一定是一件好事。";
        a = 1
    } 
    else{
        document.getElementById("e").innerHTML ="!你!按!够!了!没!有!";
        document.getElementById("e").style="font-weight:bold;";
    }
    
}
function Zan(){
    window.alert("感谢支持！");
}
function Srh(){
    var s = document.getElementById("se");
    var v = s.value;
    var str1 = "https://cn.bing.com/search?q="+v
    window.open(str1);
}