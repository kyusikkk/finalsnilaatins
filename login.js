document.getElementById('loginForm').addEventListener('submit', async function(event) {
  event.preventDefault();
  
  const email = document.getElementById('loginEmail').value;
  const password = document.getElementById('loginPassword').value;

  const response = await fetch('login.php', {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json',
      },
      body: JSON.stringify({ email, password }),
  });

  const result = await response.json();

  if (result.success) {
      window.location.href = 'dashboard.php';
  } else {
      const errorDiv = document.getElementById('error');
      const errorMessage = document.getElementById('errorMessage');
      errorMessage.textContent = result.message;
      errorDiv.classList.remove('hidden');
  }
});
