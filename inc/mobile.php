<?php
switch ($page) {
    case "index":
		loadIndexPage();
        break;
    case "about":
		loadAboutPage();
        break;
    case "contact":
		loadContactPage();
        break;
}

function loadIndexPage()
{
$output = <<<INDEX
	<body>
		<div id="header">
			<h1>Zocalo Design</h1>
		</div>
		<div id="main">
			<ul id="homeNav">
				<li><a href="branding.php"><img src="/img/branding.png" /></a></li>
				<li><a href="web.php"><img src="/img/web.png" /></a></li>
				<li><a href="collateral.php"><img src="/img/collateral.png" /></a></li>
				<li><a href="packaging.php"><img src="/img/packaging.png" /></a></li>
				<li><a href="environments.php"><img src="/img/environments.png" /></a></li>
			</ul>
		</div>
		<div id="footer">
			<ul id="footerNav">
				<li>
					<a href="contact.php">Contact</a>
				</li>
				<li>
					<a href="about.php">About</a>
				</li>
			</ul>
		</div>
	</body>
INDEX;
echo $output;
}

function loadAboutPage()
{
$output = <<<INDEX
	<body>
		<div id="header">
			<h1>Zocalo Design</h1>
		</div>
		<div id="footer">
			<ul id="footerNav">
				<li>
					<a href="contact.php">Contact</a>
				</li>
				<li>
					<a href="about.php">About</a>
				</li>
			</ul>
		</div>
	</body>
INDEX;
echo $output;
}



?>