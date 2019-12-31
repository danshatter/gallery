<!DOCTYPE html>
<html lang="en">
<head>
    <title></title>
    <link rel="stylesheet" href="<?php echo SITE_ROOT; ?>/css/style.css">
</head>
<body>
    <div class="wrapper">
<?php if (isset($_SESSION['id'])): ?>
<form action="<?php echo SITE_ROOT; ?>/admin/logout.php" method="POST">
    <button name="logout" class="logout">Logout</button>
</form>
<?php endif; ?>

