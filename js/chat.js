let currChan = 0;

$('#chans li').click(function(){
  let newIndex = $('#chans li').index(this);
  if(newIndex !== currChan){
    $('#chans li')[currChan].classList.remove('selected');
    $(this).addClass('selected');
    currChan = newIndex;
  }
});

$('#msg-box textarea').focus(()=>{
  $('#msg-box').css('border-color', 'rgb(28, 126, 192)');
});

$('#msg-box textarea').blur(()=>{
  $('#msg-box').css('border-color', '');
});

$('.msg img').click(()=>{
  alert('clicked img')
});