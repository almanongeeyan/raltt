<?php
// my_cart page with landing page design/colors (not the whole landing page)
session_start();
if (!isset($_SESSION['logged_in'])) {
	header('Location: ../connection/tresspass.php');
	exit();
}
include '../includes/headeruser.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>My Cart - RALTT</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
	<script src="https://cdn.tailwindcss.com"></script>
	<script>
	  tailwind.config = {
		theme: {
		  extend: {
			colors: {
			  primary: '#7d310a',
			  secondary: '#cf8756',
			  accent: '#e8a56a',
			  dark: '#270f03',
			  light: '#f9f5f2',
			  textdark: '#333',
			  textlight: '#777',
			},
			fontFamily: {
			  inter: ['Inter', 'sans-serif'],
			},
			boxShadow: {
			  product: '0 8px 24px rgba(0,0,0,0.08)',
			},
		  },
		},
	  }
	</script>
	<style>
		body { font-family: 'Inter', sans-serif; }
		.cart-bg {
			min-height: 100vh;
			background: linear-gradient(120deg, #f8e7db 60%, #d47a3a 100%);
			display: flex;
			align-items: center;
			justify-content: center;
			position: relative;
			font-family: 'Inter', sans-serif;
			padding: 0;
		}
			.cart-container {
				width: 100%;
				max-width: 950px;
				background: #fff;
				border-radius: 18px;
				box-shadow: 0 4px 24px rgba(0,0,0,0.08);
				padding: 2.5rem 1.5rem;
				margin: 5.5rem auto 2rem auto; /* Add top margin for header */
			}
		.cart-title {
			font-size: 2.2rem;
			font-weight: 800;
			color: #8A421D;
			margin-bottom: 0.5rem;
			letter-spacing: 1px;
		}
		.cart-desc {
			color: #684330;
			margin-bottom: 2rem;
			font-size: 1.08rem;
		}
		.cart-table {
			width: 100%;
			border-collapse: separate;
			border-spacing: 0 0.5rem;
			font-size: 1rem;
			background: none;
		}
			.cart-table th {
				background: #7d310a;
				color: #fff;
				font-weight: 700;
				padding: 1rem 0.5rem;
				border-radius: 10px 10px 0 0;
				text-align: left;
				font-size: 1.05rem;
			}
		.cart-table th.checkbox-col,
		.cart-table td.checkbox-col {
			width: 40px;
			text-align: center;
		}
		.cart-table td {
			background: #fff;
			border-radius: 10px;
			box-shadow: 0 2px 8px rgba(0,0,0,0.03);
			padding: 1rem 0.5rem;
			vertical-align: middle;
			border-bottom: 1px solid #f3e3d7;
		}
		.cart-table td.product-info {
			display: flex;
			align-items: center;
			gap: 1rem;
		}
		.cart-table img {
			width: 3.5rem;
			height: 3.5rem;
			border-radius: 0.75rem;
			object-fit: cover;
			border: 1px solid #eee;
			box-shadow: 0 2px 8px rgba(0,0,0,0.05);
		}
		.cart-table .product-name {
			font-weight: bold;
			color: #333;
			font-size: 1.05rem;
		}
		.cart-table .product-type {
			font-size: 0.92rem;
			color: #bfa07c;
		}
		.cart-table input[type="number"] {
			width: 3.2rem;
			text-align: center;
			border: 1px solid #e0e0e0;
			border-radius: 0.5rem;
			padding: 0.3rem 0.5rem;
			outline: none;
			font-size: 1rem;
		}
		.cart-table .remove-btn {
			color: #e74c3c;
			background: none;
			border: none;
			cursor: pointer;
			font-size: 1.1rem;
			transition: color 0.18s;
		}
		.cart-table .remove-btn:hover {
			color: #b30000;
		}
		.cart-summary {
			width: 100%;
			max-width: 340px;
			background: #FFF6F0;
			border-radius: 1rem;
			padding: 2rem 1.5rem;
			box-shadow: 0 2px 8px rgba(0,0,0,0.05);
			border: 1px solid #FFD8C5;
			margin-left: auto;
			margin-top: 2.5rem;
		}
		.cart-summary-row {
			display: flex;
			justify-content: space-between;
			margin-bottom: 0.5rem;
			color: #684330;
			font-weight: 600;
			font-size: 1rem;
		}
		.cart-summary-row.fee {
			color: #bfa07c;
			font-weight: 500;
		}
		.cart-summary-row.total {
			font-size: 1.2rem;
			font-weight: 900;
			color: #8A421D;
			border-top: 1px solid #FFD8C5;
			padding-top: 1rem;
			margin-top: 1rem;
		}
			.checkout-btn {
				width: 100%;
				margin-top: 2rem;
				padding: 0.75rem 0;
				background: #7d310a;
				color: #fff;
				border: none;
				border-radius: 0.75rem;
				font-size: 1rem;
				font-weight: 700;
				transition: background 0.2s, transform 0.2s;
				box-shadow: 0 2px 8px rgba(0,0,0,0.08);
				cursor: pointer;
				display: flex;
				align-items: center;
				justify-content: center;
				gap: 0.5rem;
			}
			.checkout-btn:hover {
				background: #cf8756;
				transform: translateY(-2px);
			}
		@media (max-width: 900px) {
			.cart-container {
				padding: 1.2rem 0.5rem;
				max-width: 99vw;
			}
			.cart-title {
				font-size: 1.5rem;
			}
			.cart-summary {
				padding: 1.2rem 0.7rem;
				max-width: 99vw;
			}
			.cart-table th, .cart-table td {
				font-size: 0.98rem;
				padding: 0.7rem 0.3rem;
			}
			.cart-table img {
				width: 2.5rem;
				height: 2.5rem;
			}
		}
		@media (max-width: 600px) {
			.cart-bg {
				padding: 0;
				min-height: 100vh;
			}
			.cart-container {
				padding: 0.5rem 0.1rem;
				margin: 0.5rem 0;
			}
			.cart-title {
				font-size: 1.1rem;
			}
			.cart-summary {
				padding: 0.7rem 0.3rem;
			}
			.cart-table th, .cart-table td {
				font-size: 0.92rem;
				padding: 0.5rem 0.1rem;
			}
		}
	</style>
</head>
<body class="cart-bg">
	<div class="cart-container">
		<h1 class="cart-title">My Cart</h1>
		<p class="cart-desc">Review your selected tiles and proceed to checkout.</p>
		<div style="overflow-x:auto;">
			<table class="cart-table">
				<thead>
					<tr>
						<th class="checkbox-col"></th>
						<th class="text-left px-3 py-3">Product</th>
						<th class="text-left px-3 py-3">Price</th>
						<th class="text-center px-3 py-3">Quantity</th>
						<th class="text-right px-3 py-3">Total</th>
						<th class="text-center px-3 py-3">Remove</th>
					</tr>
				</thead>
				<tbody id="cart-items">
					<!-- Example row, replace with PHP/JS loop for real data -->
					<tr>
						<td class="checkbox-col">
							<input type="checkbox" name="cart_select[]" value="1" style="width: 1.2rem; height: 1.2rem; accent-color: #8A421D;">
						</td>
						<td class="product-info">
							<img src="../images/user/tile1.jpg" alt="Tile">
							<div>
								<div class="product-name">Premium Ceramic Tile</div>
								<div class="product-type">Ceramic</div>
							</div>
						</td>
						<td style="color: #cf8756; font-weight: bold;">₱1,250</td>
						<td style="text-align:center;">
							<input type="number" min="1" value="2">
						</td>
						<td style="text-align:right; font-weight: bold; color: #8A421D;">₱2,500</td>
						<td style="text-align:center;">
							<button class="remove-btn"><i class="fa fa-trash"></i></button>
						</td>
					</tr>
					<tr>
						<td class="checkbox-col">
							<input type="checkbox" name="cart_select[]" value="2" style="width: 1.2rem; height: 1.2rem; accent-color: #8A421D;">
						</td>
						<td class="product-info">
							<img src="../images/user/tile2.jpg" alt="Tile">
							<div>
								<div class="product-name">Porcelain Tile</div>
								<div class="product-type">Porcelain</div>
							</div>
						</td>
						<td style="color: #cf8756; font-weight: bold;">₱950</td>
						<td style="text-align:center;">
							<input type="number" min="1" value="1">
						</td>
						<td style="text-align:right; font-weight: bold; color: #8A421D;">₱950</td>
						<td style="text-align:center;">
							<button class="remove-btn"><i class="fa fa-trash"></i></button>
						</td>
					</tr>
					<!-- End example rows -->
				</tbody>
			</table>
		</div>
		<div class="cart-summary">
			<div class="cart-summary-row">
				<span>Subtotal</span>
				<span id="cart-subtotal">₱3,450</span>
			</div>
			<div class="cart-summary-row fee">
				<span>Delivery Fee</span>
				<span>₱200</span>
			</div>
			<div class="cart-summary-row total">
				<span>Total</span>
				<span id="cart-total">₱3,650</span>
			</div>
			<button class="checkout-btn"><i class="fa fa-credit-card"></i> Proceed to Checkout</button>
		</div>
	</div>
</body>
</html>
