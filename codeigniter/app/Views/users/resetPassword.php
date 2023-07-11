<div class="container">
    <div class="row vh-100 align-items-center justify-content-center">
        <div class="col-12 col-md-8 col-lg-5 bg-light rounded p-3">
            <div class="mb-3">
                <img class="w-100 h-auto" src="<?php echo base_url("assets/img/logo.png"); ?>">
            </div>
            <h2>Password Reset</h2>
            <?= session()->getFlashdata('error') ?>
            <form action='<?php echo base_url("resetPassword/$username/$token1/$token2"); ?>' method="post">
                <?= csrf_field() ?>
                <div class="form-floating mb-3">
                    <input type="password" name="password" id="password" value="<?= set_value('password') ?>"
                           class="form-control <?php if(isset($validation) && $validation->hasError('password')) echo 'is-invalid'; ?>"
                           maxlength="30" aria-describedby="passwordFeedback" placeholder="password">
                    <label for="password" class="form-label">Password</label>
                    <?php if(isset($validation)):?>
                        <div id="passwordFeedback" class="invalid-feedback">
                            <?= $validation->showError('password') ?>
                        </div>
                    <?php endif;?>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" name="confirmPassword" id="confirmPassword" value="<?= set_value('confirmPassword') ?>"
                           class="form-control <?php if(isset($validation) && $validation->hasError('confirmPassword')) echo 'is-invalid'; ?>"
                           maxlength="30" aria-describedby="confirmPasswordFeedback" placeholder="password">
                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                    <?php if(isset($validation)):?>
                        <div id="confirmPasswordFeedback" class="invalid-feedback">
                            <?= $validation->showError('confirmPassword') ?>
                        </div>
                    <?php endif;?>
                </div>
                <?php if (!isset($success)): ?>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                <?php else: ?>
                    <div id="success" class="text-success">
                        Your password was successfully reset.
                        You will be redirected to the Sign In page in 5 seconds.
                        Otherwise, click <a href="<?php echo base_url('signIn'); ?>">here</a>.
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>