<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Doctor Communication</title>
  <style>
    
    .chat-container {
      max-width: 600px;
      margin: 0 auto;
      font-family: Arial, sans-serif;
    }

    .chat-messages {
      border: 1px solid #ccc;
      padding: 10px;
      height: 300px;
      overflow-y: scroll;
      margin-bottom: 10px;
    }

    .message {
      margin-bottom: 10px;
      padding: 8px;
      border-radius: 5px;
      max-width: 80%;
    }

    .message-left {
      background-color: #f1f1f1;
      text-align: left;
      align-self: flex-start;
    }

    .message-right {
      background-color: #d6e6ff;
      text-align: right;
      align-self: flex-end;
    }

    .message-time {
      font-size: 0.8em;
      color: #666;
      margin-top: 5px;
    }

    .chat-input {
      display: flex;
      justify-content: space-between;
    }

    .chat-input input[type="text"] {
      flex: 1;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 5px;
      margin-right: 10px;
    }

    .chat-input button {
      padding: 8px 20px;
      border: none;
      border-radius: 5px;
      background-color: #4caf50;
      color: white;
      cursor: pointer;
    }

    .chat-input button:hover {
      background-color: #45a049;
    }
  </style>
    <style>
        .head-container {
            border: 1px solid #ccc;
            border-radius: 8px;
            margin: 0px;
            padding: 5px;
            background-color: #fff;
            display:flex;
        }
        h4 {
            margin-top: 12px;
            margin-left: 20px;
            margin-bottom: 0px;
        }

        #docimg {
            max-width: 100%;
            max-height: 40px;
            border-radius: 60%;
        }
        #back{
          margin: 10px;
          max-width: 100%;
          max-height: 20px;
          border-radius: 30%;
        }
        #back:hover{
            background-color: #f9f9f9;
        }
    </style>
  
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>

<div class="chat-container">
  <div class="head-container"></div>
  <div class="chat-messages" id="chat-messages">
    
  </div>
  <div class="chat-input">
    <input type="text" id="message" placeholder="Type your message">
    <button id="send">Send</button>
  </div>
</div>
<script>
$(document).ready(function() {
    var doctorId = <?php echo $_GET['doctor_id'] ?>;
    var doctorName = '<?php echo $_GET['doctorName'] ?>';
    var doctorProfile = '<?php echo $_GET['doctorProfile'] ?>';

    $('.head-container').html(
      '<img id="back" src="../pic/back.png" alt="back"> <img id="docimg" src="' + doctorProfile + '" alt="doctorimage"><h4> Dr. ' + doctorName + '</h4>'
      );

        
    function loadMessages(doctorId) {
      $.ajax({
        url: 'load_messages.php?doctor_id=' + doctorId, 
        type: 'GET',
        success: function(data) {
          $('#chat-messages').html(data); 
          
          $(".chat-messages").scrollTop($(".chat-messages")[0].scrollHeight);
        },
        error: function() {
          $('#chat-messages').html('Failed to load messages.');
        }
      });
    }

    
    loadMessages(doctorId);

    
    $('#send').on('click', function() {
      var message = $('#message').val();
      if (message.trim() !== '') {
        $.ajax({
          url: 'send_message.php', 
          type: 'POST',
          data: { message: message, doctor_id: doctorId },
          success: function() {
            $('#message').val(''); 
            loadMessages(doctorId); 
          },
          error: function() {
            $('#chat-messages').html('Failed to send message.');
          }
        });
      }
    });

    
    $('#message').keypress(function(e) {
      if (e.which === 13) {
        $('#send').click();
      }
    });
    $('#back').on('click', function() {
        
        $.ajax({
          url: 'load_doctors.php', 
          type: 'GET',
          success: function(data) {
            $('#main-content').html(data); 
          },
          error: function() {
            $('#main-content').html('Failed to load patients.'); 
          }
        });
      });
  });
</script>

</body>
</html>
