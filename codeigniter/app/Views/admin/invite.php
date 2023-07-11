<div class="container">
    <div class="row align-items-center justify-content-center my-3">
        <div class="col-12 col-md-4 text-center mt-3 mt-md-0">
            <form class="form-floating" autocomplete="off">
                <select class="form-select" id="inviteSelect">
                    <option value="0" <?php if ($invited === 0) echo 'selected'; ?>>Not Invited</option>
                    <option value="1" <?php if ($invited === 1) echo 'selected'; ?>>Invited</option>
                </select>
                <label for="inviteSelect">Select User Category</label>
            </form>
        </div>
    </div>
<?php
$bg = 'bg-black';
foreach ($users as $user): ?>
    <form id="inviteForm_<?= $user['id'] ?>" class="row align-items-center justify-content-center py-3 <?= $bg ?>">
        <?= csrf_field() ?>
        <div class="col-12 col-sm-4 col-md-3 text-center text-light">
            <?= htmlspecialchars($user['username']) ?>
        </div>
        <div class="col-12 col-sm-8 col-md-7 text-center text-light">
            <?= htmlspecialchars($user['email']) ?>
        </div>
        <div class="col-12 col-sm-3 col-md-2 text-center">
            <?php if ($invited === 0): ?>
                <button type="button" class="inviteBtn btn btn-success" data-id="<?= $user['id'] ?>" data-invited="1">Invite</button>
            <?php elseif ($invited === 1): ?>
                <button type="button" class="inviteBtn btn btn-danger" data-id="<?= $user['id'] ?>" data-invited="0">Uninvite</button>
            <?php endif; ?>
            <div class="inviteErrorMsg text-danger" id="inviteErrorMsg_<?= $user['id'] ?>"></div>
        </div>
    </form>
    <?php
    if ($bg === "bg-dark") $bg = "bg-black";
    elseif ($bg === "bg-black") $bg = "bg-dark";
endforeach; ?>
</div>
