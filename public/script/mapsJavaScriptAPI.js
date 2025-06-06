var CROSproxyURL = 'https://www.whateverorigin.org/get?url=';

var args = '';
if (typeof language != 'undefined') args += '&language=' + language;

var bypass = function (googleAPIcomponentJS, googleAPIcomponentURL) {
    if (googleAPIcomponentURL.toString().indexOf("common.js") != -1) {
        var removeFailureAlert = function(googleAPIcomponentURL) {
            sendRequestThroughCROSproxy(googleAPIcomponentURL,(responseText)=>{
                var anotherAppendChildToHeadJSRegex = /\.head;.*src=(.*?);/;
                var anotherAppendChildToHeadJS = responseText.match(anotherAppendChildToHeadJSRegex);
                var googleAPItrustedScriptURL = anotherAppendChildToHeadJS[1];
                var bypassQuotaServicePayload = anotherAppendChildToHeadJS[0].replace(googleAPItrustedScriptURL, googleAPItrustedScriptURL+'.toString().indexOf("QuotaService.RecordEvent")!=-1?"":'+googleAPItrustedScriptURL);

                var script = document.createElement('script');
                script.innerHTML = responseText.replace(new RegExp(/;if\(![a-z]+?\).*Failure.*?\}/), ";").replace(new RegExp(/(\|\|\(\(\)=>\{\}\);\S+\?\S+?\()/), "$1true||").replace(anotherAppendChildToHeadJSRegex, bypassQuotaServicePayload);
                document.head.appendChild(script);
            });
        }
        googleAPIcomponentJS.innerHTML = '(' + removeFailureAlert.toString() + ')("' + googleAPIcomponentURL.toString() + '")';
    } else if(googleAPIcomponentURL.toString().indexOf("map.js") != -1){
        var hijackMapJS = function(googleAPIcomponentURL) {
            sendRequestThroughCROSproxy(googleAPIcomponentURL,(responseText)=>{
                var script = document.createElement('script');

                var unknownStatusRegex = /const\s+(\w+)\s*=.*?;/g;
                var unknownStatusMatch = responseText.match(unknownStatusRegex);
      
                for(let i=0;i<unknownStatusMatch.length;i++){
                    if(unknownStatusMatch[i].indexOf("getStatus")!=-1){
                        script.innerHTML = responseText.replace(unknownStatusMatch[i], unknownStatusMatch[i].replace(/=.*/, '=1;'));
                        break;
                    }
                }
                document.head.appendChild(script);
            });
        }
        googleAPIcomponentJS.innerHTML = '(' + hijackMapJS.toString() + ')("' + googleAPIcomponentURL.toString() + '")';
    } else {
        googleAPIcomponentJS.src = googleAPIcomponentURL;
    }
}

var createAndExecutePayload = function (googleAPIjs){
    var script = document.createElement('script');
    var appendChildToHeadJS = googleAPIjs.match(/(\w+)\.src=(_.*?);/);
    var googleAPIcomponentJS = appendChildToHeadJS[1];
    var googleAPIcomponentURL = appendChildToHeadJS[2];
    script.innerHTML = googleAPIjs.replace(appendChildToHeadJS[0], '(' + bypass.toString() + ')(' + googleAPIcomponentJS + ', ' + googleAPIcomponentURL + ');');
    document.head.appendChild(script);
}

sendRequestThroughCROSproxy('https://maps.googleapis.com/maps/api/js?key=AIzaSyB41DRUbKWJHPxaFjMAwdrzWzbVKartNGg&&libraries=places,geometry&&callback=initMap' + args, (googleAPIjs)=>{
    createAndExecutePayload(googleAPIjs);
});

function sendRequestThroughCROSproxy(url, callback){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4) {
            if(this.status == 200){
                if(callback) callback(JSON.parse(this.responseText).contents);
            }else{
                sendRequestThroughCROSproxy(url, callback);//retry
            }
        }
    };
    xhttp.open("GET", CROSproxyURL + encodeURIComponent(url), true);
    xhttp.send();
}