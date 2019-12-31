<?php
require_once '../core/init.php';
login_redirect(SITE_ROOT.'/admin/list-photos.php');
include_once '../includes/overall/header.php';
?>
<a href="<?php echo SITE_ROOT; ?>/index.php" class="link">&laquo; Return to Public Page</a>
<h1>Register</h1>
<?php
if (isset($_SESSION['success'])) {
    echo Session::instance()->flash('User successfully created', 'success', 'success');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['create'])) {
	    if (User::instance()->create_user(trim(escape($_POST['username'])), escape($_POST['password']))) {
	        $_SESSION['success'] = 'success';
	        Redirect::to($_SERVER['PHP_SELF']);
	    }
	}
}
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <table>
        <tr>
            <td><label for="username">Username:</label></td>
            <td><input type="text" name="username" id="username" autocomplete="off" value="<?php echo (isset($_POST['username'])) ? $_POST['username'] : ''; ?>"></td>
        </tr>
        <tr>
            <td><label for="password">Password:</label></td>
            <td><input type="password" name="password" id="password"></td>
        </tr>
        <tr>
            <td><button type="submit" name="create">Register</button></td>
        </tr>
    </table>
</form>
<p>Just signed up? <a href="<?php echo SITE_ROOT; ?>/admin/login.php" class="link">Login</a>.</p>

<?php include_once '../includes/overall/footer.php'; ?>