particlesJS.load('particles-js', '/~tomanfi2/assets/particles.json');

// Login form submission
$('form').submit((e) => {
  e.preventDefault();
  $.ajax('/~tomanfi2/api/login.php', {
    method: 'POST',
    data: {
      nick: $('#nick').val(),
      pwd: $('#pwd').val()
    },
    success() {
      location.href = '/~tomanfi2/c/nexus';
    },
    error(res) {
      alert(res.responseText);
    }
  });
});