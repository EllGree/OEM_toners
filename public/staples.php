<?php
/**
 * Search for the OEM toner and information using staples.com API
 *
 * @author   EllGree <ellgree@gmail.com>
 */

 if(!empty($_GET['term'])) {
    header('Content-Type: application/json; charset=utf-8');
    /**
     * This is the place to embed caching or to connect proxies
     */
    $term = urlencode($_GET['term']);
    $uri = "https://www.staples.com/searchux/common/api/v1/searchProxy?term={$term}&categoryId=12328";
    /**
     * To find a printer by name, the following code can be added to get the autocomplete list:
     * if(!empty($_GET['autocomlete'])) $uri = "https://www.staples.com/itfux/autocomplete?term={$term}";
     */
    $header = ["Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5",
        "Cache-Control: max-age=0", "Connection: keep-alive", "Keep-Alive: 300", "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7",
        "Accept-Language: en-us,en;q=0.5","Pragma: "];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $uri);
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:7.0.1) Gecko/20100101 Firefox/7.0.12011-10-16 20:23:00");
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_REFERER, "https://www.staples.com/Ink-Toner-Finder/cat_SC43");
    curl_setopt($curl, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($curl, CURLOPT_AUTOREFERER, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION,true);
    $txt = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);
    die($error ? $error : ($txt ? $txt : "Failed"));
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OEM Ink / Toner Search</title>
    <style>
body {
    margin: 0;
    font-family: "Helvetica", sans-serif;
    font-size: 16px;
}
.search {display: block;margin: 16px;margin-right: 8px;border: none;}
.search input[type="search"] {
  display: inline-block;
  width: calc(100% - 5px);
  border: solid 1px rgb(168, 168, 168);
  border-radius: 2px;
  background: transparent;
  margin: 0;
  padding: 7px 8px;
  font-size: 16px;
  color: inherit;
}
.search input[type="search"]::placeholder {
  color: rgb(168, 168, 168);
}
.search input[type="search"]:focus {
  border-color: #1183d6;
  outline: none;
}

.search button {
  text-indent: -999px;
  overflow: hidden;
  width: 40px;
  height: 33px;
  padding: 0;
  margin: 0;
  border: 1px solid transparent;
  border-radius: inherit;
  background: transparent url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' class='bi bi-search' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'%3E%3C/path%3E%3C/svg%3E") no-repeat center;
  cursor: pointer;
  opacity: 0.5;
  transition: all 0.2s;
  margin-left: -44px;
}

.search button:hover {
  opacity: 1;
}
.search .recent {
    transition: all 0.2s;
    opacity: 0;
    max-height: 0;
    height: 0;
    width: calc(100% - 31px);
    position: fixed;
    overflow: hidden;
    background-color: #ebebeb;
    color: #010f54;
    border: none;
    box-shadow: none;
}
.search:hover .recent {
    margin-top: -1px;
    z-index: 100;
    opacity: 1;
    height: auto;
    max-height: calc(100% - 66px);
    border: 1px solid #a8a8a8;
    box-shadow: 0 12px 15px 0 rgba(0, 0, 0, 0.24);
}
.search .recent .term, .search .recent .clear {
    padding: 8px; cursor: pointer;
}
.search .recent .clear:hover, .search .recent .term:hover {
    text-decoration: underline;
    color: navy;
}
.search .recent .clear {
    float: right; font-size: 14px; margin-top: -18px;
}

#results {display: block;padding: 18px;}
.title {font-weight: bold;margin-bottom:6px;}
.subtitle {font-weight: normal;margin-bottom:6px;}
.details {font-size: 12px;margin-bottom:4px;}
.details::before {
    content:'';
    background: rgb(200, 200, 200);
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 6px;
    margin-left: 6px;
    margin-right: 6px;
}
.details.black::before { background: rgb(0, 0, 0);}
.details.cyan::before { background: rgb(0, 145, 145);}
.details.magenta::before { background: rgb(152, 0, 152);}
.details.yellow::before { background: rgb(213, 213, 0);}
.details.tri-color::before { 
    background: linear-gradient(90deg, rgb(0, 145, 145) 0%, rgb(0, 145, 145) 33%, rgb(152, 0, 152) 33%, rgb(152, 0, 152) 66%, rgb(213, 213, 0) 66%, rgb(213, 213, 0) 100%);
}

