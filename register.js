document.getElementById('registerForm').addEventListener('submit', async function(event) {
    event.preventDefault();
    
    const firstName = document.getElementById('firstName').value;
    const lastName = document.getElementById('lastName').value;
    const email = document.getElementById('registerEmail').value;
    const password = document.getElementById('registerPassword').value;

    const response = await fetch('register.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ firstName, lastName, email, password }),
    });

    const result = await response.json();

    if (result.success) {
        alert('Registration successful!');
        document.getElementById('registerForm').reset();
    } else {
        alert('Error: ' + result.message);
    }
});
