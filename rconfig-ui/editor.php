<?php
require_once ('./libs/phpFileTree/php_file_tree.php');
define('CONFIG_FILES_PATH', './../files/');

?>

<?php
function printRConfigEditor (){
?>
    <link href="./libs/phpFileTree/styles/default/default.css" rel="stylesheet" />
    <script src="./libs/nanoajax.js"></script>
    <script src="./libs/phpFileTree/php_file_tree.js"></script>

    <link rel="stylesheet" href="http://bootswatch.com/sandstone/bootstrap.min.css" media="screen" title="no title" charset="utf-8">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.11.1/jquery.min.js" charset="utf-8"></script>
    <script type="text/javascript">
        var app_info = {
            version:"",
            client: "",
            channel: "", //['production', 'staging', 'dev']
        };
    </script>
    <script src="../rconfig.js" charset="utf-8"></script>

    <style>
        li.pft-directory ul{
            padding-left:20px;
        }
        .code { white-space: pre; font-family: monospace;}
        #response-wrapper {
            padding:20px;
            border:1px solid #a0a0a0;
            background: #f0f0f0;
            border-radius: 5px;
        }
    </style>
    <script>
        window.current_editin_file_link = null;
        function readConfig(link) {
            current_editin_file_link = link;
            nanoajax.ajax({url:'./read.php?file='+link}, function (code, responseText) {
                var ugly = responseText;
                var obj = JSON.parse(ugly);
                var pretty = JSON.stringify(obj, undefined, 4);
                document.getElementById('myTextArea').value = pretty;
            });
        }

        function writeConfig(){
            console.log(current_editin_file_link);
            var content = document.getElementById('myTextArea').value;
            nanoajax.ajax({url:'./write.php', method: 'POST', body: 'file='+current_editin_file_link+'&content='+content}, function (code, responseText) {
                alert(responseText);
                readConfig(current_editin_file_link);
            });
        }
    </script>

    <script type="text/javascript">

        window.url = '../';

        var Config = RConfig(url, app_info, {
            first_success: function(){
                console.log('first_success');
            },
            first_error: function(){
                console.log('first_error');
            },
            each_success: function(){
                console.log('each_success');
            },
            each_error: function(){
                console.log('each_error');
            },
            refresh_seconds: 3,
            debug: true
        });

        function update(){
            url = $('#url').val();
            app_info.client = $('#client').val();
            app_info.version = $('#version').val();
            app_info.channel = $('#channel').val();

            Config.reload();

            $.ajax({
                url: url,
                data: app_info,
                dataType: "json",
                type: "GET",
                crossDomain: true,
                success: function (response) {
                    //console.log(response);
                    window.response = response;
                    $('#response').text(JSON.stringify(response, null, 4));
                },
                error: function (xhr, status) {
                    $('#response').text("Error: "+xhr.statusText);
                }
            });
        }

        $(document).ready(function(){
            $('#url').val(url);

            update();

            $('input.keyup').keyup(function(){
                update();
            });
        });

    </script>
    <div style="width:100%;box-sizing:border-box;padding:20px;">
        <table border="0" style="width:100%;">
            <tr>
                <td style="width: 250px;vertical-align: top;">
                    <h4>Editor</h4>
                    <?php echo php_file_tree(CONFIG_FILES_PATH, "javascript:readConfig('[link]');"); ?>
                </td>
                <td style="vertical-align: top;">
                    <textarea class="form-control" id="myTextArea" style="background-color: #454545;color:#ffffff;width:100%;min-height: 300px;"></textarea>
                    <br />
                    <input type="button" value="Save" class="btn btn-primary btn-small" style="margin-top:10px;" onclick="writeConfig()" />
                </td>
            </tr>
            <tr>
                <td colspan="2" style="vertical-align: top;padding-top:20px;">
                    <hr />
                    <h4>Debugger</h4>
                    <div class="form-group hidden" style="display: none;">
                        <label for="url" class="col-lg-2 control-label">URL</label>
                        <div class="col-lg-10">
                            <input type="text" class="keyup form-control" id="url" placeholder="http://example.com/rconfig" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="client" class="col-md-2 control-label">Client</label>
                        <div class="col-md-10">
                            <input type="text" class="keyup form-control" id="client" placeholder="ios" value="ios">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="version" class="col-md-2 control-label">Version</label>
                        <div class="col-md-10">
                            <input type="text" class="keyup form-control" id="version" placeholder="2.0.0" value="1.0.0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="channel" class="col-md-2 control-label">Channel</label>
                        <div class="col-md-10">
                            <input type="text" class="keyup form-control" id="channel" placeholder="prod" value="prod">
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="vertical-align: top;padding-top:20px;">
                    <div id="response-wrapper">
                        <code id="response" class="code"></code>
                    </div>
                </td>
            </tr>
        </table>
    </div>

<?php } ?>

