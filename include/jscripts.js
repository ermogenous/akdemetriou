/*=====================
|    4/12/2009 9:25   |
=======================*/

//============================================================//===============//===============//===============//

var price = "0123456789.";
var phone = "()- 0123456789";
var numb = "0123456789";
var date = "0123456789/";
var datetime = "0123456789- :/";
var alpha = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ ";
function res(t,v){

var w = "";
for (i=0; i < t.value.length; i++) {
x = t.value.charAt(i);
if (v.indexOf(x,0) != -1)
w += x;
}
t.value = w;
}//function res

//============================================================//===============//===============//===============//

function DropDownaddOption(selectbox,text,value )
{
var optn = document.createElement("OPTION");
optn.text = text;
optn.value = value;
selectbox.options.add(optn);
}//function DropDownaddOption

//============================================================//===============//===============//===============//

function DropDownaddOptionsArray(selectbox,array_values,array_labels )
{

for (var i=0 ; i < array_values.length; ++i) {
		DropDownaddOption(selectbox,array_labels[i],array_values[i])
	}

}//function DropDownaddOptionsArray

//============================================================//===============//===============//===============//

function roundNumber(num, dec) {
	var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
	return result;
}//function roundNumber



//============================================================//===============//===============//===============//
//DATE FORMAT FUNCTIONS
var dtCh= "/";
var minYear=1900;
var maxYear=2100;

function isInteger(s){
	var i;
    for (i = 0; i < s.length; i++){   
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag){
	var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){   
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function daysInFebruary (year){
	// February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}
function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31
		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
		if (i==2) {this[i] = 29}
   } 
   return this
}

function isDate(dtStr,accept_empty){
	//check first if the field is empty
	if (dtStr == '' && accept_empty == 1) {
		return true;
	}

	var daysInMonth = DaysArray(12);
	var pos1=dtStr.indexOf(dtCh);
	var pos2=dtStr.indexOf(dtCh,pos1+1);
	var strMonth=dtStr.substring(pos1+1,pos2);
	var strDay=dtStr.substring(0,pos1);
	var strYear=dtStr.substring(pos2+1);
	strYr=strYear;
	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1);
	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1);
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1);
	}
	month=parseInt(strMonth);
	day=parseInt(strDay);
	year=parseInt(strYr);
	if (pos1==-1 || pos2==-1){
		alert("The date format should be : mm/dd/yyyy");
		return false;
	}
	if (strMonth.length<1 || month<1 || month>12){
		alert("Please enter a valid month");
		return false;
	}
	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
		alert("Please enter a valid day");
		return false;
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		alert("Please enter a valid 4 digit year between "+minYear+" and "+maxYear);
		return false;
	}
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
		alert("Please enter a valid date");
		return false;
	}
return true
}

function ValidateDate(field,accept_empty){
	var dt=document.getElementById(field).value;
	if (isDate(dt,accept_empty)==false){
		document.getElementById(field).focus();
		return false;
	}
    return true;
 }

//auto checks on empty fields
var CheckEmptyFieldsFromArrayARRAY = [];
var CheckEmptyFieldsFromArrayARRAYLABELS = [];
var CheckEmptyFieldsFromArrayARRAYTYPE = [];
var CheckEmptyFieldsFromArrayARRAYHIDDENBOX = [];
//PART1: the field name to check PART2 the error to show. PART3 if text label or other (not defined yet). PART4 field if is defined then the function checks first if the field is 1 or 0 then the rest.
function CheckEmptyFieldsFromInsert(field,error_label,type,hidden_value) {
	position = CheckEmptyFieldsFromArrayARRAY.length;
	CheckEmptyFieldsFromArrayARRAY[position] = field;
	CheckEmptyFieldsFromArrayARRAYLABELS[position] = error_label;
	CheckEmptyFieldsFromArrayARRAYTYPE[position] = type;
	CheckEmptyFieldsFromArrayARRAYHIDDENBOX[position] = hidden_value;
}

function CheckEmptyFieldsFromArray() {

error = 0;
labels = '';
var hidden_value = 1;

	for (i=0; i < CheckEmptyFieldsFromArrayARRAY.length; i++) {
	alert(CheckEmptyFieldsFromArrayARRAY.length);
		if (CheckEmptyFieldsFromArrayARRAYTYPE[i] == 'text') {
			
			//check if checkbox exists
			if (CheckEmptyFieldsFromArrayARRAYHIDDENBOX[i] != 'no') {
				if (document.getElementById(CheckEmptyFieldsFromArrayARRAYHIDDENBOX[i]).value == 0){
					hidden_value = 0;		
				}
			}
			
			if (document.getElementById(CheckEmptyFieldsFromArrayARRAY[i]).value == '' && hidden_value == 1) {
				error = 1;
				labels += '-' + CheckEmptyFieldsFromArrayARRAYLABELS[i] + '\n';
			}//if empty
		}
	}//for loop

	if (error == 1) {
		alert('The below fields cannot be empty:\n' + labels);
		return false;
	}

return true;
}

