let currChan = 0;

$('#chans li').click(function(){
  let newIndex = $('#chans li').index(this);
  if(newIndex !== currChan){
    $('#chans li')[currChan].classList.remove('selected');
    $(this).addClass('selected');
    currChan = newIndex;
  }
});