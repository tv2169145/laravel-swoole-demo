<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
              rel="stylesheet"
              integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
              crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/notie/4.3.1/notie.min.css"
              integrity="sha512-UrjLcAek5jbj1vwGbXkviPHtgSNVNQCedX7cBIMDdSI2iZtUcZcoTh2Sqc8R9mVcijOjFUi1IlxhfrE1uWaIog=="
              crossorigin="anonymous"
              referrerpolicy="no-referrer"/>
        <title>Home</title>
        <style>
            .chatbox {
                outline: 1px solid silver;
                min-height: 160px;
                padding: 0.5em;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1 class="mt-3">Home page</h1>
                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <h3>Message</h3>
                    <p><a href="https://github.com">go to github</a></p>
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input class="form-control" type="text" name="username" id="username" autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label for="message">Message:</label>
                        <input class="form-control" type="text" name="message" id="message" autocomplete="off">
                    </div>
                    <hr>
                    <a href="javascript:void(0);" class="btn btn-outline-secondary" id="sendBtn">Send Message</a>
                    <input type="hidden" name="action" id="action">

                    <div id="status" class="mt-2 float-end">

                    </div>

                    <div id="output" class="chatbox mt-3">

                    </div>

                </div>

                <div class="col-md-4">
                    <h3>Online:</h3>
                    <ul id="online_users">

                    </ul>
                </div>
            </div>
        </div>
    </body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/notie/4.3.1/notie.min.js" integrity="sha512-NHRCwRf2LnVSlLDejCA9oS3fG3/FLSQIPCjAWl3M7tVi5wszwr6FxkjotWnQDXLE+aLKcxRrzFDNEgXj9nvkPw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
{{--    <script src="https://cdn.jsdelivr.net/npm/reconnecting-websocket@4.4.0/dist/reconnecting-websocket-cjs.min.js"></script>--}}
    <script>
        let socket = null;
        let outputDom = document.getElementById("output");
        let usernameField = document.getElementById("username");
        let messageField = document.getElementById("message");

        window.onbeforeunload = function() {
            console.log("Leaving");
            let jsonData = {};
            jsonData["action"] = "left";
            socket.send(JSON.stringify(jsonData));
        };

        document.addEventListener("DOMContentLoaded", function() {
            //設定自動重連ws
            socket = new WebSocket("ws://192.168.51.84:1215/");
            // socket = new WebSocket("ws://laravel-swoole.test:1215");
            // socket = new ReconnectingWebSocket("ws://laravel-swoole.test:1215", null, {debug: true, reconnectInterval: 3000});

            const offline = `<span class="badge bg-secondary">Not connected</span>`;
            const online = `<span class="badge bg-success">Connected</span>`;
            let statusDiv = document.getElementById("status");

            socket.onopen = () => {
                console.log("Successfully connected");
                statusDiv.innerHTML = online;
            };
            socket.onclose = () => {
                console.log("connection closed");
                statusDiv.innerHTML = offline;
            };
            socket.onerror = () => {
                console.log("have error");
                statusDiv.innerHTML = offline;
            };
            socket.onmessage = (msg) => {
                let j = JSON.parse(msg.data);
                console.log(j)

                // let data = JSON.parse(msg.data);
                // console.log("Action is", data.action);
                switch (j.event) {
                    case "list_users":
                        let ul = document.getElementById("online_users");
                        while (ul.firstChild) {
                            ul.removeChild(ul.firstChild);
                        }
                        let jData = JSON.parse(j.data);
                        console.log(jData['connected_users']);
                        if (jData['connected_users'].length > 0) {
                            jData['connected_users'].forEach(function(item) {
                                let li = document.createElement("li");
                                li.appendChild(document.createTextNode(item));
                                ul.appendChild(li);
                            })
                        }
                        break;

                    case "broadcast":
                        console.log(333)
                        console.log(j.data)
                        outputDom.innerHTML = outputDom.innerHTML + j.data + "<br>";
                        break;
                }
            };

            // 當有輸入username時, 更新在線列表
            usernameField.addEventListener("change", function () {
                let jsonData = {};
                jsonData.event = "username";
                jsonData.data = this.value;
                // jsonData.push("username", this.value);
                // console.log("json data:", JSON.stringify(jsonData))
                socket.send(JSON.stringify(jsonData))
            });

            // 發送訊息事件(允許enter發送)
            messageField.addEventListener("keypress", function (event) {
                if (event.code === "Enter") {
                    if (!socket) {
                        console.log("no connection");
                        return false;
                    }
                    event.preventDefault();
                    event.stopPropagation();
                    if (usernameField.value === "" || messageField.value === "") {
                        errorMessage("username or message is empty!");
                        return false
                    }
                    sendMessage();
                }
            });

            // send message按鈕發送訊息
            document.getElementById("sendBtn").addEventListener("click", function (event) {
                event.preventDefault();
                event.stopPropagation();
                if (usernameField.value === "" || messageField.value === "") {
                    errorMessage("username or message is empty!");
                    return false
                }
                sendMessage();
            });
        });

        function sendMessage() {
            let jsonData = {};
            jsonData.event = "broadcast";
            jsonData.data = {
                username: usernameField.value,
                message: messageField.value,
            }

            socket.send(JSON.stringify(jsonData));

            messageField.value = "";
        }

        function errorMessage(msg) {
            notie.alert({
                type: "error", // optional, default = 4, enum: [1, 2, 3, 4, 5, 'success', 'warning', 'error', 'info', 'neutral']
                text: msg,
                // stay: Boolean, // optional, default = false
                time: 2, // optional, default = 3, minimum = 1,
                position: "top" // optional, default = 'top', enum: ['top', 'bottom']
            });
        }
    </script>
</html>
