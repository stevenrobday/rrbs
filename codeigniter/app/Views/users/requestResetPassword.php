<div class="container">
    <div class="row vh-100 align-items-center justify-content-center">
        <div class="col-12 col-md-8 col-lg-5 bg-light rounded p-3">
            <div class="mb-3">
                <img class="w-100 h-auto" src="<?php echo base_url("assets/img/logo.png"); ?>">
            </div>
            <h2 class="mb-3">Password Reset Request</h2>
            <form action="<?php echo base_url('submitResetPassword'); ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-floating mb-3">
                    <?php $emailMsg = session()->getFlashdata('email'); ?>
                    <input type="text" name="email" id="email" placeholder="email" value="<?= set_value('email') ?>"
                           class="form-control <?php if($emailMsg) echo 'is-invalid'; ?>"
                           aria-describedby="emailFeedback">
                    <label for="email" class="form-label">Username or Email</label>
                    <?php if($emailMsg):?>
                        <div id="emailFeedback" class="invalid-feedback">
                            <?= $emailMsg ?>
                        </div>
                    <?php endif;?>
                </div>
                <?php $successMsg = session()->getFlashdata('success');
                if($successMsg):?>
                    <div class="mb-3 text-success">
                        <?= $successMsg ?>
                    </div>
                <?php endif;?>
                <div class="d-grid">
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>