#mask {
  display: none;
  position: absolute;
  user-select: none;
  height: 100%;
  width: 100%;
  background-color: rgb(0 0 0 / 43%);
  bottom: 0;
  left: 0;
  right: 0;
  top: 0;
  z-index: 9999;
}
#mask.loading {
  display: block;
}
#mask.loading:before {
    content: "";
    background-color: rgba(0, 0, 0, 0);
    border: 5px solid rgb(255 243 16 / 90%);
    opacity: 0.9;
    border-right: 5px solid rgb(0 191 255 / 95%);
    border-left: 5px solid rgb(255 12 230);
    border-top: 5px solid black;
    border-radius: 50px;
    box-shadow: 0 0 3px 6px #ffffff;
    width: 50px;
    height: 50px;
    -moz-animation: spinPulse 1s infinite ease-in-out;
    -webkit-animation: spinPulse 1s infinite linear;
    margin: -25px 0 0 -25px;
    position: absolute;
    top: 50%;
    left: 50%;
}
#mask.loading:after {
    content: "";
    background-color: rgba(0, 0, 0, 0);
    border: 5px solid rgb(255 243 16 / 90%);
    opacity: 0.9;
    border-left: 5px solid rgb(255 12 230);
    border-right: 5px solid rgb(0 191 255 / 95%);
    border-top: 5px solid black;
    border-radius: 50px;
    box-shadow: 0 0 3px 2px #ffffff;
    width: 30px;
    height: 30px;
    -moz-animation: spinoffPulse 1s infinite linear;
    -webkit-animation: spinoffPulse 1s infinite linear;
    margin: -15px 0 0 -15px;
    position: absolute;
    top: 50%;
    left: 50%;
}
@-moz-keyframes spinPulse {
  0% {
    -moz-transform: rotate(160deg);
    opacity: 0;
    box-shadow: 0 0 1px #2187e7;
  }
  50% {
    -moz-transform: rotate(145deg);
    opacity: 1;
  }
  100% {
    -moz-transform: rotate(-320deg);
    opacity: 0;
  }
}
@-moz-keyframes spinoffPulse {
  0% {
    -moz-transform: rotate(0deg);
  }
  100% {
    -moz-transform: rotate(360deg);
  }
}
@-webkit-keyframes spinPulse {
  0% {
    -webkit-transform: rotate(160deg);
    opacity: 0;
    box-shadow: 0 0 1px #2187e7;
  }
  50% {
    -webkit-transform: rotate(145deg);
    opacity: 1;
  }
  100% {
    -webkit-transform: rotate(-320deg);
    opacity: 0;
  }
}
@-webkit-keyframes spinoffPulse {
  0% {
    -webkit-transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
  }
}

input[type=text]::-ms-clear { display: none; width : 0; height: 0; }
input[type=text]::-ms-reveal { display: none; width : 0; height: 0; }
input[type="search"]::-webkit-search-decoration,
input[type="search"]::-webkit-search-cancel-button,
input[type="search"]::-webkit-search-results-button,
input[type="search"]::-webkit-search-results-decoration { display: none; }
</style>
</head>
<body>
    <div class="search">
        <input type="search" placeholder="Enter the printer name" /> 
        <button>Search</button>
        <div class="recent"></div>
    </div>
    <div id="results"></div>
    <div id="mask"></div>
