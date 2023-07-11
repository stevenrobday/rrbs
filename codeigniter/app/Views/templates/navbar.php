<nav id="navbar" class="navbar navbar-expand navbar-dark bg-dark border-bottom border-secondary">
    <a class="navbar-brand ms-2" href="#">
        <a href="<?= base_url() ?>">
            <img src="<?= base_url("assets/img/logo.png") ?>" width="auto" height="50" alt="">
        </a>
    </a>
    <ul class="navbar-nav ms-auto me-2">
        <?php if (!$session->isLoggedIn): ?>
            <li class="nav-item">
                <span><a class="nav-link d-inline-block" href="<?= base_url("signIn") ?>">Sign In</a> <span
                            class="text-light">/</span> <a class="nav-link d-inline-block"
                                                           href="<?= base_url("signUp") ?>">Sign Up</a></span>
            </li>
        <?php elseif ($session->verified): ?>
            <?php if ($session->level === 'Admin'): ?>
                <li class="nav-item d-flex align-items-center">
                    <a class="nav-link d-inline-block py-0" role="button" href="#" data-bs-toggle="modal" data-bs-target="#profileModal">
                        <?php if (strlen($session->img)): ?>
                            <span id="profileImg">
                                <img alt="profile_picture" id="profilePic" src="/assets/img/profile/<?= $session->img ?>">
                            </span>
                        <?php else: ?>
                            <span id="profileImg" class="fa-stack fa-lg">
                                <i class="fa-regular fa-circle fa-stack-2x"></i>
                                <i class="fa-solid fa-user fa-stack-1x"></i>
                            </span>
                        <?php endif; ?>
                        <span class="va-m"><?= htmlspecialchars($session->username) ?></span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link" href="#" id="navbarDropdown" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-sharp fa-solid fa-bars fa-lg py-0 va-m"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="<?= base_url('videos') ?>">Videos</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('schedule') ?>">Schedule</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('categories') ?>">Categories</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('invite') ?>">Invite</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('suggestedVideos') ?>">Suggested Videos</a></li>
                        <li><a class="videoLog dropdown-item" href="#">Video Log</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('hiScores') ?>">Hi Scores</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('donate') ?>">Donate</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="<?= base_url('signOut') ?>">Sign Out</a></li>
                    </ul>
                </li>
            <?php elseif ($session->level === 'User'): ?>
                <li class="nav-item d-flex align-items-center">
                    <a class="nav-link d-inline-block py-0" role="button" href="#" data-bs-toggle="modal" data-bs-target="#profileModal">
                        <?php if (strlen($session->img)): ?>
                            <span id="profileImg">
                                <img alt="profile_picture" id="profilePic" src="/assets/img/profile/<?= $session->img ?>">
                            </span>
                        <?php else: ?>
                            <span id="profileImg" class="fa-stack fa-lg">
                                <i class="fa-regular fa-circle fa-stack-2x"></i>
                                <i class="fa-solid fa-user fa-stack-1x"></i>
                            </span>
                        <?php endif; ?>
                        <span class="va-m"><?= htmlspecialchars($session->username) ?></span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link" href="#" id="navbarDropdown" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-sharp fa-solid fa-bars fa-lg py-0 va-m"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <?php if ($session->invited) : ?>
                            <li><a class="dropdown-item" href="<?= base_url('suggestVideos') ?>">Suggest Videos</a></li>
                        <?php endif; ?>
                        <li><a class="videoLog dropdown-item" href="#">Video Log</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('hiScores') ?>">Hi Scores</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('donate') ?>">Donate</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="<?= base_url('signOut') ?>">Sign Out</a></li>
                    </ul>
                </li>
            <?php endif; ?>
        <?php else: ?>
            <li class="nav-item text-light d-flex align-items-center">
                <span style="font-size: 16px">Welcome <?= htmlspecialchars($session->username) ?>, please check your email to verify your account.</span>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link" href="#" id="navbarDropdown" role="button"
                   data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-sharp fa-solid fa-bars fa-lg py-0 va-m"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="<?= base_url('signOut') ?>">Sign Out</a></li>
                </ul>
            </li>
        <?php endif; ?>
    </ul>
</nav>
