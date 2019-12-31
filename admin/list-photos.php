<?php
require_once '../core/init.php';
admin_protect();
$pagination = new Pagination(5);
$count = DB::instance()->select_by_sql("SELECT COUNT(*) AS `count` FROM `images`", array());
$count = array_shift($count);
$pagination->total = $count->count;
$pictures = DB::instance()->select_by_sql("SELECT * FROM `images` ORDER BY `upload_date` DESC LIMIT ? OFFSET ?", array($pagination->limit, $pagination->get_offset()));

include_once '../includes/overall/header.php';
    if (isset($_SESSION['success'])) {
        echo Session::instance()->flash('You are successfully logged in', 'success', 'success');
        echo '<br/>';
    }
?>
<a href="<?php echo SITE_ROOT; ?>/index.php" class="link">&laquo; Return to Public Page</a>
<h1>Welcome to The Admin Page, <?php echo ucfirst(strtolower(User::instance()->user_data($_SESSION['id'])->username)); ?></h1>
<?php
    if (isset($_SESSION['upload'])) {
        echo Session::instance()->flash('file upload successful', 'success', 'upload');
    }
    if (isset($_SESSION['delete'])) {
        echo Session::instance()->flash('file delete successful', 'success', 'delete');
    }
    if (isset($_SESSION['update'])) {
        echo Session::instance()->flash('file visibility change successful', 'success', 'update');
    }
?>
<?php if (count($pictures) === 0): ?>
    <?php if (isset($_GET['page'])): ?>
    <h1>This page does not exist or has been deleted</h1>
    <?php else: ?>
    <h2>No pictures yet, Upload a picture today</h2>
    <h3>Rules for uploading pictures</h3>
    <ol>
        <li>You can upload multiple pictures at a time. Multiple pictures bear the same caption with a numbering pattern on the name of each picture</li>
        <li>Each picture must not be more than the upload_max_filesize in the php.ini file</li>
        <li>For this project, you cannot upload more than 18 pictures at a time</li>
        <li>The sum of the size of all the pictures you want to upload must not be more than the post_max_size in the php.ini file</li>
        <li>The name can only contain alphabets, numbers and spaces</li>
    </ol>
    <h3>Happy Uploading!!!</h3>
    <?php endif;?>
<?php else: ?>
<table class="table">
    <tr>
        <th>Image</th>
        <th>Filename</th>
        <th>Caption</th>
        <th>Size</th>
        <th>Type</th>
        <th>Comments</th>
        <th>Visible</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
    </tr>
<?php
foreach ($pictures as $picture): ?>
    <tr>
        <td><img src="<?php echo SITE_ROOT ?>/images/<?php echo $picture->img_name; ?>" class="list-photos"/></td>
        <td><?php echo $picture->img_name; ?></td>
        <td><?php echo $picture->caption; ?></td>
        <td><?php echo $picture->size; ?></td>
        <td><?php echo $picture->type; ?></td>
        <td><?php echo $count = Comment::instance()->count($picture->id);
            echo ($count === 1) ? ' comment' : ' comments'; 
        ?></td>
        <td><?php echo ($picture->visible === 1) ? 'Yes' : 'No' ?></td>
        <td><form action="<?php echo SITE_ROOT; ?>/admin/update-photo.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $picture->id; ?>">
            <button type="submit" name="update" title="<?php echo $picture->img_name ?>"><?php echo ($picture->visible === 0) ? 'Make Visible' : 'Make Invisible'; ?></button>
        </form></td>
        <td><form action="<?php echo SITE_ROOT; ?>/admin/delete-photo.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $picture->id; ?>">
            <button type="submit" name="delete" title="<?php echo $picture->img_name ?>">Delete</button>
        </form></td>
    </tr>
<?php endforeach; ?>
</table>
<?php endif; ?>
<br/>  
<?php if ($pagination->has_pagination()): ?>
    <div id="pagination">
    <?php if ($pagination->has_prev()): ?>
        <a href="<?php echo SITE_ROOT; ?>/admin/list-photos.php?page=<?php echo $pagination->first(); ?>" class="link">First</a>
        <a href="<?php echo SITE_ROOT; ?>/admin/list-photos.php?page=<?php echo $pagination->current_page() - 1; ?>" class="link">Prev</a>
    <?php endif; ?>
    <?php if ($pagination->has_next()): ?>
        <a href="<?php echo SITE_ROOT; ?>/admin/list-photos.php?page=<?php echo $pagination->current_page() + 1; ?>" class="link">Next</a>
        <a href="<?php echo SITE_ROOT; ?>/admin/list-photos.php?page=<?php echo $pagination->last(); ?>" class="link">Last</a>
    <?php endif; ?>
    <br/><br/>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="goto-form">
        <label for="page">Go to Page:
        <input type="number" name="page" class="input-page" id="page" value="<?php echo $pagination->current_page(); ?>" min="<?php echo $pagination->first(); ?>" max="<?php echo $pagination->last(); ?>" /> of <?php echo $pagination->last(); ?></label>&nbsp;&nbsp;&nbsp;
        <button type="submit" name="go">Go</button>
    </form>
<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['go'])) {
            $pagination->jump_to(trim($_POST['page']), SITE_ROOT.'/admin/list-photos.php?page='.$_POST['page']);
        }
    }
?>
    </div>
<?php endif; ?>   
<br/><br/>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data" class="upload-form">
<table>
    <tr>
        <td><label for="name">Name: </label></td>
        <td><input type="text" name="img_name" id="name" autocomplete="off" value="<?php echo (isset($_POST['img_name'])) ? $_POST['img_name'] : '' ; ?>" /></td>
    </tr>
    <tr>
        <td><label for="caption">Caption: </label></td>
        <td><input type="text" name="caption" id="caption" autocomplete="off" value="<?php echo (isset($_POST['caption'])) ? $_POST['caption'] : '' ; ?>" /></td>
    </tr>
    <tr>
        <td><label>Visible: </label></td>
        <td>
            <label for="no">No</label><input type="radio" name="visible" value="0" id="no" <?php echo (isset($_POST['visible']) && $_POST['visible'] === '0') ? 'checked' : ''; ?>/>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <label for="yes">Yes</label><input type="radio" name="visible" value="1" id="yes" <?php echo (isset($_POST['visible']) && $_POST['visible'] === '1') ? 'checked' : ''; ?>/>
        </td>
    </tr>
</table>
<input type="file" name="file[]" multiple><br/>
<button type="submit" name="upload">Upload Image</button>
</form>
<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['upload'])) {
            if (Image::instance()->upload($_FILES['file'], trim($_POST['img_name']), trim($_POST['caption']), $_POST['visible'])) {
                $_SESSION['upload'] = 'success';
                Redirect::to($_SERVER['PHP_SELF']);
            }
        }
    }

include_once '../includes/overall/footer.php';
?>