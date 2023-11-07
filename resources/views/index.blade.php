<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat App</title>

    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

    <link rel="stylesheet" href="/style.css">
</head>

<body>
    <div class="chat">
        <div class="top">
            <img src="img/cartoonProfile.png" alt="User Picture">
            <br><br>
            <p>User</p>
            <small>Online</small>
        </div>
        <div class="messages">
            @include('receive', ['message' => "Hey! What's up!"])
            @include('receive', ['message' => "Ask a friend to open this link and you can chat with them!"])
        </div>
        <div class="bottom">
            <form>
                <input type="text" id="message" name="message" placeholder="Enter message...">
                <button type="submit"></button>
            </form>
        </div>
    </div>
</body>
<script>
    const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key')}}', {cluster: 'ap1'});
    const channel = pusher.subscribe('public');

    //Receive messages
    channel.bind('chat', function(data) {
        $.post("/receive", {
                _token: '{{csrf_token()}}',
                message: data.message,
            })
            .done(function(res) {
                console.log(res);
                $(".messages > .message").last().after(res);
                $(document).scrollTop($(document).height());
            });
    });

    //Broadcast messages
    $("form").submit(function(event) {
        event.preventDefault();

        $.ajax({
            url: "/broadcast",
            method: 'POST',
            headers: {
                'X-Socket-Id': pusher.connection.socket_id
            },
            data: {
                _token: '{{csrf_token()}}',
                message: $("form #message").val(),
            }
        }).done(function(res) {
            console.log(res);
            $(".messages > .message").last().after(res);
            $("form #message").val('');
            $(document).scrollTop($(document).height());
        });
    });
</script>

</html>