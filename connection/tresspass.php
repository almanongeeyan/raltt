<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>🚫 You Shall Not Pass! 🚫</title>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<style>
		:root {
			--primary: #ef4444;
			--primary-hover: #dc2626;
			--secondary: #3b82f6;
			--secondary-hover: #2563eb;
			--success: #10b981;
			--success-hover: #059669;
			--warning: #f59e0b;
			--warning-hover: #d97706;
			--text: #1f2937;
			--text-light: #6b7280;
			--bg: #f9fafb;
			--card-bg: #ffffff;
			--shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
			--shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
			--radius: 12px;
			--transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
		}

		* {
			box-sizing: border-box;
			margin: 0;
			padding: 0;
		}

		body {
			background: var(--bg);
			font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
			color: var(--text);
			line-height: 1.6;
			min-height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 0;
		}

		.container {
			width: 100%;
			max-width: 640px;
			margin: auto;
			min-height: 100vh;
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			padding: 32px 0;
		}

		.card {
			background: var(--card-bg);
			border-radius: var(--radius);
			box-shadow: var(--shadow);
			padding: 2.5rem 2rem 2rem 2rem;
			margin-bottom: 2rem;
			text-align: center;
			transition: var(--transition);
			border: 1px solid rgba(0, 0, 0, 0.05);
			width: 100%;
		}

		.card:hover {
			box-shadow: var(--shadow-hover);
			transform: translateY(-2px);
		}

		.card h1 {
			color: var(--primary);
			font-size: clamp(2rem, 5vw, 2.6rem);
			font-weight: 700;
			margin-bottom: 1.25rem;
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 0.75rem;
			line-height: 1.2;
		}

		.card p {
			color: var(--text-light);
			font-size: 1.13rem;
			margin-top: 1.5rem;
		}

		.highlight {
			color: var(--success);
			font-weight: 600;
			text-decoration: none;
			transition: var(--transition);
			position: relative;
		}

		.highlight:hover {
			color: var(--success-hover);
		}

		.highlight::after {
			content: '';
			position: absolute;
			bottom: -2px;
			left: 0;
			width: 0;
			height: 2px;
			background: var(--success);
			transition: var(--transition);
		}

		.highlight:hover::after {
			width: 100%;
		}

		.magic {
			color: var(--warning);
			font-weight: 600;
			text-decoration: none;
			transition: var(--transition);
			position: relative;
		}

		.magic:hover {
			color: var(--warning-hover);
		}

		.magic::after {
			content: '';
			position: absolute;
			bottom: -2px;
			left: 0;
			width: 0;
			height: 2px;
			background: var(--warning);
			transition: var(--transition);
		}

		.magic:hover::after {
			width: 100%;
		}

		.card-secondary {
			background: var(--card-bg);
			border-radius: var(--radius);
			box-shadow: var(--shadow);
			padding: 2rem 1.5rem 1.5rem 1.5rem;
			margin-bottom: 2rem;
			text-align: center;
			border: 1px solid rgba(0, 0, 0, 0.05);
			width: 100%;
		}

		.card-secondary h2 {
			font-size: clamp(1.4rem, 3vw, 1.7rem);
			font-weight: 600;
			color: var(--text);
			margin-bottom: 1rem;
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 0.5rem;
			line-height: 1.2;
		}

		.card-secondary p {
			color: var(--text-light);
			font-size: 1rem;
		}

		.login-btn {
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 0.5rem;
			margin: 2rem auto 0 auto;
			background: var(--secondary);
			color: white;
			font-size: 1.13rem;
			font-weight: 600;
			border: none;
			border-radius: var(--radius);
			padding: 1rem 2.5rem;
			cursor: pointer;
			box-shadow: 0 2px 8px rgba(33, 150, 243, 0.2);
			transition: var(--transition);
			text-decoration: none;
			width: 100%;
			max-width: 340px;
			text-align: center;
		}

		.login-btn:hover {
			background: var(--secondary-hover);
			transform: translateY(-2px) scale(1.02);
			box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
		}

		.footer {
			margin-top: 2.5rem;
			color: var(--text-light);
			font-size: 0.97rem;
			text-align: center;
			line-height: 1.5;
			width: 100%;
		}

		.footer a {
			color: var(--text-light);
			text-decoration: none;
			transition: var(--transition);
		}

		.footer a:hover {
			color: var(--text);
			text-decoration: underline;
		}

		/* Animation */
		@keyframes shake {
			0%, 100% { transform: translateX(0); }
			10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
			20%, 40%, 60%, 80% { transform: translateX(5px); }
		}

		.shake {
			animation: shake 0.6s ease-in-out;
		}

		/* Responsive adjustments */
		@media (max-width: 640px) {
			.container {
				padding: 16px 0;
			}
			.card, .card-secondary {
				padding: 1.2rem 0.7rem 1.2rem 0.7rem;
			}
			.login-btn {
				width: 100%;
				padding: 0.9rem 1rem;
				max-width: 100%;
			}
		}

		@media (max-width: 480px) {
			.container {
				padding: 8px 0;
			}
			.card h1 {
				flex-direction: column;
				gap: 0.5rem;
				font-size: 1.3rem;
			}
			.card-secondary h2 {
				flex-direction: column;
				gap: 0.5rem;
				font-size: 1.1rem;
			}
		}
	</style>
</head>
<body>
	<div class="container">
		<div class="card">
			<h1><span>🚫</span> You Shall Not Pass! <span>🚫</span></h1>
			<p>Looks like you're trying to access something you shouldn't, buddy.<br>
			Are you sure you have the <span class="highlight" style="pointer-events:none;cursor:default;text-decoration:none;">secret handshake</span> and the <span class="magic" style="pointer-events:none;cursor:default;text-decoration:none;">magic password</span>?</p>
		</div>
		<div class="card-secondary">
			<h2><span>🤔</span> Wait... Are you supposed to be here? <span>🤔</span></h2>
			<p>Maybe you took a wrong turn on the internet highway?<br>
			If you think you <b>do</b> belong here, you might need to prove it...</p>
		</div>
		<a href="../user_login_form.php" class="login-btn">
			<i class="fas fa-key"></i> Login With Your Credentials
		</a>
		<div class="footer">
			This is a highly secure and definitely not hastily put together meme page.<br>
			&copy; <span id="year"></span> RALTT Security | <a href="#">Terms</a> | <a href="#">Privacy</a>
		</div>
	</div>

	<script>
		// Set current year
		document.getElementById('year').textContent = new Date().getFullYear();
		
		// Add shake animation to card on hover
		const card = document.querySelector('.card');
		card.addEventListener('mouseenter', () => {
			card.classList.add('shake');
			setTimeout(() => card.classList.remove('shake'), 600);
		});
	</script>
</body>
</html>