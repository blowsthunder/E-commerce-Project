<?php
function classActive($name)
{
	if (strpos($_SERVER['PHP_SELF'], $name) !== false)
		echo 'class="active"';
}
?>
<div id="sidebar">
	<button id="sidebar__collapse">
		<svg>
			<use xlink:href="#icon-collapse"></use>
		</svg>
		<span>Collapse menu</span>
	</button>

	<nav id="sidebar__nav">
		<ul>
			<li class="menu-heading"><span>Manage</span></li>
			<li>
				<a href="../Index/index.php" <?php classActive('Index') ?>>
					<svg>
						<use xlink:href="#icon-dashboard"></use>
					</svg>
					<span>Dashboard</span>
				</a>
			</li>
			<li>
				<a href="../Orders/Orders.php" <?php classActive('Orders') ?>>
					<svg>
						<use xlink:href="#icon-appearance"></use>
					</svg>
					<span>Orders</span>
				</a>
			</li>
			<li>
				<a href="../Products/Products.php" <?php classActive('Products') ?>>
					<svg>
						<use xlink:href="#icon-plugins"></use>
					</svg>
					<span>Products</span>
				</a>
			</li>
			<li>
				<a href="../Users/Users.php" <?php classActive('Users') ?>>
					<svg>
						<use xlink:href="#icon-users"></use>
					</svg>
					<span>Users</span>
				</a>
			</li>
			<li class="menu-heading"><span>Content</span></li>
			<li>
				<a href="../Chats/Chats.php" <?php classActive('Chats') ?>>
					<svg>
						<use xlink:href="#icon-posts"></use>
					</svg>
					<span>Chats</span>
				</a>
			</li>
			<li>
				<a href="../Pages/Pages.php" <?php classActive('Pages')?>>
					<svg>
						<use xlink:href="#icon-pages"></use>
					</svg>
					<span>Pages</span>
				</a>
			</li>
			<li>
				<a href="../Comments/Comments.php" <?php classActive('Comments') ?>>
					<svg>
						<use xlink:href="#icon-comments"></use>
					</svg>
					<span>Comments</span>
				</a>
			</li>
		</ul>
	</nav>

	<div id="sidebar__theme-switcher">
		<svg id="sidebar__theme-switcher__sun">
			<use xlink:href="#icon-sun"></use>
		</svg>

		<svg id="sidebar__theme-switcher__moon">
			<use xlink:href="#icon-moon"></use>
		</svg>
	</div> <!-- sidebar__theme-switcher -->
</div> <!-- sidebar -->