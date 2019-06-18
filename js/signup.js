let reader = new FileReader();
particlesJS.load('particles-js', '/assets/particles.json');

// File selector for profile picture
$("#img-sel").change(function() {
  const imgFile = this.files[0];
  this.value = null;
  if (!imgFile.type.includes('image')) {
    alert('Selected file has to be an image!');
    return;
  }
  reader.readAsDataURL(imgFile);
});

// Profile picture preview
reader.onload = () => {
  if (reader.result.length > 5242880) {
    alert('Image size is too big!');
    return;
  }
  $('#pick-bg').css('background-image', `url(${reader.result})`);
  $('#pick-ui').addClass('hide-plus');
};

$('.cancel').click(() => {
  location.href = '/';
});

// Form submission and validation
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
  if (nick.length > 30) {
    alert('Nickname can only be up to 30 characters long!');
    return;
  }
  if (pwd.length < 6) {
    alert("Password has to be at least 6 characters long!");
    return;
  }
  if (pwd.length > 30) {
    alert('Password can only be up to 30 characters long!');
    return;
  }
  if (!mail.match(/^(.+)@(.+)\.(.+)$/)) {
    alert("Invalid e-mail address!");
    return;
  }
  if (mail.length > 30) {
    alert('E-mail can only be up to 30 characters long!');
    return;
  }
  if (reader.result === null) {
    alert("Profile picture has to be selected!");
    return; 
  }

  // Displays loader
  $('#loader').css('display', 'flex');

  // POST request to the SITLINK API
  $.ajax('/api/add_user.php', {
    method: 'POST',
    data: {
      nick,
      mail,
      pwd,
      pic
    },
    success() {
      location.href = '/';
    },
    error(res) {
      // Hides loader and alerts error message
      $('#loader').css('display', 'none');
      alert(res.responseText);
    }
  });
});
