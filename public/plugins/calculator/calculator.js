function calEnterVal(id){document.calc.result.value+=id;}
function clearScreen(){document.calc.result.value="";}
function calculate(){try{var input=eval(document.calc.result.value);document.calc.result.value=input;}catch(err){document.calc.result.value="Error";}}
$(document).ready(function(){$('#btnCalculator').popover();});document.onkeypress=function(e){if(e.keyCode==13){e.preventDefault();calculate();}
keyPressed=String.fromCharCode(e.which);calEnterVal(keyPressed);};