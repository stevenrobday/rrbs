<div class="container">
    <div class="row vh-100 align-items-center justify-content-center">
        <div class="col-12 col-md-8 col-lg-5 bg-light rounded p-3">
            <div class="mb-3">
                <img class="w-100 h-auto" src="<?php echo base_url("assets/img/logo.png"); ?>">
            </div>
            <h2 class="mb-3">Sign In</h2>
            <?= session()->getFlashdata('error') ?>
            <form action="<?php echo base_url('SignInController/loginAuth'); ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-floating mb-3">
                    <input type="text" name="email" id="email" placeholder="email" value="<?= set_value('email') ?>"
                           class="form-control <?php if(session()->getFlashdata('email')) echo 'is-invalid'; ?>"
                           aria-describedby="emailFeedback">
                    <label for="email" class="form-label">Username or Email</label>
                    <?php if(session()->getFlashdata('email')):?>
                        <div id="emailFeedback" class="invalid-feedback">
                            <?= session()->getFlashdata('email') ?>
                        </div>
                    <?php endif;?>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" name="password" id="password" placeholder="password" value="<?= set_value('password') ?>"
                           class="form-control <?php if(session()->getFlashdata('password')) echo 'is-invalid'; ?>"
                           aria-describedby="passwordFeedback">
                    <label for="password" class="form-label">Password</label>
                    <?php if(session()->getFlashdata('password')):?>
                        <div id="passwordFeedback" class="invalid-feedback">
                            <?= session()->getFlashdata('password') ?>
                        </div>
                    <?php endif;?>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="Yes" name="rememberMe" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">
                        Remember Me
                    </label>
                </div>
                <div class="mb-3">
                    Not registered? <a href="<?php echo base_url('signUp'); ?>">Sign Up!</a>
                </div>
                <div class="mb-3">
                    <a href="<?php echo base_url('requestResetPassword'); ?>">Forgot Password?</a>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-success">Sign In</button>
                </div>
            </form>
        </div>
    </div>
</div>