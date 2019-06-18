particlesJS.load('particles-js', '/assets/particles.json');

// Login form submission
$('form').submit((e) => {
  e.preventDefault();
  $.ajax('/api/login.php', {
    method: 'POST',
    data: {
      nick: $('#nick').val(),
      pwd: $('#pwd').val()
    },
    success() {
      location.href = '/c/nexus';
    },
    error(res) {
      alert(res.responseText);
    }
  });
});
