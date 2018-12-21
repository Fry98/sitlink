let currChan = 0;

// Scroll to the chat bottom
$('#content')[0].scrollTop = $('#content')[0].scrollHeight;

// Channel switching
$('#chans li').click(function() {
  let newIndex = $('#chans li').index(this);
  if(newIndex !== currChan){
    $('#chans li')[currChan].classList.remove('selected');
    $(this).addClass('selected');
    $('#sidebar').toggleClass('open');
    currChan = newIndex;
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

// Textarea autosizing
$('textarea').on('input', function() {
  const cont = $('#content')[0];
  let rescroll = false;
  if (cont.scrollHeight - cont.scrollTop - cont.clientHeight < 1) {
    rescroll = true;
  }
  this.style.height = "auto";
  this.style.height = (this.scrollHeight - 8) + "px";
  const bottom = ($('textarea')[0].clientHeight + 58) + "px";
  $('#content').css('padding-bottom', bottom);
  if (rescroll) {
    cont.scrollTop = cont.scrollHeight;
  }
});

// Image selector
$('#img').click(() => {
  $('#img-sel').click();
});