function openWebSocket(topicId) {
  conn = new WebSocket("ws://localhost:8000?access_token=" + topicId);
  conn.onopen = function (e) {
    console.log("Connection established!");
  };

  // Onmessage
  conn.onmessage = function (e) {
    const data = JSON.parse(e.data);

    if ("users" in data) {
      updateUsers(data.users);
    } else {
      newMessage(data); // message on the left
    }
  };
}

// Send chat message
$(document).on("submit", CHAT_MESSAGE_FORM, function (e) {
  e.preventDefault();
  const form = $(this);
  const message = form.find("#messageInput").val();

  // Send the message
  if (conn.readyState === WebSocket.OPEN) {
    // Send the message
    conn.send(message);
    myMessage(message); // message on the right
  } else {
    console.error("WebSocket connection is not open yet.");
  }
});

// New Message
    function newMessage(data) {
        const chatMessage = `
            <div class="flex justify-start items-start gap-3 mb-3">
                <div class="w-[40px] h-[40px] rounded-full bg-gray-100 flex justify-center items-center">
                    <ion-icon name="person-circle-outline" class="text-[30px] text-gray-500"></ion-icon>
                </div>
                <div class="flex flex-col gap-1">
                    <h1 class="text-md font-bold text-gray-500">${data.from}</h1>
                    <p class="text-gray-600">${data.message}</p>
                </div>
            </div>
        `;

        $(RENDER_CHATS).append(chatMessage);
        $(RENDER_CHATS).animate({ scrollTop: $(RENDER_CHATS).prop("scrollHeight")}, 1000);
    }

    // My Message
    function myMessage(message) {
        var name = '<?= $userName ?>'
        var imgs = '<?= $userImage ?>'
        var date = new Date;
        var minutes = date.getMinutes();
        var hour = date.getHours();
        var time = hour + ':' + minutes;
        const chatMessage = `
            <div class="flex justify-end items-start gap-3 mb-3">
                <div class="flex flex-col gap-1">
                    <h1 class="text-md font-bold text-gray-500">${name}</h1>
                    <p class="text-gray-600">${message}</p>
                </div>
                <div class="w-[40px] h-[40px] rounded-full bg-gray-100 flex justify-center items-center">
                    <img src="${imgs}" alt="user-image" class="w-[40px] h-[40px] rounded-full object-cover">
                </div>
            </div>
        `;

        $(RENDER_CHATS).append(chatMessage);
        $(RENDER_CHATS).animate({ scrollTop: $(RENDER_CHATS).prop("scrollHeight")}, 1000);
    }

    // Update user
    function updateUsers(users) {
        var html = ''
        var myId = $userId;
        
        for (let index = 0; index < users.length; index++) {
            if(myId != users[index].user_id)
                html += `<div class="flex justify-start items-center gap-3 mb-3">
                            <div class="w-[40px] h-[40px] rounded-full bg-gray-100 flex justify-center items-center">
                                <ion-icon name="person-circle-outline" class="text-[30px] text-gray-500"></ion-icon>
                            </div>
                            <div class="flex flex-col gap-1">
                                <h1 class="text-md font-bold text-gray-500">${users[index].name}</h1>
                                <p class="text-gray-600">${users[index].status}</p>
                            </div>
                        </div>`
        }

        if(html == ''){
            html = `
                <div class="h-[200px] w-full flex justify-center items-center gap-3 bg-cyan-100 rounded-md">
                    <p class="text-center text-gray-500 roboto">No user available</p>
                </div>`;
        }
        

        $('#user-list').html(html)
        
    }