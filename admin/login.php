<?php
require_once '../core/init.php';
login_redirect(SITE_ROOT.'/admin/list-photos.php');
include_once '../includes/overall/header.php';
?>

<a href="<?php echo SITE_ROOT; ?>/index.php" class="link">&laquo; Return to Public Page</a>
<h1 class="login">Login</h1>
<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['login'])) {
            User::instance()->login_validate(escape(trim($_POST['username'])), $_POST['password']);
        }
    }
?>
<div class="login-form">
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <table>
        <tr>
            <td><label for="username">Username:</label></td>
            <td><input type="text" name="username" id="username" autocomplete="off" value="<?php echo (isset($_POST['username'])) ? $_POST['username'] : ''; ?>" /></td>
        </tr>
        <tr>
            <td><label for="password">Password:</label></td>
            <td><input type="password" name="password" id="password"></td>
        </tr>
        <tr>
            <td><button type="submit" name="login">Login</button></td>
        </tr>
    </table>
</form>
</div>
<p>Not registered yet? <a href="<?php echo SITE_ROOT; ?>/admin/add-user.php" class="link">Create an account</a>.</p>

<?php include_once '../includes/overall/footer.php'; ?>