<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Basic styling for the chatbot */
        #chatbox {
            position: fixed;
            bottom: 0;
            right: 20px;
            width: 300px;
            height: 400px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }
        #chatbox-header {
            background-color: #007BFF;
            color: #fff;
            padding: 10px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        #chatbox-messages {
            flex: 1;
            padding: 10px;
            overflow-y: auto;
        }
        #chatbox-input {
            display: flex;
            border-top: 1px solid #ddd;
        }
        #chatbox-input input {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 0 0 0 10px;
        }
        #chatbox-input button {
            padding: 10px;
            border: none;
            background-color: #007BFF;
            color: #fff;
            border-radius: 0 0 10px 0;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div id="chatbox">
        <div id="chatbox-header">Chatbot</div>
        <div id="chatbox-messages"></div>
        <div id="chatbox-input">
            <input type="text" id="user-message" placeholder="Type your message...">
            <button id="send-button">Send</button>
        </div>
    </div>
    <script>
        // JavaScript to handle chatbot functionality
        document.getElementById('send-button').addEventListener('click', function() {
            var message = document.getElementById('user-message').value;
            if (message.trim() === '') return;

            // Display user message
            var messagesDiv = document.getElementById('chatbox-messages');
            var userMessageDiv = document.createElement('div');
            userMessageDiv.textContent = 'You: ' + message;
            messagesDiv.appendChild(userMessageDiv);

            // Clear input field
            document.getElementById('user-message').value = '';

            // Send message to chatbot
            fetch('chatbot_response.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: message })
            })
            .then(response => response.json())
            .then(data => {
                var botMessageDiv = document.createElement('div');
                botMessageDiv.textContent = 'Chatbot: ' + data.response;
                messagesDiv.appendChild(botMessageDiv);
                messagesDiv.scrollTop = messagesDiv.scrollHeight;  // Scroll to bottom
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>
