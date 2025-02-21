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
  
  <script src="https:
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
    var patientId = <?php echo $_GET['patient_id']?>;
    var patientName = '<?php echo $_GET['patientName']?>';
    var patientProfile = '<?php echo $_GET['patientProfile']?>';
    
    $('.head-container').html(
      '<img id="back" src="../pic/back.png" alt="back"> <img id="docimg" src="' + patientProfile + '" alt="patientimage"><h4>' + patientName + '</h4>'
    );

    
    
    function loadMessages(patientId) {
      $.ajax({
        url: 'load_messages.php?patient_id=' +patientId, 
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

    
    loadMessages(patientId);

    
    $('#send').on('click', function() {
      var message = $('#message').val();
      if (message.trim() !== '') {
        $.ajax({
          url: 'send_message.php', 
          type: 'POST',
          data: { message: message, patient_id: patientId },
          success: function() {
            $('#message').val(''); 
            loadMessages(patientId); 
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
        url: 'load_patients.php', 
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
