document.getElementById('merchandise-form').addEventListener('submit', async function (event) {
  event.preventDefault();

  const itemName = document.getElementById('itemName').value.trim();
  const description = document.getElementById('itemDescription').value.trim();
  const category = document.getElementById('itemCategory').value;
  const listPrice = parseFloat(document.getElementById('listPrice').value);
  const salePrice = parseFloat(document.getElementById('salePrice').value);
  const quantity = parseInt(document.getElementById('quantitySold').value);
  const totalValue = salePrice * quantity;

  if (!itemName || !description || !category || !listPrice || !salePrice || !quantity || isNaN(totalValue)) {
      alert('Please fill out all fields correctly.');
      return;
  }

  const submitButton = event.target.querySelector('button[type="submit"]');
  submitButton.disabled = true;
  submitButton.textContent = 'Submitting...';

  try {
      const response = await fetch('dashboard.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
              item: itemName,
              description: description,
              category: category,
              list_price: listPrice,
              sale_price: salePrice,
              quantity: quantity,
              total_value: totalValue,
          }),
      });

      if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
      }

      const result = await response.json();
      console.log('Server response:', result);

      if (result.success) {
          alert('Item added successfully!');
          location.reload();
      } else {
          alert('Error: ' + result.message);
      }
  } catch (error) {
      console.error('Fetch error:', error);
      alert('An unexpected error occurred. Please try again.');
  } finally {
      submitButton.disabled = false;
      submitButton.textContent = 'Add Item';
  }
});

function deleteItem(id) {
  fetch('delete_dashboard.php', { 
      method: 'POST',
      headers: {
          'Content-Type': 'application/json',
      },
      body: JSON.stringify({ id: id }),
  })
      .then(response => response.json()) 
      .then(data => {
          if (data.success) {
              alert(data.message);
              document.querySelector(`#item-${id}`).remove(); 
          } else {
              alert('Error: ' + data.message); 
          }
      })
      .catch(error => {
          alert('An error occurred while deleting the item.');
      });
}
