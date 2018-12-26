let currChan = 0;
let flwList;
let flwTab = false;
let chanName = chans[0];

// Inital page setup
setTimeout(() => {
  updateFollowToggle();
  $('#content')[0].scrollTop = $('#content')[0].scrollHeight;
}, 0);

// Channel switching
$('#chans li').click(function() {
  let newIndex = $('#chans li').index(this);
  if (newIndex !== currChan) {
    $('#chans li')[currChan].classList.remove('selected');
    $(this).addClass('selected');
    $('#sidebar').removeClass('open');
    currChan = newIndex;
    chanName = chans[currChan];
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
$('.msg img').click(function() {
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

// Submit message to the API endpoint
function sendMessage() {
  $.ajax('/~tomanfi2/api/message.php', {
    method: 'POST',
    data: {
      sid: sub,
      chan: chanName,
      img: false,
      content: $('#msg').val()
    },
    success() {
      $('#msg').val('');
      resize($('#msg')[0]);    
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