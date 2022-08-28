<?php
/**
 * PrinterProject - Search for the OEM toner and information using staples.com API
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
    $ref = "https://www.staples.com/{$term}/directory_{$term}";
    $header = ["Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5",
        "Cache-Control: max-age=0", "Connection: keep-alive", "Keep-Alive: 300", "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7",
        "Accept-Language: en-us,en;q=0.5","Pragma: "];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $uri);
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:7.0.1) Gecko/20100101 Firefox/7.0.12011-10-16 20:23:00");
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_REFERER, $ref);
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
    <title>OEM Tonner Search</title>
    <style>
body {
    margin: 0;
    font-family: "Helvetica", sans-serif;
    font-size: 16px;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}
input[type=text]::-ms-clear { display: none; width : 0; height: 0; }
input[type=text]::-ms-reveal { display: none; width : 0; height: 0; }
input[type="search"]::-webkit-search-decoration,
input[type="search"]::-webkit-search-cancel-button,
input[type="search"]::-webkit-search-results-button,
input[type="search"]::-webkit-search-results-decoration { display: none; }

#results { display: flex; justify-content: space-between; flex-wrap: wrap;}
.card {
    display: flexbox;
    margin: 16px;
    min-width: 280px;
    min-height: 180px;
    font-size: 16px;
    background-color: #ffffff;
    border-radius: 2px;
    box-shadow: 0 12px 15px 0 rgba(0, 0, 0, 0.24);
}
.card .heading {
    background-color: #000;
    font-size: 20px;
    padding: 8px;
    font-weight: bold;
    height: 22px;
    color: #ffffff;
}
.card.cyan .heading {background-color: rgb(0, 145, 145);}
.card.magenta .heading {background-color: rgb(152, 0, 152);}
.card.yellow .heading {background-color: rgb(213, 213, 0);}
.card .content {
    padding: 16px;
}
.printer {font-size: 20px; padding-top: 16px; padding-left: 20px; font-weight: bold; width: 100%;}
.search {
    display: block;
    margin: 16px;
    margin-right: 8px;
    border: none;
}
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
    height: 0;
    width: calc(100% - 32px);
    position: fixed;
    overflow: hidden;
    background-color: #ffffff;
}
.search:hover .recent {
    z-index: 100;
    opacity: 1;
    height: auto;
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

.loading-mask {
  position: absolute;
  height: 100%;
  width: 100%;
  background-color: #5c5c5c;
  bottom: 0;
  left: 0;
  right: 0;
  top: 0;
  z-index: 9999;
  opacity: 0.4;
}
.loading-mask:before {
  content: "";
  background-color: rgba(0, 0, 0, 0);
  border: 5px solid rgba(0, 183, 229, 0.9);
  opacity: 0.9;
  border-right: 5px solid rgba(0, 0, 0, 0);
  border-left: 5px solid rgba(0, 0, 0, 0);
  border-radius: 50px;
  box-shadow: 0 0 35px #2187e7;
  width: 50px;
  height: 50px;
  -moz-animation: spinPulse 1s infinite ease-in-out;
  -webkit-animation: spinPulse 1s infinite linear;
  margin: -25px 0 0 -25px;
  position: absolute;
  top: 50%;
  left: 50%;
}
.loading-mask:after {
  content: "";
  background-color: rgba(0, 0, 0, 0);
  border: 5px solid rgba(0, 183, 229, 0.9);
  opacity: 0.9;
  border-left: 5px solid rgba(0, 0, 0, 0);
  border-right: 5px solid rgba(0, 0, 0, 0);
  border-radius: 50px;
  box-shadow: 0 0 15px #2187e7;
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
    </style>
</head>

<body>
    <div class="search">
        <input type="search" placeholder="Enter the printer name" /> 
        <button>Search</button>
        <div class="recent">
            <div class="term" onclick="app.keyHandler(this)">HP LaserJet Pro M404</div>
            <div class="term" onclick="app.keyHandler(this)">HP Color LaserJet CP3525</div>
            <div class="term" onclick="app.keyHandler(this)">HP Photosmart C4272</div>
            <div class="clear" onclick="app.keyHandler(this)">Clear History</div>
        </div>
    </div>
    
    <div id="results">
        <div class="printer">Printer Name</div>
        <div class="card">
            <div class="heading">Black</div>
            <div class="content">Text</div>
        </div>
        <div class="card cyan">
            <div class="heading">Hello</div>
            <div class="content">Text</div>
        </div>
        <div class="card magenta">
            <div class="heading">Magenta</div>
            <div class="content">Text</div>
        </div>
        <div class="card yellow">
            <div class="heading">Yellow</div>
            <div class="content">Text</div>
        </div>
    </div>
</body>
<script type="application/javascript">
var app = {
    lastSearch: '',
    init: function() {
        app.input = document.querySelector(".search input");
        app.input.addEventListener("keyup", app.keyHandler);
        app.button = document.querySelector(".search button");
        app.button.addEventListener("click", app.keyHandler);
        app.recent = document.querySelector(".search .recent");
        // populate app.recent from localStorage
    },
    reset: function() {
        app.lastSearch = '';
        app.input.value = '';
    },
    mask: function(on) {
        document.body.className = on ? 'loading-mask' : '';
    },
    search: function(str) {
        if(str && app.lastSearch !== str) app.apiCall(str);
    },
    clearHistory: function() {
        localStorage.clear();
        app.recent.innerHTML = '';
    },
    keyHandler: function(e) {
        if(e.innerText) {
            if(e.className === 'term') app.search(e.innerText);
            else if(e.className === 'clear') app.clearHistory();
        } else if(e.type === 'click') app.search(app.input.value);
        else if(e.key === 'Enter') app.search(e.target.value);
        else if(e.key === 'Escape') app.reset();
    },
    apiCall: function(str) {
        app.mask(true);
        app.input.value = app.lastSearch = str;
        var req = new XMLHttpRequest();
        req.addEventListener('load', function() {
            try {
                app.response = JSON.parse(this.responseText);
            } catch(e) {
                alert('Failed to search: API wrong reply' + e);
                console.log(this.responseText);
            }
            console.log(app.response);
        });
        req.addEventListener('readystatechange', function(e) {
            if(this.readyState === XMLHttpRequest.DONE) app.mask(false);
        });
        req.addEventListener('error', function() {
            app.input.value = '';
            alert('Failed to search: API Error');
        });
        req.open("GET", window.location.href + '?term' + escape(str));
        req.send();
    }
};
onload = app.init;
</script>
</html>