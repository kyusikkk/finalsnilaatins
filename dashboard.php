<?php
require 'connection.php';

session_start();

    if (!isset($_SESSION['user_id'])) {
   
    header("Location: login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (
            !isset($data['item'], $data['description'], $data['category'], $data['list_price'], $data['sale_price'], $data['quantity'], $data['total_value']) ||
            !is_numeric($data['list_price']) ||
            !is_numeric($data['sale_price']) ||
            !is_numeric($data['quantity']) ||
            !is_numeric($data['total_value'])
        ) {
            echo json_encode(['success' => false, 'message' => 'Invalid input']);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO merchandise_sales (item, description, category, list_price, sale_price, quantity, total_value, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "sssddidi",
            $data['item'],
            $data['description'],
            $data['category'],
            $data['list_price'],
            $data['sale_price'],
            $data['quantity'],
            $data['total_value'],
            $_SESSION['user_id'] 
        );
        

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Item added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error executing query: ' . $stmt->error]);
        }
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'An unexpected error occurred: ' . $e->getMessage()]);
    } finally {
        $conn->close();
    }
    exit;
}


$result = $conn->prepare("SELECT * FROM merchandise_sales WHERE user_id = ? ORDER BY id DESC");
$result->bind_param("i", $_SESSION['user_id']);
$result->execute();
$items = $result->get_result()->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="dashboard.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">
<nav class=" text-white p-4">
    <div class="container mx-auto flex justify-between items-center">
        <div class="text-xl text-gray-900 font-bold">Vet Clinic Merchandise Sale</div>
        <?php if (isset($_SESSION['user_id'])): ?>
            <form action="logout.php" method="post">
                <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded-lg ">Logout</button>
            </form>
        <?php endif; ?>
    </div>
</nav>

    <div class="container mx-auto p-6">
        <form id="merchandise-form" class="bg-white p-6 rounded-lg shadow-md">
            <div class="mb-4">
                <label for="itemName" class="block font-medium">Item Name</label>
                <input type="text" id="itemName" class="w-full border border-gray-300 rounded-lg p-2" placeholder="Item Name" required>
            </div>
            <div class="mb-4">
                <label for="itemDescription" class="block font-medium">Description</label>
                <input type="text" id="itemDescription" class="w-full border border-gray-300 rounded-lg p-2" placeholder="Description" required>
            </div>
            <div class="mb-4">
                <label for="itemCategory" class="block font-medium">Category</label>
                <select id="itemCategory" class="w-full border border-gray-300 rounded-lg p-2" required>
                    <option value="" selected>Choose category</option>
                    <option value="food">Food</option>
                    <option value="medication">Medication</option>
                    <option value="accessories">Accessories</option>
                    <option value="grooming">Grooming Supplies</option>
                </select>
            </div>
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="listPrice" class="block font-medium">List Price</label>
                    <input type="number" id="listPrice" class="w-full border border-gray-300 rounded-lg p-2" placeholder="List Price" required>
                </div>
                <div>
                    <label for="salePrice" class="block font-medium">Sale Price</label>
                    <input type="number" id="salePrice" class="w-full border border-gray-300 rounded-lg p-2" placeholder="Sale Price" required>
                </div>
                <div>
                    <label for="quantitySold" class="block font-medium">Quantity</label>
                    <input type="number" id="quantitySold" class="w-full border border-gray-300 rounded-lg p-2" placeholder="Quantity Sold" required>
                </div>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Add Item</button>
        </form>

        <h2 class="text-2xl font-bold mt-8 mb-4">Items List</h2>
        <table class="w-full bg-white rounded-lg shadow-md overflow-hidden">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-3 text-left">ID</th>
                    <th class="p-3 text-left">Item Name</th>
                    <th class="p-3 text-left">Description</th>
                    <th class="p-3 text-left">Category</th>
                    <th class="p-3 text-left">List Price</th>
                    <th class="p-3 text-left">Sale Price</th>
                    <th class="p-3 text-left">Quantity</th>
                    <th class="p-3 text-left">Total Value</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr id="item-<?php echo htmlspecialchars($item['id']); ?>" class="border-t">
                    <td class="p-3 text-left"><?php echo htmlspecialchars($item['id']); ?></td>
                    <td class="p-3 text-left"><?php echo htmlspecialchars($item['item']); ?></td>
                    <td class="p-3 text-left"><?php echo htmlspecialchars($item['description']); ?></td>
                    <td class="p-3 text-left"><?php echo htmlspecialchars($item['category']); ?></td>
                    <td class="p-3 text-left"><?php echo htmlspecialchars($item['list_price']); ?></td>
                    <td class="p-3 text-left"><?php echo htmlspecialchars($item['sale_price']); ?></td>
                    <td class="p-3 text-left"><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td class="p-3 text-left"><?php echo htmlspecialchars($item['total_value']); ?></td>
                    <td class="p-3 text-left">
                        <button onclick="deleteItem(<?php echo htmlspecialchars($item['id']); ?>)" class="bg-red-500 text-white px-3 py-1 rounded-lg">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
