function closeForm() {
    document.getElementById("modifyForm").style.display = "none";
    document.getElementById("page-mask").style.display = "none";
}

function deleteRow(){
    document.getElementById("formDelete").value=document.getElementById("formID").value;
}

function moveRowUp(i){
    document.getElementById("formID").value= i;
    document.getElementById("formSwap").value= i-1;
    document.getElementById('formProperty').submit();
}

function moveRowDown(i){
    document.getElementById("formID").value= i;
    document.getElementById("formSwap").value= i+1;
    document.getElementById('formProperty').submit();
}

document.addEventListener("DOMContentLoaded", function(event) { 
    var scrollpos = localStorage.getItem('scrollpos');
    if (scrollpos) window.scrollTo(0, scrollpos);
});

window.onbeforeunload = function(e) {
    localStorage.setItem('scrollpos', window.scrollY);
};

document.getElementById("custom").addEventListener("change",function(){
    document.getElementById("custom_insert").readOnly = false;
    document.getElementById("custom_insert").required = true;
});

document.getElementById("auto-start").addEventListener("change",function(){
    document.getElementById("custom_insert").value="";
    document.getElementById("custom_insert").readOnly = true;
    document.getElementById("custom_insert").required = false;
});

document.getElementById("auto-end").addEventListener("change",function(){
    document.getElementById("custom_insert").value="";
    document.getElementById("custom_insert").readOnly = true;
    document.getElementById("custom_insert").required = false;
});