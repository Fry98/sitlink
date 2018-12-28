// Initial declarations
let currChan = 0;
let flwList;
let flwTab = false;
let chanName = chans[0];
let reader = new FileReader();
let lastId = 0;
let skip = 0;
let lastMsg = false;
let scrollDeac = true;
let updateLoop;
let XHRPool = [];
const MESSAGE_LIMIT = 30;

// Inital page setup
updateFollowToggle();
fetchMessages(true);
resetUpdateLoop();

// Message polling request
function resetUpdateLoop() {
  clearInterval(updateLoop);
  setTimeout(
    updateLoop = setInterval(() => {
      if (XHRPool.length === 0) {
        $.ajax(`/~tomanfi2/api/update.php?sub=${sub}&chan=${chanName}&last=${lastId}`, {
          method: 'GET',
          beforeSend (xhr) {
            XHRPool.push(xhr);
          },
          success(res) {
            const msgArr = JSON.parse(res);
            if (msgArr.length > 0) {
              insertMessages(msgArr, false, true);
              $('#content')[0].scrollTop = $('#content')[0].scrollHeight;
              lastId = msgArr[msgArr.length - 1].id;
            }
          },
          error(_, status) {
            if (status !== 'abort') {
              location.reload();
            }
          },
          complete(xhr) {
            const index = XHRPool.indexOf(xhr);
            if (index !== -1) {
              XHRPool.splice(index, 1);
            }
          }
        });
      }
  }, 3000), 3000);
}

// Channel switching
$('#chans li').click(function() {
  let newIndex = $('#chans li').index(this);
  if (newIndex !== currChan) {
    $('#chans li')[currChan].classList.remove('selected');
    $(this).addClass('selected');
    $('#sidebar').removeClass('open');
    currChan = newIndex;
    chanName = chans[currChan];
    scrollDeac = true;
    $('#content').html('');
    skip = 0;
    lastMsg = false;
    lastId = 0;
    resetUpdateLoop();
    abortRequests();
    fetchMessages(true);
  }
});

// Custom textarea outline on focus
$('#msg-box textarea').focus(() => {
  $('#msg-box').css('border-color', 'rgb(28, 126, 192)');
});

$('#msg-box textarea').blur(() => {
  $('#msg-box').css('border-color', '');
});

// Image pop-up
$('body').on('click', '.chat-img', function() {
  $('#popup img')[0].src = this.src;
  $('#popup').removeClass('popup-hide');
});

$('#popup').click(function() {
  $(this).addClass('popup-hide');
});

// Toggle sidebar
$('#burger').click(() => {
  $('#sidebar').toggleClass('open');
});

// Multiline textarea handling
$('#msg').on('input', function() {
  const cont = $('#content')[0];
  let rescroll = false;
  if (cont.scrollHeight - cont.scrollTop - cont.clientHeight < 1) {
    rescroll = true;
  }
  resize(this);
  if (rescroll) {
    cont.scrollTop = cont.scrollHeight;
  }
});

// Message submission via Enter key
$('#msg').keydown(function(e) {
  if (e.keyCode === 13 && !e.shiftKey) {
    e.preventDefault();
    sendMessage();
  }
});

// Message submission via Send button
$('#submit').click(sendMessage);

// Image selector
$('#img').click(() => {
  $('#img-sel').click();
});

// Image submission
$('#img-sel').change(function() {
  const imgFile = this.files[0];
  this.value = null;
  if(!imgFile.type.includes('image')){
    alert('Selected file has to be an image');
    return;
  }
  if(imgFile.size > 2097152){
    alert('Image has to be smaller than 2MB');
    return;
  }
  reader.readAsDataURL(imgFile);
});

reader.onload = () => {
  $.ajax('/~tomanfi2/api/message.php', {
    method: 'POST',
    data: {
      sid: sub,
      chan: chanName,
      img: true,
      content: reader.result,
      last: lastId
    },
    error(_, status) {
      if (status !== 'abort') {
        location.reload();
      }
    }
  });
};

