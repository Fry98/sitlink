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
let UpdatePool = null;
let MessagePool = null;
let confirmCallback = null;
let chanIndex = null;
let subUrlInp = '';
let chanNameInp = '';
const MESSAGE_LIMIT = 30;
const msgSound = new Audio('/assets/light.mp3');

// Inital page setup
updateFollowToggle();
initChannel();
startUpdateLoop();

// Message polling request
function startUpdateLoop(immediate) {
  clearInterval(updateLoop);
  function update() {
    if (UpdatePool === null) {
      $.ajax(`/api/update.php?sub=${sub}&chan=${chanName}&last=${lastId}`, {
        method: 'GET',
        beforeSend (xhr) {
          UpdatePool = xhr;
        },
        success(res) {
          const msgArr = JSON.parse(res);
          if (msgArr.length > 0) {            
            insertMessages(msgArr, false, true);
            $('#ctn-wrap')[0].scrollTop = $('#ctn-wrap')[0].scrollHeight;
            lastId = msgArr[msgArr.length - 1].id;
          }
          for (const msg of msgArr) {
            if (!msg.owned) {
              msgSound.play();
              break;
            }
          }
        },
        error(_, status) {
          if (status !== 'abort') {
            location.reload();
          }
        },
        complete() {
          UpdatePool = null;
        }
      });
    }
  }
  if (immediate) {
    update();
  }
  updateLoop = setInterval(update, 3000);
}

// Channel switching
$('body').on('click', '#chans li', function() {
  if (this.id === 'chan-add') {
    return;
  }
  let newIndex = $('#chans li').index(this);
  if (newIndex !== currChan) {
    if ($('#chans li')[currChan]) {
      $('#chans li')[currChan].classList.remove('selected');
    }
    $(this).addClass('selected');
    $('#sidebar').removeClass('open');
    currChan = newIndex;
    chanName = chans[currChan];
    scrollDeac = true;
    $('.msg').remove();
    skip = 0;
    lastMsg = false;
    lastId = 0;
    abortRequests();
    initChannel();
  }
});

// Custom textarea outline on focus
$('#msg-box textarea').focus(() => {
  $('#msg-box').css('border-color', 'rgb(28, 126, 192)');
});

$('#msg-box textarea').blur(() => {
  $('#msg-box').css('border-color', '');
});

// Custom outline for Suchat URL
$('#new-sub-url').focus(() => {
  $('#new-sub-url-wrap').css('border-color', 'rgb(28, 126, 192)');
});

$('#new-sub-url').blur(() => {
  $('#new-sub-url-wrap').css('border-color', '');
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
  const cont = $('#ctn-wrap')[0];
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
    alert('Selected file has to be an image!');
    return;
  }
  reader.readAsDataURL(imgFile);
});

reader.onload = () => {
  if (reader.result.length > 5242880) {
    alert('Image size is too big!');
    return;
  }
  clearInterval(updateLoop);
  abortRequests();
  const currChan = chanName;
  $.ajax('/api/message.php', {
    method: 'POST',
    data: {
      sid: sub,
      chan: chanName,
      img: true,
      content: reader.result
    },
    error(res) {
      alert(res.responseText);
    },
    complete() {
      if (chanName === currChan) {
        startUpdateLoop(true);
      }
    }
  });
};

// Switching Subchats
$('body').on('click', '.flw-list-item', function() {
  let flwIndex = $('.flw-list-item').index(this);
  let link;
  if (flwTab) {
    link = flwList.owned[flwIndex].id;
  } else {
    link = flwList.followed[flwIndex].id;
  }
  if (sub !== link) {
    location.href = `/c/${link}`;
  } else {
    $('#flw-overlay').addClass('overlay-hide');
  }
});

// Accessing the Subchat Menu
$('#subs').click(() => {
  $.ajax('/api/follow.php', {
    method: 'GET',
    success(res) {
      flwList = JSON.parse(res);
      updateFollows();
      $('#flw-overlay').toggleClass('overlay-hide');
      $('#sidebar').removeClass('open');
    }
  });
});

$('#flw-list-close').click(() => {
  $('#flw-overlay').toggleClass('overlay-hide');
});

