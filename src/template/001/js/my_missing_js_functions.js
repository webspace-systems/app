

jQuery.each( [ "put", "delete" ], function( i, method ) {
  jQuery[ method ] = function( url, data, callback, type ) {
    if ( jQuery.isFunction( data ) ) {
      type = type || callback;
      callback = data;
      data = undefined;
    }

    return jQuery.ajax({
      url: url,
      type: method,
      dataType: type,
      data: data,
      success: callback
    });
  };
});

jQuery.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};
String.prototype.replaceAll = function(search, replacement) {
    var target = this;
    return target.split(search).join(replacement);
};
jQuery.fn.outerHtml = function(s) {
    return s
        ? this.before(s).remove()
        : jQuery("<p>").append(this.eq(0).clone()).html();
};

jQuery.fn.outerWidth = function() {
    return parseInt(this.width())+parseInt(this.css('padding-left'))+parseInt(this.css('padding-right'));
};

String.prototype.explode = function(separator, limit)
{
    var arr = this.split(separator);
    if (limit) arr.push( arr.splice(limit-1).join(separator) );
    return arr;
}

function isset( string ){
  if( ("_"+string).length>1 && ("_"+string) != '_null' && ("_"+string) != '_undefined' ) return true;
  else return false;
}

function escapeRegExp(str) {
    return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
}

function isInteger( num )
{
  if(("_"+parseInt(num))=='_NaN')
    return false;
  else
    return true;
}

function isArray( num )
{
  if(isset(num) && typeof num == 'object')
    return true;
  else
    return false;
}

function isString( str )
{
	str = "_"+str;
	if(str=='_undefined'||str=='_null'||str=='_'||str=='_[object Object]')return false;
	else return true;
}

function isNumber(v){
    return ("."+parseFloat(v))=='.NaN' ? 0 : parseFloat(v);
}
function validateNumber(v){
    return ("."+parseFloat(v))=='.NaN' ? 0 : parseFloat(v);
}

function ucfirst(str) {
  str += '';
  var f = str.charAt(0).toUpperCase();
  return f + str.substr(1);
}
function htmlentities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}


function sortAlphaNum(arr,key){

  var arr1 = new Array();
  var arr2 = new Array();

  arr.forEach(function(item){
    if(isInteger(item[key]))
      arr1[arr1.length] = item;
    else
      arr2[arr2.length] = item;
  });

  arr1.sort(function (a,b) {
    if (a[key] < b[key]) return 1;
    if (a[key] > b[key]) return -1;
    return 0;
  });
  arr2.sort(function (a,b) {
    if (a[key] < b[key]) return 1;
    if (a[key] > b[key]) return -1;
    return 0;
  });

  arr1.reverse();
  arr2.reverse();

  var completeArray = arr1;
  arr2.forEach(function(item){
    completeArray[completeArray.length] = item;
  });

  return completeArray;

}


function supports_html5_storage(){
    try {
        return 'localStorage' in window && window['localStorage'] !== null;
    } catch(e) {
        return false;
    }
}

function format_namespace( str ){
  return str.replace(' ','-').toLowerCase().replace(/[^a-z0-9-_]/gi,'')
}

function getTimerange( time_start, time_end ){
    if(Number.isInteger(time_start) != true)
    {
        time_start = getTimestamp(time_start,1);
        time_end = getTimestamp(time_end,1);
    }
    if(parseInt(time_start) == 0) return '';
    return time_end-time_start;
}

function getAboutTime( time_start, time_end ){
    var timerange = getTimerange( time_start, time_end );
    var tr_minutes = Math.round(timerange/60);
    var tr_hours = Math.round(timerange/60/60);
    var tr_days = Math.round(timerange/60/60/24);
    var tr_months = Math.round(timerange/60/60/24/30);

    if(tr_minutes < 5) return 'under 5 minutes';
    else if(tr_minutes > 4 && tr_minutes < 55 ) return 'about '+tr_minutes+' minutes';
    else if(tr_minutes > 54 && tr_minutes < 90 ) return 'about an hour';
    else if(tr_minutes > 89 && tr_hours < 23 ) return 'about '+tr_hours+' hours';
    else if(tr_hours > 23 && tr_hours < 32 ) return 'about a day';
    else if(tr_hours > 32 && tr_days < 30 ) return 'about '+tr_days+' days';
    else if(tr_days > 30 && tr_days < 45 ) return 'about a month';
    else if(tr_months > 1 ) return 'about '+tr_months+' months';
    else return 'about '+tr_days+' days';
}

function formatDurationTime( seconds, incl_ms ){
    if(!incl_ms)
        seconds = seconds*1000;
    var date = new Date(seconds);
    var day = date.getDate()-1;
    var hour = date.getHours()-1;
    var minute = date.getMinutes();

    if(minute<10)minute = '0'+minute;

    if(day>0)hour = hour+(day*24);

    if(hour<10)hour = '0'+hour;

    return hour+':'+minute;
}

function getTimeDuration( time_start, time_end ){
    if(Number.isInteger(time_start))
    {
        tr_seconds = (time_end-time_start)*1000;
    }
    else var tr_seconds = getTimerange( time_start, time_end );

    return formatDurationTime(tr_seconds,true);
}

function getHashParams() {

    var hashParams = {};
    var e,
        a = /\+/g,  // Regex for replacing addition symbol with a space
        r = /([^&;=]+)=?([^&;]*)/g,
        d = function (s) { return decodeURIComponent(s.replace(a, " ")); },
        q = window.location.hash.substring(1);

    while (e = r.exec(q))
       hashParams[d(e[1])] = d(e[2]);

    return hashParams;
}


function btnLoadingState( btn, on ){
	if(on)
	{
		$('html,body').css({'opacity':'0.95','pointer-events':'none'});
		$(btn).css({'opacity':'0.85','pointer-events':'none'});
    //$(btn).css('min-width',parseFloat($(btn).outerWidth())+'px');
    $(btn).data('ohtml',$(btn).html());
    $(btn).html('<i class="fa fa-spinner fa-spin"></i>');
	}
	else
	{
		$('html,body').css({'opacity':'1','pointer-events':'all'});
		$(btn).css({'opacity':'1','pointer-events':'all'});
		$(btn).html($(btn).data('ohtml'));
	}
}
