<?php
require_once 'core/init.php';
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $picture = DB::instance()->select_by_sql("SELECT * FROM `images` WHERE `id` = ?", array($id));
        if (count($picture) === 0) {
            die('<h1>This picture does not exist or has already been deleted</h1>');
        } else {
            $pic = array_shift($picture);
            if ($pic->visible === 0) {
                die('<h1>This picture cannot be viewed at the moment. Please try again later</h1>');
            }
        }
    } else {
        Redirect::to(SITE_ROOT.'/index.php');
    }
$pagination = new Pagination(2);
$pagination->total = Comment::instance()->count($id);
$comments = DB::instance()->select_by_sql("SELECT * FROM `comments` WHERE `img_id` = ? ORDER BY `date` DESC LIMIT ? OFFSET ?", array($id, $pagination->limit, $pagination->get_offset()));

include_once 'includes/overall/header.php';
?>
    <?php if (isset($_SESSION['id'])): ?>
<a href="<?php echo SITE_ROOT; ?>/admin/list-photos.php" class="link">&raquo; Go to Admin Page</a>
    <?php endif; ?>
<h1>Image View</h1>
<?php
    if (isset($_SESSION['comment'])) {
        echo Session::instance()->flash('Comment Posted Successfully', 'success', 'comment');
        echo '<br/>';
    }
?>
<a href="<?php echo (isset($_SERVER['HTTP_REFERER']) && pathinfo($_SERVER['HTTP_REFERER'], PATHINFO_FILENAME) === 'index') ? $_SERVER['HTTP_REFERER'] : SITE_ROOT.'/index.php'; ?>" class="link">&laquo; Back to Gallery Page</a>
<br/><br/>
<img src="<?php echo SITE_ROOT ?>/images/<?php echo $pic->img_name; ?>" class="image-view">
<br/><br/>
<!--Download-->
<div class="download">
    <form action="<?php echo SITE_ROOT; ?>/download.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $pic->id; ?>">
        <button type="submit" name="download">Download</button>
    </form>
</div>
<!--End Download-->
    <?php if (count($comments) === 0): ?>
        <?php if (isset($_GET['page'])): ?>
            &nbsp;
        <?php else: ?>
            <h3>No comments on this post</h3>
        <?php endif; ?>
    <?php else: ?>
        <?php foreach ($comments as $comment): ?>
            <div class="comments">
                <h4><?php echo $comment->name; ?></h4>
                <p><?php echo $comment->comment; ?></p>
                <h4><?php echo time_convert($comment->date); ?></h4>
                <br/>
            </div>
        <?php endforeach; ?>
<?php if ($pagination->has_pagination()): ?>
        <div>
    <?php if ($pagination->has_prev()): ?>
        <a href="<?php echo SITE_ROOT; ?>/photo.php?id=<?php echo $id; ?>&page=<?php echo $pagination->first(); ?>" class="link">First</a>
        <a href="<?php echo SITE_ROOT; ?>/photo.php?id=<?php echo $id; ?>&page=<?php echo $pagination->current_page() - 1; ?>" class="link">Prev</a>
    <?php endif; ?>
    <?php if ($pagination->has_next()): ?>
        <a href="<?php echo SITE_ROOT; ?>/photo.php?id=<?php echo $id; ?>&page=<?php echo $pagination->current_page() + 1; ?>" class="link">Next</a>
        <a href="<?php echo SITE_ROOT; ?>/photo.php?id=<?php echo $id; ?>&page=<?php echo $pagination->last(); ?>" class="link">Last</a>
    <?php endif; ?>
    <br/><br/>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $id; ?>" method="POST" class="goto-form">
        <label for="page">Go to Comment Page: 
        <input type="number" name="page" class="input-page" id="page" value="<?php echo $pagination->current_page(); ?>" min="<?php echo $pagination->first(); ?>" max="<?php echo $pagination->last(); ?>" /> of <?php echo $pagination->last(); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
        <button type="submit" name="go">Go</button>
    </form>
<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['go'])) {
            $pagination->jump_to(trim($_POST['page']), SITE_ROOT.'/photo.php?id='.$id.'&page='.$_POST['page']);
        }
    }
?>
        </div>
<?php endif; ?>
        <br/><br/>
    <?php endif; ?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $id ?>" method="POST">
    <table class="comment">
        <tr>
            <td><label for="name">Your Name:</label></td>
            <td><input type="text" name="name" id="name" autocomplete="off" value="<?php echo (isset($_POST['name'])) ? $_POST['name'] : ''; ?>"></td>
        </tr>
        <tr>
            <td><label for="comment">Comment:</label></td>
            <td><textarea name="comment" id="comment" cols="50" rows="10"><?php echo (isset($_POST['comment'])) ? $_POST['comment'] : ''; ?></textarea></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><button type="submit" name="post_comment">Post Comment</button></td>
        </tr>
    </table>
</form>
<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['post_comment'])) {
            if (Comment::instance()->post_comment($id, trim($_POST['name']), escape(trim($_POST['comment'])))) {
                $_SESSION['comment'] = 'success';
                Redirect::to(SITE_ROOT.'/photo.php?id='.$id);
            }
        }
    }

include_once 'includes/overall/footer.php';
?>