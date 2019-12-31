<?php
require_once 'core/init.php';
$pagination = new Pagination(6);
$pictures = DB::instance()->select_by_sql("SELECT * FROM `images` WHERE `visible` = ? ORDER BY `upload_date` DESC LIMIT ? OFFSET ?", array(1, $pagination->limit, $pagination->get_offset()));
$count = DB::instance()->select_by_sql("SELECT COUNT(*) AS `count` FROM `images` WHERE `visible` = ?", array(1));
$count = array_shift($count);
$pagination->total = $count->count;
include_once 'includes/overall/header.php';
?>
<?php if (isset($_SESSION['id'])):  ?>
    <a href="<?php echo SITE_ROOT; ?>/admin/list-photos.php" class="link">&raquo; Go to Admin Page</a>
<?php endif; ?>
    <h1>Welcome to Our Photo Gallery</h1>
<?php if (count($pictures) === 0): ?>
        <?php if (isset($_GET['page'])): ?>
            <h1>This page does not exist or has been deleted</h1>
        <?php else: ?>
            <h2>There are currently no pictures in our site.</h2>
        <?php endif; ?>
<?php endif; ?>
    <div class="picture-body">
<?php foreach ($pictures as $picture): ?>
        <div class="pictures">
    <a href="<?php echo SITE_ROOT; ?>/photo.php?id=<?php echo $picture->id; ?>" ><img src="<?php echo SITE_ROOT; ?>/images/<?php echo $picture->img_name; ?>" class="user-list-photos" title="<?php echo $picture->caption; ?>"/></a>
        </div>
<?php endforeach; ?>
<?php if ($pagination->has_pagination()): ?>
        <div id="pagination">
    <?php if ($pagination->has_prev()): ?>
        <a href="<?php echo SITE_ROOT; ?>/index.php?page=<?php echo $pagination->first(); ?>" class="link">First</a>
        <a href="<?php echo SITE_ROOT; ?>/index.php?page=<?php echo $pagination->current_page() - 1; ?>" class="link">Prev</a>
    <?php endif; ?>
    <?php if ($pagination->has_next()): ?>
        <a href="<?php echo SITE_ROOT; ?>/index.php?page=<?php echo $pagination->current_page() + 1; ?>" class="link">Next</a>
        <a href="<?php echo SITE_ROOT; ?>/index.php?page=<?php echo $pagination->last(); ?>" class="link">Last</a>
    <?php endif; ?>
    <br/><br/>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="goto-form">
        <label for="page">Go to Page: 
        <input type="number" name="page" class="input-page" id="page" value="<?php echo $pagination->current_page(); ?>" min="<?php echo $pagination->first(); ?>" max="<?php echo $pagination->last(); ?>" /> of <?php echo $pagination->last(); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
        <button type="submit" name="go">Go</button>
    </form>
<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['go'])) {
            $pagination->jump_to(trim($_POST['page']), SITE_ROOT.'/index.php?page='.$_POST['page']);
        }
    }
?>
        </div>
<?php endif; ?>
    </div>
<?php if (!isset($_SESSION['id'])): ?>
<br/><br/><br/>
    <h4><a href="<?php echo SITE_ROOT; ?>/admin/add-user.php" class="link">Register</a> with us to upload pictures</h4>
    <h4>Already signed up? <a href="<?php echo SITE_ROOT; ?>/admin/login.php" class="link">Login</a></h4>
<?php endif; ?>
<?php include_once 'includes/overall/footer.php'; ?>