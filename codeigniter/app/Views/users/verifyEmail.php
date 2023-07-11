<div class="container">
    <div class="row vh-100 align-items-center justify-content-center">
        <div class="col-12 col-md-8 col-lg-5 bg-light rounded p-3">
            <div class="mb-3">
                <img class="w-100 h-auto" src="<?php echo base_url("assets/img/logo.png"); ?>">
            </div>
            <h2>Account Verification</h2>
            <?php if (isset($success)): ?>
                <div id="verificationSuccess" class="text-success">
                    Your account was successfully verified.
                    You will be redirected to the homepage in 5 seconds.
                    Otherwise, click <a href="<?php echo base_url(); ?>">here</a>.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