function switchPlusMinusButton(image,plus_url,minus_url,hidden_field) {

	if (document.getElementById(image).src == plus_url) {

		document.getElementById(image).src = minus_url;
		if (document.getElementById(hidden_field)) {
			document.getElementById(hidden_field).value = 1;
		}
	}
	else {

		document.getElementById(image).src = plus_url;
		if (document.getElementById(hidden_field)) 
			document.getElementById(hidden_field).value = 0;
	}
	
}
//DATE FORMAT FUNCTIONS
//============================================================//===============//===============//===============//
function getNiceTime(fromDate, toDate, levels, prefix){
    var lang = {
            "date.past": "{0} ago",
            "date.future": "in {0}",
            "date.now": "now",
            "date.year": "{0} year",
            "date.years": "{0} years",
            "date.years.prefixed": "{0} years",
            "date.month": "{0} month",
            "date.months": "{0} months",
            "date.months.prefixed": "{0} months",
            "date.day": "{0} day",
            "date.days": "{0} days",
            "date.days.prefixed": "{0} days",
            "date.hour": "{0} hour",
            "date.hours": "{0} hours",
            "date.hours.prefixed": "{0} hours",
            "date.minute": "{0} minute",
            "date.minutes": "{0} minutes",
            "date.minutes.prefixed": "{0} minutes",
            "date.second": "{0} second",
            "date.seconds": "{0} seconds",
            "date.seconds.prefixed": "{0} seconds",
        },
        langFn = function(id,params){
            var returnValue = lang[id] || "";
            if(params){
                for(var i=0;i<params.length;i++){
                    returnValue = returnValue.replace("{"+i+"}",params[i]);
                }
            }
            return returnValue;
        },
        toDate = toDate ? toDate : new Date(),
        diff = fromDate - toDate,
        past = diff < 0 ? true : false,
        diff = diff < 0 ? diff * -1 : diff,
        date = new Date(new Date(1970,0,1,0).getTime()+diff),
        returnString = '',
        count = 0,
        years = (date.getFullYear() - 1970);
    if(years > 0){
        var langSingle = "date.year" + (prefix ? "" : ""),
            langMultiple = "date.years" + (prefix ? ".prefixed" : "");
        returnString += (count > 0 ?  ', ' : '') + (years > 1 ? langFn(langMultiple,[years]) : langFn(langSingle,[years]));
        count ++;
    }
    var months = date.getMonth();
    if(count < levels && months > 0){
        var langSingle = "date.month" + (prefix ? "" : ""),
            langMultiple = "date.months" + (prefix ? ".prefixed" : "");
        returnString += (count > 0 ?  ', ' : '') + (months > 1 ? langFn(langMultiple,[months]) : langFn(langSingle,[months]));
        count ++;
    } else {
        if(count > 0)
            count = 99;
    }
    var days = date.getDate() - 1;
    if(count < levels && days > 0){
        var langSingle = "date.day" + (prefix ? "" : ""),
            langMultiple = "date.days" + (prefix ? ".prefixed" : "");
        returnString += (count > 0 ?  ', ' : '') + (days > 1 ? langFn(langMultiple,[days]) : langFn(langSingle,[days]));
        count ++;
    } else {
        if(count > 0)
            count = 99;
    }
    var hours = date.getHours();
    if(count < levels && hours > 0){
        var langSingle = "date.hour" + (prefix ? "" : ""),
            langMultiple = "date.hours" + (prefix ? ".prefixed" : "");
        returnString += (count > 0 ?  ', ' : '') + (hours > 1 ? langFn(langMultiple,[hours]) : langFn(langSingle,[hours]));
        count ++;
    } else {
        if(count > 0)
            count = 99;
    }
    var minutes = date.getMinutes();
    if(count < levels && minutes > 0){
        var langSingle = "date.minute" + (prefix ? "" : ""),
            langMultiple = "date.minutes" + (prefix ? ".prefixed" : "");
        returnString += (count > 0 ?  ', ' : '') + (minutes > 1 ? langFn(langMultiple,[minutes]) : langFn(langSingle,[minutes]));
        count ++;
    } else {
        if(count > 0)
            count = 99;
    }
    var seconds = date.getSeconds();
    if(count < levels && seconds > 0){
        var langSingle = "date.second" + (prefix ? "" : ""),
            langMultiple = "date.seconds" + (prefix ? ".prefixed" : "");
        returnString += (count > 0 ?  ', ' : '') + (seconds > 1 ? langFn(langMultiple,[seconds]) : langFn(langSingle,[seconds]));
        count ++;
    } else {
        if(count > 0)
            count = 99;
    }
    if(prefix){
        if(returnString == ""){

            returnString = langFn("date.now");
        } else if(past)
            returnString = langFn("date.past",[returnString]);
        else
            returnString = langFn("date.future",[returnString]);
    }
    return returnString;
}

function dateDiffInYears(dateold, datenew) {
	var ynew = datenew.getFullYear();
	var mnew = datenew.getMonth();
	var dnew = datenew.getDate();
	var yold = dateold.getFullYear();
	var mold = dateold.getMonth();
	var dold = dateold.getDate();
	var diff = ynew - yold;
	if (mold > mnew) diff--;
	else {
		if (mold == mnew) {
			if (dold > dnew) diff--;
		}
	}
	return diff;
}