$('#flw-overlay').click(function(e) {
  if (e.target !== this) {
    return;
  }
  $('#flw-overlay').toggleClass('overlay-hide');
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
$('#ctn-wrap').on('scroll', function() {
  if ($(this).scrollTop() <= 0 && !scrollDeac) {
    scrollDeac = true;
    fetchMessages();
  }
});

// Removing a channel
$('body').on('click', '.chan-remove', function(e) {
  e.stopPropagation();
  if (chans.length < 2) {
    alert('Subchat has to contain at least one channel!');
    return;
  }
  chanIndex = $('.chan-remove').index(this);
  confirmCallback = removeChannel;
  $('#confirm-prompt').html(`Do you really want to delete channel <span>#${chans[chanIndex]}</span>?`);
  $('#confirm-overlay').removeClass('overlay-hide');
});

// Confirm box controls
$('#cancel').click(() => {
  $('#confirm-overlay').addClass('overlay-hide');
});

$('#confirm').click(() => {
  $('#confirm-overlay').addClass('overlay-hide');
  confirmCallback();
});

// Adding new Subchat
$('body').on('click', '#flw-add', () => {
  $('#new-sub-box input').val('');
  subUrlInp = '';
  $('#new-sub-overlay').removeClass('overlay-hide');
});

$('#new-sub-cancel').click(() => {
  $('#new-sub-overlay').addClass('overlay-hide');
});

$('#new-sub-url').on('input', function() {
  if (!this.value.match(/^[A-Za-z0-9\-_]*$/) || this.value.length > 30) {
    this.value = subUrlInp;
  } else {
    subUrlInp = this.value;
  }
});

$('#new-sub-form').submit((e) => {
  e.preventDefault();
  if ($('#new-sub-name').val().length > 50) {
    alert('Subchat name can only be up to 50 characters long!');
    return;
  }
  if ($('#new-sub-name').val().length < 3) {
    alert('Subchat name has to be at least 3 charactes long!');
    return;
  }
  if ($('#new-sub-url').val().length < 3) {
    alert('Subchat URL has to be at least 3 charactes long!');
    return;
  }
  if ($('#new-sub-desc').val().length < 10) {
    alert('Subchat description has to be at least 10 charactes long!');
    return;
  }
  if ($('#new-sub-desc').val().length > 100) {
    alert('Subchat description can only be up to 100 characters long!');
    return;
  }
  $.ajax('/api/subchat.php', {
    method: 'POST',
    data: {
      url: $('#new-sub-url').val().toLowerCase(),
      title: $('#new-sub-name').val(),
      desc: $('#new-sub-desc').val()
    },
    success() {
      location.href = `/c/${$('#new-sub-url').val()}`;
    },
    error(res) {
      alert(res.responseText);
    }
  });
});

// Adding new channel
$('#chan-add').click(() => {
  $('#new-chan-url').val('');
  chanNameInp = '';
  $('#new-chan-overlay').removeClass('overlay-hide');
});

$('#new-chan-cancel').click(() => {
  $('#new-chan-overlay').addClass('overlay-hide');
});

$('#new-chan-url').on('input', function() {
  if (!this.value.match(/^[A-Za-z0-9\-_]*$/) || this.value.length > 20) {
    this.value = chanNameInp;
  } else {
    chanNameInp = this.value;
  }
});

$('#new-chan-form').submit((e) => {
  e.preventDefault();
  if ($('#new-chan-url').val().length < 3) {
    alert('Channel name has to be at least 3 characters long!');
    return;
  }
  const newChan = $('#new-chan-url').val().toLowerCase();
  $.ajax('/api/channel.php', {
    method: 'POST',
    data: {
      sub,
      chan: newChan
    },
    success() {
      chans.push(newChan);
      $('#chan-add').before(`<li>
                              #${newChan}
                              <div class='chan-remove'>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="none" d="M0 0h24v24H0V0z"/><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zm2.46-7.12l1.41-1.41L12 12.59l2.12-2.12 1.41 1.41L13.41 14l2.12 2.12-1.41 1.41L12 15.41l-2.12 2.12-1.41-1.41L10.59 14l-2.13-2.12zM15.5 4l-1-1h-5l-1 1H5v2h14V4z"/><path fill="none" d="M0 0h24v24H0z"/></svg>
                              </div>
                            </li>`);
      $('#new-chan-overlay').addClass('overlay-hide');
    },
    error(res) {
      alert(res.responseText);
    }
  });
});

// Stop the Update loop when logging out
$('#lo-wrap').click(() => {
  clearInterval(updateLoop);
});

// Switching SITLINK color theme
$('#theme').click(() => {
  if (Cookies.get('light') === undefined) {
    $('#sheet')[0].setAttribute("href", "/css/chat-light.min.css");
    Cookies.set('light', true);
  } else {
    $('#sheet')[0].setAttribute("href", "/css/chat-dark.min.css");
    Cookies.remove('light');
  }
});

// Setting the body size
window.onresize = function() {
  document.body.style.cssText = `height: ${window.innerHeight}px`;
};

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
  const currChan = chanName;
  $.ajax('/api/message.php', {
    method: 'POST',
    data: {
      sid: sub,
      chan: chanName,
      img: false,
      content: cont
    },
    error(res) {
      alert(res.responseText);
    },
    complete() {
      if (chanName === currChan) {
        startUpdateLoop(true);
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
    confirmCallback = removeSubchat;
    $('#confirm-prompt').html('Do you really want to delete this subchat?');
    $('#confirm-overlay').removeClass('overlay-hide');
    return;
  }

  $.ajax('/api/follow.php', {
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
    $('#flw-list-content').append(`<div class='flw-list-item'>
                                    <h1>${item.title}</h1>
                                    <div class='flw-item-desc'>${item.desc}</div>
                                  </div>`);
  }
  if (flwTab) {
    $('#flw-list-content').append(`<div id='flw-add-wrap'>
                                    <div id='flw-add'>
                                      + Create New Subchat
                                    </div>
                                  </div>`);
  }
}

// Insert message into the DOM
function insertMessages(msgArr, prepend, scroll) {
  for (const msg of msgArr) {
    if (msg.img) {
      if (prepend) {
        $('#loader').after(imgTemplate(msg, scroll));
      } else {
        $('#content').append(imgTemplate(msg, scroll));
      }
    } else {
      if (prepend) {
        $('#loader').after(textTemplate(msg));
      } else {
        $('#content').append(textTemplate(msg));
      }
    }
    skip++;
  }
}

// Template for inserting text messages
function textTemplate(msg) {
  return `<div class='msg ${msgOwned(msg.owned)}'>
            <div class='nametag'>
              <div class='pro-img' style='background-image: url("https://i.imgur.com/${msg.upic}t.png");'></div>
              <span>${msg.nick}</span>
            </div>
            <div class='msg-text'>${msg.content}</div>
          </div>`;
}

// Template for inserting image messages
function imgTemplate(msg, scroll) {
  return `<div class='msg ${msgOwned(msg.owned)}'>
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

function msgOwned(owned) {
  if (owned) {
    return 'own';
  }
  return '';
}

// Fetches a block of messages from the current channel
function fetchMessages() {
  if (!lastMsg) {
    $.ajax(`/api/message.php?sub=${sub}&chan=${chanName}&lim=${MESSAGE_LIMIT}&skip=${skip}`, {
      method: 'GET',
      beforeSend(xhr) {
        MessagePool = xhr;
      },
      success(res) {
        const msgArr = JSON.parse(res);
        if (msgArr.length < MESSAGE_LIMIT) {
          lastMsg = true;
          $('#loader').css('display', 'none');
        }
        let origSize = $('#ctn-wrap')[0].scrollHeight;
        insertMessages(msgArr, true, false);
        let offset = $('#ctn-wrap')[0].scrollHeight - origSize;
        $('#ctn-wrap').scrollTop(offset);
        scrollDeac = false;
      },
      error(_, status) {
        if (status !== 'abort') {
          location.reload();
        }
      },
      complete() {
        MessagePool = null;
      }
    });
  }
}

// Initializes new channel (I mean... the function name is pretty self-explanatory...)
function initChannel() {
  clearInterval(updateLoop);
  abortRequests();
  abortMessage();
  $('#loader').css('display', 'flex');
  $.ajax(`/api/message.php?sub=${sub}&chan=${chanName}&lim=${MESSAGE_LIMIT}&skip=${skip}`, {
    method: 'GET',
    beforeSend(xhr) {
      MessagePool = xhr;
    },
    success(res) {
      const msgArr = JSON.parse(res);
      if (msgArr.length < MESSAGE_LIMIT) {
        lastMsg = true;
        $('#loader').css('display', 'none');
      }
      insertMessages(msgArr, true, true);
      $("#ctn-wrap").scrollTop($("#ctn-wrap")[0].scrollHeight);
      window.onresize();
      scrollDeac = false;
      if (msgArr.length > 0) {
        lastId = msgArr[0].id;
      }
      startUpdateLoop();
    },
    error(_, status) {
      if (status !== 'abort') {
        location.reload();
      }
    },
    complete() {
      MessagePool = null;
    }
  });
}

// Scrolls to the bottom of the chat window (triggered when image is loaded)
function scrollDown() {
  $('#ctn-wrap')[0].scrollTop = $('#ctn-wrap')[0].scrollHeight;
}

// Aborts all pending Update requests
function abortRequests() {
  if (UpdatePool !== null) {
    UpdatePool.abort();
    UpdatePool = null;
  }
}

// Aborts all pending Message requests
function abortMessage() {
  if (MessagePool !== null) {
    MessagePool.abort();
    MessagePool = null;
  }
}

// Removing channel
function removeChannel() {
  $.ajax('/api/channel.php', {
    method: 'DELETE',
    data: {
      sub,
      chan: chans[chanIndex]
    },
    success() {
      $('#chans li')[chanIndex].remove();
      chans.splice(chanIndex, 1);
      let newSelect = null;
      chans.forEach((chan, i) => {
        if (chan === chanName) {
          newSelect = i;
        }
      });
      if (newSelect !== null) {
        currChan = newSelect;
      } else {
        currChan = null;
        $('#chans li')[0].click();
      }
    },
    error(res) {
      alert(res.responseText);
    }
  });
}

// Removing subchat
function removeSubchat() {
  $.ajax('/api/subchat.php', {
    method: 'DELETE',
    data: { sub },
    success() {
      location.href = '/';
    },
    error(res) {
      alert(res.responseText);
    }
  });
}