</body>
<script type="application/javascript">
var app = {
    lastSearch: '',
    init: function() {
        app.input = document.querySelector(".search input");
        app.input.addEventListener("keyup", app.keyHandler);
        app.input.focus();
        app.button = document.querySelector(".search button");
        app.button.addEventListener("click", app.keyHandler);
        app.recent = document.querySelector(".search .recent");
        app.results = document.getElementById('results');
        app.mask = document.getElementById('mask');
        app.showHistory();
    },
    reset: function() {
        app.results.innerHTML = app.lastSearch = app.input.value = '';
    },
    search: function(str) {
        app.recent.innerHTML = '';
        setTimeout(app.showHistory, 100);
        if (!str || app.lastSearch === str) return;
        app.input.value = app.lastSearch = str;
        var cache = localStorage.getItem(str);
        if (cache) app.showData(JSON.parse(cache));
        else app.apiCall(str);
    },
    clearHistory: function() {
        localStorage.clear();
        app.recent.innerHTML = '';
    },
    showHistory: function() {
        var html = '';
        for ( var i = 0, len = localStorage.length; i < len; ++i ) {
            html += '<div class="term" onclick="app.keyHandler(this)">'+localStorage.key(i)+'</div>';
        }
        if (html) html += '<div class="clear" onclick="app.keyHandler(this)">Clear History</div>';
        app.recent.innerHTML = html;
    },
    storeData: function(data) {
        if (data && data.name && data.details.length > 0) {
            localStorage.setItem(data.name, JSON.stringify(data));
        }
    },
    keyHandler: function(e) {
        if (e.innerText) {
            if (e.className === 'term') app.search(e.innerText);
            else if (e.className === 'clear') app.clearHistory();
        } else if (e.type === 'click') app.search(app.input.value);
        else if (e.key === 'Enter') app.search(e.target.value);
        else if (e.key === 'Escape') app.reset();
    },
    showData: function(data) {
        if (data.details.length < 1) {
            app.results.innerHTML = '<div class="subtitle">No standard toners were found for this model</div>';
            return;
        }
        var html = '<div class="title">'+data.name+' ('+data.type+' printer)</div>';
        if(data.type === 'monochrome') {
            if(data.cost) html += '<div class="subtitle">Standard Yield Toner Cost: $' + data.cost + '</div>';
            if(data.yeld) html += '<div class="subtitle">Black Toner Yield: ' + data.yeld + ' impressions</div>';
        } else {
            if(data.cost) html += '<div class="subtitle">Standard Yield Black Toner Cost: $' + data.cost + '</div>';
            if(data.yeld) html += '<div class="subtitle">Black Toner Yield: ' + data.yeld + ' impressions</div>';
            if(data.ccost) html += '<div class="subtitle">Color Standard Yield cartridge cost: ' + data.ccost + '</div>';
            if(data.cyeld) html += '<div class="subtitle">Color cartridge yield: ' + data.cyeld + ' impressions</div>';
        }
        html += '<div class="subtitle">Details:</div>';
        data.details.forEach(function(d) {
            html += '<div class="details '+d.color+'">' + d.name + ' ($'+d.cost+
                (d.yeld ? ' / '+d.yeld+' impressions' : '') + ')</div>';
        });
        app.results.innerHTML = html;
    },
    parse: function(obj) {
        var ret = {name:'',type:'monochrome',cost:0,yeld:0,ccost:0,cyeld:0,details:[]};
        if (obj.originalQuery) ret.name = obj.originalQuery;
        obj.products.forEach(function(p) { 
            var p = app.parseProduct(p);
            if (p && ret.details.filter(function(d) { return d.color === p.color;}).length < 1) {
                ret.details.push(p);
            }
        });
        ret.details.forEach(function(p) {
            if (p.color === 'black') {
                if (ret.cost === 0) ret.cost = p.cost;
                if (ret.yeld === 0) ret.yeld = p.yeld;
            } else {
                ret.type = 'color';
                if (ret.ccost === 0) ret.ccost = p.cost;
                if (ret.cyeld === 0) ret.cyeld = p.yeld;
            }
        });
        return ret;
    },
    parseProduct: function(p) {
        var standard = false, ret = {name:p.title,color:'black',yeld:0,cost:0};
        if (p.title.match(/Tri-Color/)) ret.color = 'tri-color';
        if (p.priceValue) ret.cost = p.priceValue;
        else if (p.price) ret.cost = parseFloat(p.price.replace(/[^0-9.-]+/g, ''));
        if (p.inkAndTonerDetails) p.inkAndTonerDetails.forEach(function(d) {
            if (d.yieldColor && !ret.color) ret.color = d.yieldColor.toLowerCase();
            if (d.yieldType && d.yieldType.match(/Standard/)) standard = true;
            if (d.yieldPerPage) ret.yeld = parseInt(d.yieldPerPage.replace(/[^0-9]+/g, ''));
        });
        if (p.description.specification) p.description.specification.forEach(function(s){
            if (s.name.match(/Cartridge Yield Type/) && s.value.match(/^Standard/)) standard = true;
            else if (s.name === 'Ink or Toner Color' || s.name === 'True Color') ret.color = ret.color === 'black' ? s.value.toLowerCase() : ret.color;
            else if (s.name.match(/^(Page Yield|Yield per Cartridge)/) && ret.yeld === 0) ret.yeld = parseInt(s.value.replace(/[^0-9]+/g, ''));
        });
        // Post-processing:
        if (p.title.match(/High Yield/)) standard = false;
        console.log(standard,{p},{ret});
        if (!standard) return null;
        if (ret.color.match('cyan/magenta/yellow')) ret.color = 'tri-color';
        return ret;
    },
    apiCall: function(str) {
        app.recent.innerHTML = '';
        app.mask.className = 'loading';
        var req = new XMLHttpRequest();
        req.addEventListener('load', function() {
            try {
                var data = app.parse(JSON.parse(this.responseText));
                app.storeData(data);
                app.showData(data);
            } catch(e) {
                alert('Failed to search: API wrong reply' + e);
                console.log(this.responseText);
            }
        });
        req.addEventListener('readystatechange', function(e) {
            if (this.readyState === XMLHttpRequest.DONE) {
                app.mask.className = '';
                app.showHistory();
            }
        });
        req.addEventListener('error', function() {
            app.input.value = '';
            alert('Failed to search: API Error');
        });
        req.open("GET", window.location.href + '?term=' + escape(str));
        req.send();
    }
};
onload = app.init;
</script>
</html>