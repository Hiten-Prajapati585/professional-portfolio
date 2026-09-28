<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
$personal = $db->query("SELECT * FROM personal WHERE id=1")->fetch_assoc();
$skills = $db->query("SELECT * FROM skills ORDER BY sort_order,id")->fetch_all(MYSQLI_ASSOC);
$education = $db->query("SELECT * FROM education ORDER BY sort_order,id DESC")->fetch_all(MYSQLI_ASSOC);
$experience = $db->query("SELECT * FROM experience ORDER BY sort_order,id DESC")->fetch_all(MYSQLI_ASSOC);
$projects = $db->query("SELECT * FROM projects ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
$resume = $db->query("SELECT * FROM resumes WHERE id=1")->fetch_assoc();
$projectImgs = [];
foreach ($db->query("SELECT * FROM project_images ORDER BY id") as $img) $projectImgs[$img['project_id']][] = $img;
$page_title = ($personal['name'] ?: 'My') . ' | Portfolio';
require __DIR__ . '/includes/header.php';
$photo = $personal['profile_photo'] ?: 'https://placehold.co/600x600/e9ecef/6c757d?text=Profile';
?>
<nav class="navbar navbar-expand-lg navbar-dark fixed-top glass-nav">
    <div class="container"><a class="navbar-brand fw-bold" href="#home"><?= e($personal['name'] ?: 'My Portfolio') ?></a><button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#mainNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#education">Education</a></li>
                <li class="nav-item"><a class="nav-link" href="#skills">Skills</a></li>
                <li class="nav-item"><a class="nav-link" href="#experience">Experience</a></li>
                <li class="nav-item"><a class="nav-link" href="#projects">Projects</a></li>
                <li class="nav-item"><a class="nav-link" href="#resume">Resume</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
            </ul>
        </div>
    </div>
</nav>
<main>
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-7 order-2 order-lg-1"><span class="badge rounded-pill text-bg-primary mb-3"><?= e($personal['role']) ?></span>
                    <h1 class="display-3 fw-bold mb-3">Hi, I'm <span class="gradient-text"><?= e($personal['name']) ?></span></h1>
                    <p class="lead text-secondary mb-4"><?= e($personal['tagline']) ?></p>
                    <div class="d-flex flex-wrap gap-3"><a href="#projects" class="btn btn-primary btn-lg"><i class="bi bi-grid"></i> View Projects</a><a href="#contact" class="btn btn-outline-dark btn-lg"><i class="bi bi-envelope"></i> Contact Me</a></div>
                    <div class="social-links mt-4"><?php include __DIR__ . '/includes/social.php'; ?></div>
                </div>
                <div class="col-lg-5 text-center order-1 order-lg-2">
                    <div class="hero-photo-wrap"><img class="hero-photo" src="<?= e($photo) ?>" alt="Profile photo"></div>
                </div>
            </div>
        </div>
    </section>
    <section id="about" class="section-padding">
        <div class="container">
            <div class="section-heading"><span>ABOUT ME</span>
                <h2>Who I Am</h2>
            </div>
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="info-card h-100">
                        <p class="text-secondary mb-0"><?= nl2br(e($personal['about'])) ?></p>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="info-card h-100">
                        <div class="detail-list">
                            <div><i class="bi bi-envelope text-primary"></i><span><?= e($personal['email']) ?></span></div>
                            <div><i class="bi bi-telephone text-primary"></i><span><?= e($personal['phone']) ?></span></div>
                            <div><i class="bi bi-geo-alt text-primary"></i><span><?= e($personal['location']) ?></span></div>
                            <div><i class="bi bi-mortarboard text-primary"></i><span><?= e($personal['education_summary']) ?></span></div>
                            <div><i class="bi bi-translate text-primary"></i><span><?= e($personal['languages']) ?></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php if ($education): ?><section id="education" class="section-padding bg-light-subtle">
            <div class="container">
                <div class="section-heading"><span>EDUCATION</span>
                    <h2>Academic Journey</h2>
                </div>
                <div class="row g-4"><?php foreach ($education as $ed): ?><div class="col-lg-6">
                            <div class="info-card h-100"><span class="badge text-bg-primary mb-2"><?= e(trim($ed['start_date'] . ' - ' . $ed['end_date'], ' -')) ?></span>
                                <h4 class="fw-bold mb-1"><?= e($ed['degree']) ?></h4>
                                <h6 class="text-primary"><?= e($ed['institution']) ?></h6>
                                <p class="small text-secondary mb-2"><?= e($ed['location']) ?> <?= $ed['grade'] ? '• ' . e($ed['grade']) : '' ?></p>
                                <p class="text-secondary mb-0"><?= nl2br(e($ed['description'])) ?></p>
                            </div>
                        </div><?php endforeach; ?></div>
            </div>
        </section><?php endif; ?>
    <section id="skills" class="section-padding">
        <div class="container">
            <div class="section-heading"><span>MY SKILLS</span>
                <h2>Technologies I Use</h2>
            </div>
            <div class="row g-3"><?php foreach ($skills as $s): ?><div class="col-6 col-md-4 col-lg-3">
                        <div class="skill-card h-100"><i class="bi bi-code-slash text-primary fs-4"></i>
                            <h6 class="fw-bold mt-3 mb-1"><?= e($s['name']) ?></h6><small class="text-secondary"><?= e($s['category']) ?></small>
                        </div>
                    </div><?php endforeach; ?></div>
        </div>
    </section>
    <?php if ($experience): ?><section id="experience" class="section-padding bg-light-subtle">
            <div class="container">
                <div class="section-heading"><span>EXPERIENCE</span>
                    <h2>Work Experience</h2>
                </div>
                <div class="row g-4"><?php foreach ($experience as $ex): ?><div class="col-lg-6">
                            <div class="info-card h-100">
                                <div class="d-flex justify-content-between gap-3">
                                    <div>
                                        <h4 class="fw-bold mb-1"><?= e($ex['job_title']) ?></h4>
                                        <h6 class="text-primary"><?= e($ex['company']) ?></h6>
                                    </div><span class="badge text-bg-light align-self-start"><?= e(trim($ex['start_date'] . ' - ' . $ex['end_date'], ' -')) ?></span>
                                </div>
                                <p class="small text-secondary"><?= e($ex['location']) ?></p>
                                <p class="text-secondary mb-0"><?= nl2br(e($ex['description'])) ?></p>
                            </div>
                        </div><?php endforeach; ?></div>
            </div>
        </section><?php endif; ?>
    <section id="projects" class="section-padding">
        <div class="container">
            <div class="section-heading d-flex flex-column flex-md-row justify-content-between align-items-md-end">
                <div><span>PORTFOLIO</span>
                    <h2>Featured Projects</h2>
                </div>
                <p class="text-secondary mb-0">Selected work and academic projects.</p>
            </div>
            <div class="row g-4"><?php foreach ($projects as $p): $imgs = $projectImgs[$p['id']] ?? []; ?><div class="col-md-6 col-xl-4">
                        <article class="project-card"><img class="project-image" src="<?= e($p['cover_image'] ?: 'https://placehold.co/900x550/eaf2ff/0d6efd?text=Project') ?>" alt="<?= e($p['title']) ?>">
                            <div class="project-body"><span class="badge text-bg-light mb-2"><?= e($p['category']) ?></span>
                                <h4 class="fw-bold"><?= e($p['title']) ?></h4>
                                <p class="text-secondary"><?= e(mb_strimwidth($p['description'], 0, 150, '…')) ?></p>
                                <div class="mb-3"><?php foreach (array_filter(array_map('trim', explode(',', $p['technologies']))) as $t): ?><span class="tech-badge"><?= e($t) ?></span><?php endforeach; ?></div>
                                <div class="d-flex flex-wrap gap-2"><button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#projectModal<?= $p['id'] ?>"><i class="bi bi-eye"></i> View Details</button><?php if ($p['github_url']): ?><a class="btn btn-outline-dark btn-sm" target="_blank" href="<?= e(clean_url($p['github_url'])) ?>"><i class="bi bi-github"></i></a><?php endif; ?><?php if ($p['live_url']): ?><a class="btn btn-outline-primary btn-sm" target="_blank" href="<?= e(clean_url($p['live_url'])) ?>"><i class="bi bi-box-arrow-up-right"></i></a><?php endif; ?></div>
                            </div>
                        </article>
                    </div>
                    <div class="modal fade" id="projectModal<?= $p['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold"><?= e($p['title']) ?></h5><button class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-4">
                                        <div class="col-lg-6"><img class="w-100 rounded-4 project-detail-image" src="<?= e($p['cover_image'] ?: 'https://placehold.co/900x550/eaf2ff/0d6efd?text=Project') ?>" alt="">
                                            <div class="row g-2 mt-1"><?php foreach ($imgs as $im): ?><div class="col-4"><img class="w-100 rounded-3 gallery-thumb" src="<?= e($im['image_path']) ?>" alt=""></div><?php endforeach; ?></div>
                                        </div>
                                        <div class="col-lg-6"><span class="badge text-bg-primary mb-2"><?= e($p['category']) ?></span>
                                            <h3 class="fw-bold"><?= e($p['title']) ?></h3>
                                            <p class="text-secondary"><?= nl2br(e($p['description'])) ?></p>
                                            <p class="small text-secondary"><strong>Timeline:</strong> <?= e(trim($p['start_date'] . ' - ' . $p['completion_date'], ' -')) ?></p>
                                            <div class="mb-3"><?php foreach (array_filter(array_map('trim', explode(',', $p['technologies']))) as $t): ?><span class="tech-badge"><?= e($t) ?></span><?php endforeach; ?></div>
                                            <div class="d-flex flex-wrap gap-2"><?php if ($p['github_url']): ?><a target="_blank" class="btn btn-dark" href="<?= e(clean_url($p['github_url'])) ?>"><i class="bi bi-github"></i> GitHub</a><?php endif; ?><?php if ($p['live_url']): ?><a target="_blank" class="btn btn-primary" href="<?= e(clean_url($p['live_url'])) ?>"><i class="bi bi-globe"></i> Live Project</a><?php endif; ?><?php if ($p['pdf_file']): ?><a target="_blank" class="btn btn-outline-danger" href="<?= e($p['pdf_file']) ?>"><i class="bi bi-file-earmark-pdf"></i> View PDF</a><a download class="btn btn-outline-secondary" href="<?= e($p['pdf_file']) ?>"><i class="bi bi-download"></i> Download</a><?php endif; ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div><?php if (!$projects): ?><div class="empty-state"><i class="bi bi-folder2-open"></i>
                    <h5>No projects added yet</h5>
                </div><?php endif; ?>
        </div>
    </section>
    <section id="resume" class="section-padding bg-dark text-white">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-8"><span class="small text-uppercase opacity-75">Resume</span>
                    <h2 class="display-6 fw-bold mt-2">Want to know more about my experience?</h2>
                    <p class="opacity-75 mb-0">View or download my latest resume.</p>
                </div>
                <div class="col-lg-4">
                    <div class="d-flex flex-wrap gap-2 justify-content-lg-end"><?php if ($resume['file_path']): ?><a target="_blank" href="<?= e($resume['file_path']) ?>" class="btn btn-light"><i class="bi bi-eye"></i> View Resume</a><a download href="<?= e($resume['file_path']) ?>" class="btn btn-outline-light"><i class="bi bi-download"></i> Download</a><?php endif; ?><?php if ($personal['canva_url']): ?><a target="_blank" href="<?= e(clean_url($personal['canva_url'])) ?>" class="btn btn-outline-light"><i class="bi bi-palette"></i> Create on Canva</a><?php endif; ?></div>
                </div>
            </div>
        </div>
    </section>
    <section id="contact" class="section-padding">
        <div class="container">
            <div class="section-heading"><span>GET IN TOUCH</span>
                <h2>Contact Me</h2>
            </div>
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="info-card h-100">
                        <h5 class="fw-bold mb-4">Let's connect</h5>
                        <div class="contact-list"><?php if ($personal['email']): ?><a href="mailto:<?= e($personal['email']) ?>"><i class="bi bi-envelope text-primary"></i><?= e($personal['email']) ?></a><?php endif; ?><?php if ($personal['phone']): ?><a href="tel:<?= e($personal['phone']) ?>"><i class="bi bi-telephone text-primary"></i><?= e($personal['phone']) ?></a><?php endif; ?><?php if ($personal['location']): ?><div><i class="bi bi-geo-alt text-primary"></i><?= e($personal['location']) ?></div><?php endif; ?></div>
                        <div class="social-links mt-4"><?php include __DIR__ . '/includes/social.php'; ?></div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="contact-cta h-100">
                        <div><span class="badge text-bg-light mb-3">Available for opportunities</span>
                            <h3 class="fw-bold">Have a project or opportunity?</h3>
                            <p class="text-secondary">Feel free to contact me using the details provided here.</p>
                        </div><?php if ($personal['email']): ?><a href="mailto:<?= e($personal['email']) ?>" class="btn btn-primary"><i class="bi bi-send"></i> Send Email</a><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<footer class="py-4 border-top">
    <div class="container d-flex flex-column flex-md-row justify-content-between gap-2"><span class="text-secondary">© <?= date('Y') ?> <?= e($personal['name']) ?></span><a href="admin/login.php" class="text-decoration-none">Admin Login</a></div>
</footer>
<?php require __DIR__ . '/includes/footer.php'; ?>