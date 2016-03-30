var CConfigClass = (function(api_url, app_info, options){

    /* Validation */
    options = options || {};

    /* Private variables */
    var data = {},
        localStorageKey = options.local_storage_key || 'config',
        refreshSeconds  = options.refresh_seconds   || false,
        onFirstSuccess  = options.first_success     || function(){},
        onFirstError    = options.first_error       || function(){},
        onEachSuccess   = options.each_success      || function(){},
        onEachError     = options.each_error        || function(){},
        debug           = options.debug             || false
        ;

    /* Private functions */
    function reloadServerData(_onSuccess, _onError){
        loadJSON({
            url: api_url,
            data: app_info,
            success: function(response){
                saveLocally(response);
                if(typeof _onSuccess === 'function'){
                    _onSuccess();
                }
                if(debug){
                    console.log('Configs load success.', response);
                }
            },
            error: function(){
                if(typeof _onError === 'function'){
                    _onError();
                }
                if(debug){
                    console.error('Configs load failed.');
                }
            }
        });
    }

    function loadJSON(params) {
        var xmlhttp;

        if (window.XMLHttpRequest) {
            xmlhttp = new XMLHttpRequest();//IE7+, Firefox, Chrome, Opera, Safari
        } else {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");//IE6, IE5
        }

        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == XMLHttpRequest.DONE ) {
                if(xmlhttp.status == 200){
                    try{
                        if(typeof xmlhttp.responseText === 'string'){
                            xmlhttp.responseText = JSON.parse(xmlhttp.responseText);
                        }
                        params.success(xmlhttp.responseText);
                    } catch (err) {
                        params.error(xmlhttp);
                    }
                } else {
                    params.error(xmlhttp);
                }
            }
        };

        function jsonToQueryString(json) {
            return '?' +
                Object.keys(json).map(function(key) {
                    return encodeURIComponent(key) + '=' +
                        encodeURIComponent(json[key]);
                }).join('&');
        }
        xmlhttp.open("GET", params.url+jsonToQueryString(params.data), true);
        xmlhttp.send();
    }

    function saveLocally (data) {
        localStorage.setItem(localStorageKey, data);
    }

    function refreshLocalData(){
        var configs = localStorage.getItem(localStorageKey);
        if(configs){
            try{
                data = JSON.parse(configs);
            } catch (err){
                data = {};
                localStorage.setItem(localStorageKey, '{}');
                console.error(err);
            }
        }
    }

    function setPreBuild(dataIn){
        data = dataIn || {};
        saveLocally(data);
    }

    function getConfig(path, fallbackValue){
        refreshLocalData();
        if(!path){
            return data;
        }
        var parts = path.split('.');
        var prev = data;
        for(var i=0; i<parts.length; i++){
            var part = parts[i];
            var current = prev;
            if(typeof current[part] !== "undefined"){
                prev = current[part];
                continue;
            } else {
                return fallbackValue;
            }
        }
        return prev;
    }

    /* Class initialization */
    reloadServerData(onFirstSuccess, onFirstError);
    if(refreshSeconds){
        setInterval((function(){
            reloadServerData(onEachSuccess, onEachError);
        }).bind(this), refreshSeconds*1000);
    }

    /* Public */
    return {
        reload: reloadServerData,

        set: setPreBuild,

        get: getConfig
    };

});
