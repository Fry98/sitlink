let reader = new FileReader();
particlesJS.load('particles-js', 'assets/particles.json');

$("#img-sel").change(function() {
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

reader.onload = () => {
  $('#pick-bg').css('background-image', `url(${reader.result})`);
  $('#pick-ui').addClass('hide-plus');
};

$('.cancel').click(() => {
  location.href = './';
});

$('form').submit((e) => {
  e.preventDefault();
  const pwd = $('#pwd').val();
  const pwdCon = $('#pwd-con').val();
  const nick = $('#nick').val();
  const mail = $('#mail').val();
  const pic = reader.result;
  if (pwd !== pwdCon) {
    alert("Passwords don't match!");
    return;
  }
  if (nick.length < 3) {
    alert("Nickname has to be at least 3 characters long!");
    return;
  }
  if (pwd.length < 6) {
    alert("Password has to be at least 6 characters long!");
    return;
  }
  if (!mail.match(/(.+)@(.+)\.(.+)/)) {
    alert("Invalid e-mail address!");
    return;
  }
  if (reader.result === null) {
    alert("Profile picture has to be selected!");
    return; 
  }
  $.ajax('api/add_user.php', {
    method: 'POST',
    data: {
      nick,
      mail,
      pwd,
      pic
    },
    success() {
      location.href = './';
    },
    error(res) {
      alert(res.responseText);
    }
  });
});