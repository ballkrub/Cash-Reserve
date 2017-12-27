// JavaScript Document
function calDateNext3days(start,range) {
	var res = start.split("-");
	//var begin = new Date(res[0], res[1], res[2]);
	var recieveDate = new Date(res[0], res[1]-1, res[2]);
	for(var i=0;i<range;i++) { //next 3 woiking day
		recieveDate = new Date(recieveDate.getTime() + (24 * 60 * 60 * 1000));
		if(recieveDate.getDay() == 0) { //Sunday
			recieveDate = new Date(recieveDate.getTime() + (24 * 60 * 60 * 1000));
		}else {}
	
	}
	var month = recieveDate.getUTCMonth() + 1; //months from 1-12
	if(month<10) { month = "0"+month; }
	var day = recieveDate.getUTCDate();
	if(day<10) { day = "0"+day; }
	var year = recieveDate.getUTCFullYear();
	return year+"-"+month+"-"+day;
} 

function getWorkingDays(startDate, endDate){
     var result = 0;

    var currentDate = startDate;
    while (currentDate <= endDate)  {  

        var weekDay = currentDate.getDay();
        if(weekDay != 0 && weekDay != 6)
            result++;

         currentDate.setDate(currentDate.getDate()+1); 
    }

    return result;
 }