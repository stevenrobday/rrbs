<div class="container">
    <div class="row vh-100 align-items-center justify-content-center">
        <div class="col-12 col-md-8 col-lg-5 bg-light rounded p-3">
            <div class="mb-3">
                <img class="w-100 h-auto" src="<?php echo base_url("assets/img/logo.png"); ?>">
            </div>
            <h2>Sign Up</h2>
            <?= session()->getFlashdata('error') ?>
            <form action="<?php echo base_url('SignUpController/store'); ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-floating mb-3">
                    <input type="text" name="username" id="username" value="<?= set_value('username') ?>"
                           class="form-control <?php if(isset($validation) && $validation->hasError('username')) echo 'is-invalid'; ?>"
                           maxlength="20" aria-describedby="usernameFeedback" placeholder="username">
                    <label for="username" class="form-label">Username</label>
                    <?php if(isset($validation)):?>
                        <div id="usernameFeedback" class="invalid-feedback">
                            <?= $validation->showError('username') ?>
                        </div>
                    <?php endif;?>
                </div>
                <div class="form-floating mb-3">
                    <input type="email" name="email" id="email" value="<?= set_value('email') ?>"
                           class="form-control <?php if(isset($validation) && $validation->hasError('email')) echo 'is-invalid'; ?>"
                           maxlength="50" aria-describedby="emailFeedback" placeholder="email">
                    <label for="email" class="form-label">Email</label>
                    <?php if(isset($validation)):?>
                        <div id="emailFeedback" class="invalid-feedback">
                            <?= $validation->showError('email') ?>
                        </div>
                    <?php endif;?>
                </div>
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
                <div class="mb-3">
                    Already registered? <a href="<?php echo base_url('signIn'); ?>">Sign In!</a>
                </div>
                <?php if (isset($success)): ?>
                    <div class="mb-3 text-success">
                        Your account was successfully created! Please check your inbox or spam folder for an email from us to verify your account.
                    </div>
                <?php endif; ?>
                <div class="d-grid">
                    <button type="submit" class="btn btn-success">Sign Up</button>
                </div>
            </form>
        </div>
    </div>
</div>