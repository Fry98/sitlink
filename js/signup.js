let reader = new FileReader();
particlesJS.load('particles-js', 'assets/particles.json');

$("#img-sel").change(function(){
  const imgFile = this.files[0];
  this.value = null;
  if(!imgFile.type.includes('image')){
    alert('Selected file has to be an image');
    return;
  }
  if(imgFile.size > 5242880){
    alert('Image has to be smaller than 5MB');
    return;
  }
  reader.readAsDataURL(imgFile);
});

reader.onload = ()=>{
  $('#pick-bg').css('background-image', `url(${reader.result})`);
  $('#pick-ui').addClass('hide-plus');
};