// Accessing the Subchat Menu
$('#subs').click(() => {
  $.ajax('/~tomanfi2/api/follow.php', {
    method: 'GET',
    success(res) {
      flwList = JSON.parse(res);
      updateFollows();
      $('#flw-overlay').toggleClass('flw-hide');
      $('#sidebar').removeClass('open');
    }
  });
});

$('#flw-list-close').click(() => {
  $('#flw-overlay').toggleClass('flw-hide');
});

$('#flw-overlay').click(function(e) {
  if (e.target !== this) {
    return;
  }
  $('#flw-overlay').toggleClass('flw-hide');
});

// Switching tabs in the Subchat Menu
$('#flw-button1').click(function() {
  if (!flwTab) {
    return;
  }
  $(this).addClass('flw-option-active');
  $('#flw-button2').removeClass('flw-option-active');
  flwTab = false;
  updateFollows();
});

$('#flw-button2').click(function() {
  if (flwTab) {
    return;
  }
  $(this).addClass('flw-option-active');
  $('#flw-button1').removeClass('flw-option-active');
  flwTab = true;
  updateFollows();
});

$('#flw').click(followHandler);

// Fetch previous messages
$('#content').on('scroll', function() {
  if ($(this).scrollTop() <= 0 && !scrollDeac) {
    scrollDeac = true;
    fetchMessages(false);
  }
});

// Submit message to the API endpoint
function sendMessage() {
  clearInterval(updateLoop);
  abortRequests();
  let cont = $('#msg').val();
  cont = cont.trim();
  if (cont.length === 0) {
    alert("Message can't be empty!");
    return;
  }
  $('#msg').val('');
  resize($('#msg')[0]);
  $.ajax('/~tomanfi2/api/message.php', {
    method: 'POST',
    data: {
      sid: sub,
      chan: chanName,
      img: false,
      content: cont,
      last: lastId
    },
    beforeSend(xhr) {
      XHRPool.push(xhr);
    },
    success(res) {
      const newMsg = JSON.parse(res);
      lastId = newMsg[newMsg.length - 1].id;
      insertMessages(newMsg, false, true);
      $('#content')[0].scrollTop = $('#content')[0].scrollHeight;
      if (XHRPool.length === 1) {
        resetUpdateLoop();
      }
    },
    error(_, status) {
      if (status !== 'abort') {
        location.reload();
      }
    },
    complete(xhr) {
      const index = XHRPool.indexOf(xhr);
      if (index !== -1) {
        XHRPool.splice(index, 1);
      }
    }
  });
}

// Textarea autosizing
function resize(el) {
  el.style.height = "auto";
  el.style.height = (el.scrollHeight - 8) + "px";
  const bottom = (el.clientHeight + 58) + "px";
  $('#content').css('padding-bottom', bottom);
}

// Update Follow toggle according to the current state
function updateFollowToggle() {

  // Reset all toggles
  $('#flw').removeClass();
  $('#tgl-circle').removeClass();
  $('#tgl-tick').removeClass();
  $('#tgl-cross').removeClass();
  $('#tgl-bin').removeClass();

  if (admin) {
    $('#flw').addClass('unflw');
    $('#tgl-circle').addClass('flw-idle');
    $('#tgl-bin').addClass('flw-hover');
    $('#tgl-tick').addClass('flw-invis');
    $('#tgl-cross').addClass('flw-invis');
    return;
  }

  if (followed) {
    $('#flw').addClass('unflw');
    $('#tgl-circle').addClass('flw-invis');
    $('#tgl-bin').addClass('flw-invis');
    $('#tgl-tick').addClass('flw-idle');
    $('#tgl-cross').addClass('flw-hover');
  } else {
    $('#tgl-circle').addClass('flw-idle');
    $('#tgl-bin').addClass('flw-invis');
    $('#tgl-tick').addClass('flw-hover');
    $('#tgl-cross').addClass('flw-invis');
  }
}

// Handles clicking the Follow button
function followHandler() {
  if (admin) {
    // TODO: Makes this work lol
    alert('WORK IN PROGRESS');
    return;
  }

  $.ajax('/~tomanfi2/api/follow.php', {
    method: 'POST',
    data: { sub },
    success() {
      followed = !followed;
      updateFollowToggle();
    }
  });
}

// Update DOM with the newset version of follows
function updateFollows() {
  $('#flw-list-content').html('');
  let subsToDraw;
  if (flwTab) {
    subsToDraw = flwList.owned;
  } else {
    subsToDraw = flwList.followed;
  }
  for (const item of subsToDraw) {
    $('#flw-list-content').append(`<a href='/~tomanfi2/c/${item.id}'><div class='flw-list-item'>
                                    <h1>${item.title}</h1>
                                    <div class='flw-item-desc'>${item.desc}</div>
                                  </div></a>`);
  }
}

// Insert message into the DOM
function insertMessages(msgArr, prepend, scroll) {
  for (const msg of msgArr) {
    if (msg.img) {
      if (prepend) {
        $('#content').prepend(imgTemplate(msg, scroll));
      } else {
        $('#content').append(imgTemplate(msg, scroll));
      }
    } else {
      if (prepend) {
        $('#content').prepend(textTemplate(msg));
      } else {
        $('#content').append(textTemplate(msg));
      }
    }
    skip++;
  }
}

// Template for inserting text messages
function textTemplate(msg) {
  return `<div class='msg'>
            <div class='nametag'>
              <div class='pro-img' style='background-image: url("https://i.imgur.com/${msg.upic}m.png");'></div>
              <span>${msg.nick}</span>
            </div>
            <div class='msg-text'>${msg.content}</div>
          </div>`;
}

// Template for inserting image messages
function imgTemplate(msg, scroll) {
  return `<div class='msg'>
            <div class='nametag'>
              <div class='pro-img' style='background-image: url("https://i.imgur.com/${msg.upic}t.png);'></div>
              <span>${msg.nick}</span>
            </div>
            <div class='msg-text'>
              <img class='chat-img' src="https://i.imgur.com/${msg.content}.png" ${onloader(scroll)} alt='User Image'>
            </div>
          </div>`;
  function onloader(scroll) {
    if (scroll) {
      return "onload='scrollDown()'";
    }
    return '';
  }
}

// Fetches a block of messages from the current chanel
function fetchMessages(scroll) {
  if (!lastMsg) {
    $.ajax(`/~tomanfi2/api/message.php?sub=${sub}&chan=${chanName}&lim=${MESSAGE_LIMIT}&skip=${skip}`, {
      method: 'GET',
      beforeSend(xhr) {
        XHRPool.push(xhr);
      },
      success(res) {
        const msgArr = JSON.parse(res);
        if (msgArr.length < MESSAGE_LIMIT) {
          lastMsg = true;
        }
        let origSize = $('#content')[0].scrollHeight;
        insertMessages(msgArr, true, scroll);
        if (!scroll) {
          let offset = $('#content')[0].scrollHeight - origSize;
          $('#content').scrollTop(offset);
        } else {
          $('#content')[0].scrollTop = $('#content')[0].scrollHeight;
          scrollDeac = false;
          if (msgArr[0] !== undefined) {
            lastId = msgArr[0].id;
          }
        }
      },
      error(_, status) {
        if (status !== 'abort') {
          location.reload();
        }
      },
      complete(xhr) {
        const index = XHRPool.indexOf(xhr);
        if (index !== -1) {
          XHRPool.splice(index, 1);
        }
      }
    });
  }
}

// Scrolls to the bottom of the chat window (triggered when image is loaded)
function scrollDown() {
  $('#content')[0].scrollTop = $('#content')[0].scrollHeight;
}

// Aborts all pending requests
function abortRequests() {
  XHRPool.forEach((req) => {
    req.abort();
  });
  XHRPool = [];
}

// Stop the Update loop when logging out
$('#lo-wrap').click(() => {
  clearInterval(updateLoop);
});