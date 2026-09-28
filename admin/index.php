<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();
$tab = $_GET['tab'] ?? 'dashboard';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        verify_csrf();
        $action = $_POST['action'] ?? '';
        if ($action === 'personal') {
            $p = $db->query('SELECT profile_photo FROM personal WHERE id=1')->fetch_assoc();
            $photo = $p['profile_photo'] ?? '';
            if (!empty($_POST['remove_photo'])) {
                delete_file($photo);
                $photo = '';
            }
            if (!empty($_FILES['profile_photo']['name'])) {
                $new = save_upload($_FILES['profile_photo'], 'profile', ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], 5);
                delete_file($photo);
                $photo = $new;
            }
            $sql = 'UPDATE personal SET name=?,role=?,tagline=?,about=?,email=?,phone=?,location=?,education_summary=?,languages=?,profile_photo=?,github=?,linkedin=?,instagram=?,facebook=?,twitter=?,canva_url=? WHERE id=1';
            $st = $db->prepare($sql);
            $vals = [trim($_POST['name']), trim($_POST['role']), trim($_POST['tagline']), trim($_POST['about']), trim($_POST['email']), trim($_POST['phone']), trim($_POST['location']), trim($_POST['education_summary']), trim($_POST['languages']), $photo, clean_url($_POST['github'] ?? ''), clean_url($_POST['linkedin'] ?? ''), clean_url($_POST['instagram'] ?? ''), clean_url($_POST['facebook'] ?? ''), clean_url($_POST['twitter'] ?? ''), clean_url($_POST['canva_url'] ?? '')];
            $st->bind_param(str_repeat('s', 16), ...$vals);
            $st->execute();
            flash('success', 'Personal information saved.');
            $tab = 'personal';
        } elseif ($action === 'skill_save') {
            $id = (int)$_POST['id'];
            $name = trim($_POST['name']);
            $cat = trim($_POST['category']);
            $sort = (int)$_POST['sort_order'];
            if ($id) {
                $st = $db->prepare('UPDATE skills SET name=?,category=?,sort_order=? WHERE id=?');
                $st->bind_param('ssii', $name, $cat, $sort, $id);
            } else {
                $st = $db->prepare('INSERT INTO skills(name,category,sort_order) VALUES(?,?,?)');
                $st->bind_param('ssi', $name, $cat, $sort);
            }
            $st->execute();
            flash('success', 'Skill saved.');
            $tab = 'skills';
        } elseif ($action === 'skill_delete') {
            $id = (int)$_POST['id'];
            $st = $db->prepare('DELETE FROM skills WHERE id=?');
            $st->bind_param('i', $id);
            $st->execute();
            flash('success', 'Skill deleted.');
            $tab = 'skills';
        } elseif ($action === 'education_save') {
            $id = (int)$_POST['id'];
            $degree = trim($_POST['degree']);
            $institution = trim($_POST['institution']);
            $location = trim($_POST['location']);
            $start = trim($_POST['start_date']);
            $end = trim($_POST['end_date']);
            $grade = trim($_POST['grade']);
            $description = trim($_POST['description']);
            $sort = (int)$_POST['sort_order'];
            if ($id) {
                $st = $db->prepare('UPDATE education SET degree=?,institution=?,location=?,start_date=?,end_date=?,grade=?,description=?,sort_order=? WHERE id=?');
                $st->bind_param('sssssssii', $degree, $institution, $location, $start, $end, $grade, $description, $sort, $id);
            } else {
                $st = $db->prepare('INSERT INTO education(degree,institution,location,start_date,end_date,grade,description,sort_order) VALUES(?,?,?,?,?,?,?,?)');
                $st->bind_param('sssssssi', $degree, $institution, $location, $start, $end, $grade, $description, $sort);
            }
            $st->execute();
            flash('success', 'Education saved.');
            $tab = 'education';
        } elseif ($action === 'education_delete') {
            $id = (int)$_POST['id'];
            $st = $db->prepare('DELETE FROM education WHERE id=?');
            $st->bind_param('i', $id);
            $st->execute();
            flash('success', 'Education deleted.');
            $tab = 'education';
        } elseif ($action === 'experience_save') {
            $id = (int)$_POST['id'];
            $job = trim($_POST['job_title']);
            $company = trim($_POST['company']);
            $location = trim($_POST['location']);
            $start = trim($_POST['start_date']);
            $end = trim($_POST['end_date']);
            $description = trim($_POST['description']);
            $sort = (int)$_POST['sort_order'];
            if ($id) {
                $st = $db->prepare('UPDATE experience SET job_title=?,company=?,location=?,start_date=?,end_date=?,description=?,sort_order=? WHERE id=?');
                $st->bind_param('ssssssii', $job, $company, $location, $start, $end, $description, $sort, $id);
            } else {
                $st = $db->prepare('INSERT INTO experience(job_title,company,location,start_date,end_date,description,sort_order) VALUES(?,?,?,?,?,?,?)');
                $st->bind_param('ssssssi', $job, $company, $location, $start, $end, $description, $sort);
            }
            $st->execute();
            flash('success', 'Experience saved.');
            $tab = 'experience';
        } elseif ($action === 'experience_delete') {
            $id = (int)$_POST['id'];
            $st = $db->prepare('DELETE FROM experience WHERE id=?');
            $st->bind_param('i', $id);
            $st->execute();
            flash('success', 'Experience deleted.');
            $tab = 'experience';
        } elseif ($action === 'project_save') {
            $id = (int)$_POST['id'];
            $cover = $id ? ($db->query("SELECT cover_image FROM projects WHERE id=$id")->fetch_assoc()['cover_image'] ?? '') : '';
            $pdf = $id ? ($db->query("SELECT pdf_file FROM projects WHERE id=$id")->fetch_assoc()['pdf_file'] ?? '') : '';
            if (!empty($_POST['remove_cover'])) {
                delete_file($cover);
                $cover = '';
            }
            if (!empty($_FILES['cover_image']['name'])) {
                $new = save_upload($_FILES['cover_image'], 'projects', ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], 5);
                delete_file($cover);
                $cover = $new;
            }
            if (!empty($_POST['remove_pdf'])) {
                delete_file($pdf);
                $pdf = '';
            }
            if (!empty($_FILES['pdf_file']['name'])) {
                $new = save_upload($_FILES['pdf_file'], 'projects', ['application/pdf'], 10);
                delete_file($pdf);
                $pdf = $new;
            }
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $category = trim($_POST['category']);
            $technologies = trim($_POST['technologies']);
            $github = clean_url($_POST['github_url'] ?? '');
            $live = clean_url($_POST['live_url'] ?? '');
            $start = trim($_POST['start_date']);
            $completion = trim($_POST['completion_date']);
            if ($id) {
                $st = $db->prepare('UPDATE projects SET title=?,description=?,category=?,technologies=?,github_url=?,live_url=?,start_date=?,completion_date=?,cover_image=?,pdf_file=? WHERE id=?');
                $st->bind_param('ssssssssssi', $title, $description, $category, $technologies, $github, $live, $start, $completion, $cover, $pdf, $id);
            } else {
                $st = $db->prepare('INSERT INTO projects(title,description,category,technologies,github_url,live_url,start_date,completion_date,cover_image,pdf_file) VALUES(?,?,?,?,?,?,?,?,?,?)');
                $st->bind_param('ssssssssss', $title, $description, $category, $technologies, $github, $live, $start, $completion, $cover, $pdf);
            }
            $st->execute();
            if (!$id) $id = $db->insert_id;
            if (!empty($_FILES['gallery']['name'][0])) {
                foreach ($_FILES['gallery']['name'] as $k => $n) {
                    if (!$n) continue;
                    $file = ['name' => $_FILES['gallery']['name'][$k], 'type' => $_FILES['gallery']['type'][$k], 'tmp_name' => $_FILES['gallery']['tmp_name'][$k], 'error' => $_FILES['gallery']['error'][$k], 'size' => $_FILES['gallery']['size'][$k]];
                    $path = save_upload($file, 'projects', ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], 5);
                    $st2 = $db->prepare('INSERT INTO project_images(project_id,image_path) VALUES(?,?)');
                    $st2->bind_param('is', $id, $path);
                    $st2->execute();
                }
            }
            flash('success', 'Project saved.');
            $tab = 'projects';
        } elseif ($action === 'project_delete') {
            $id = (int)$_POST['id'];
            $p = $db->query("SELECT cover_image,pdf_file FROM projects WHERE id=$id")->fetch_assoc();
            $imgs = $db->query("SELECT image_path FROM project_images WHERE project_id=$id");
            foreach ($imgs as $im) delete_file($im['image_path']);
            delete_file($p['cover_image'] ?? '');
            delete_file($p['pdf_file'] ?? '');
            $st = $db->prepare('DELETE FROM projects WHERE id=?');
            $st->bind_param('i', $id);
            $st->execute();
            flash('success', 'Project deleted.');
            $tab = 'projects';
        } elseif ($action === 'image_delete') {
            $id = (int)$_POST['id'];
            $im = $db->query("SELECT image_path FROM project_images WHERE id=$id")->fetch_assoc();
            delete_file($im['image_path'] ?? '');
            $st = $db->prepare('DELETE FROM project_images WHERE id=?');
            $st->bind_param('i', $id);
            $st->execute();
            flash('success', 'Project image deleted.');
            $tab = 'projects';
        } elseif ($action === 'resume_save') {
            $r = $db->query('SELECT file_path FROM resumes WHERE id=1')->fetch_assoc();
            $old = $r['file_path'] ?? '';
            if (!empty($_POST['remove_resume'])) {
                delete_file($old);
                $old = '';
            }
            if (!empty($_FILES['resume']['name'])) {
                $new = save_upload($_FILES['resume'], 'resume', ['application/pdf'], 10);
                delete_file($old);
                $old = $new;
            }
            $name = $_FILES['resume']['name'] ?? '';
            $st = $db->prepare('UPDATE resumes SET file_path=?,file_name=? WHERE id=1');
            $st->bind_param('ss', $old, $name);
            $st->execute();
            flash('success', 'Resume updated.');
            $tab = 'resume';
        } elseif ($action === 'password') {
            $email = trim($_POST['email']);
            $old = $_POST['old_password'];
            $new = $_POST['new_password'];
            $st = $db->prepare('SELECT password_hash FROM admins WHERE id=?');
            $st->bind_param('i', $_SESSION['admin_id']);
            $st->execute();
            $a = $st->get_result()->fetch_assoc();
            if (!$a || !password_verify($old, $a['password_hash'])) throw new RuntimeException('Current password is incorrect.');
            if (strlen($new) < 8) throw new RuntimeException('New password must be at least 8 characters.');
            if ($new !== ($_POST['confirm_password'] ?? '')) throw new RuntimeException('New passwords do not match.');
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $st = $db->prepare('UPDATE admins SET email=?,password_hash=? WHERE id=?');
            $st->bind_param('ssi', $email, $hash, $_SESSION['admin_id']);
            $st->execute();
            $_SESSION['admin_email'] = $email;
            flash('success', 'Admin settings updated.');
            $tab = 'settings';
        }
    } catch (Throwable $e) {
        flash('danger', $e->getMessage());
    }
    header('Location: index.php?tab=' . urlencode($tab));
    exit;
}
$personal = $db->query('SELECT * FROM personal WHERE id=1')->fetch_assoc();
$skills = $db->query('SELECT * FROM skills ORDER BY sort_order,id')->fetch_all(MYSQLI_ASSOC);
$education = $db->query('SELECT * FROM education ORDER BY sort_order,id DESC')->fetch_all(MYSQLI_ASSOC);
$experience = $db->query('SELECT * FROM experience ORDER BY sort_order,id DESC')->fetch_all(MYSQLI_ASSOC);
$projects = $db->query('SELECT * FROM projects ORDER BY id DESC')->fetch_all(MYSQLI_ASSOC);
$resume = $db->query('SELECT * FROM resumes WHERE id=1')->fetch_assoc();
$projectImages = $db->query('SELECT * FROM project_images ORDER BY id')->fetch_all(MYSQLI_ASSOC);
$imgByProject = [];
foreach ($projectImages as $im) $imgByProject[$im['project_id']][] = $im;
$editProject = null;
if (isset($_GET['edit_project'])) {
    $id = (int)$_GET['edit_project'];
    $st = $db->prepare('SELECT * FROM projects WHERE id=?');
    $st->bind_param('i', $id);
    $st->execute();
    $editProject = $st->get_result()->fetch_assoc();
}
$editSkill = null;
if (isset($_GET['edit_skill'])) {
    $id = (int)$_GET['edit_skill'];
    $st = $db->prepare('SELECT * FROM skills WHERE id=?');
    $st->bind_param('i', $id);
    $st->execute();
    $editSkill = $st->get_result()->fetch_assoc();
}
$editEducation = null;
if (isset($_GET['edit_education'])) {
    $id = (int)$_GET['edit_education'];
    $st = $db->prepare('SELECT * FROM education WHERE id=?');
    $st->bind_param('i', $id);
    $st->execute();
    $editEducation = $st->get_result()->fetch_assoc();
}
$editExperience = null;
if (isset($_GET['edit_experience'])) {
    $id = (int)$_GET['edit_experience'];
    $st = $db->prepare('SELECT * FROM experience WHERE id=?');
    $st->bind_param('i', $id);
    $st->execute();
    $editExperience = $st->get_result()->fetch_assoc();
}
$page_title = 'Admin Dashboard'; ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= e($page_title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="admin-page">
    <nav class="navbar navbar-dark bg-dark">
        <div class="container"><a class="navbar-brand fw-bold" href="../index.php"><i class="bi bi-arrow-left"></i> Portfolio</a>
            <div class="d-flex align-items-center gap-3"><span class="text-white-50 d-none d-md-inline"><?= e($_SESSION['admin_email']) ?></span><a class="btn btn-outline-light btn-sm" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></div>
        </div>
    </nav>
    <div class="container py-4 py-lg-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div><span class="text-primary fw-semibold">PORTFOLIO CMS</span>
                <h1 class="fw-bold mb-1">Manage Website</h1>
                <p class="text-secondary mb-0">Update your portfolio without editing source code.</p>
            </div><a href="../index.php" target="_blank" class="btn btn-outline-dark"><i class="bi bi-eye"></i> View Public Site</a>
        </div><?php show_flash(); ?><div class="admin-dashboard row g-4">
            <aside class="col-lg-3">
                <div class="admin-card p-3 sticky-lg-top" style="top:20px">
                    <div class="nav flex-column nav-pills gap-1"><?php foreach (['dashboard' => 'Dashboard', 'personal' => 'Personal Information', 'education' => 'Education', 'skills' => 'Skills', 'experience' => 'Work Experience', 'projects' => 'Projects', 'resume' => 'Resume & Files', 'settings' => 'Admin Settings'] as $k => $v): ?><a class="nav-link <?= $tab === $k ? 'active' : '' ?>" href="?tab=<?= $k ?>"><i class="bi <?= match ($k) {
                                                                                                                                                                                                                                                                                                                                                                                                            'dashboard' => 'bi-speedometer2',
                                                                                                                                                                                                                                                                                                                                                                                                            'personal' => 'bi-person',
                                                                                                                                                                                                                                                                                                                                                                                                            'education' => 'bi-mortarboard',
                                                                                                                                                                                                                                                                                                                                                                                                            'skills' => 'bi-tools',
                                                                                                                                                                                                                                                                                                                                                                                                            'experience' => 'bi-briefcase',
                                                                                                                                                                                                                                                                                                                                                                                                            'projects' => 'bi-folder2-open',
                                                                                                                                                                                                                                                                                                                                                                                                            'resume' => 'bi-file-earmark-pdf',
                                                                                                                                                                                                                                                                                                                                                                                                            'settings' => 'bi-shield-lock'
                                                                                                                                                                                                                                                                                                                                                                                                        } ?> me-2"></i><?= e($v) ?></a><?php endforeach; ?></div>
                </div>
            </aside>
            <section class="col-lg-9">
                <?php if ($tab === 'dashboard'): ?><div class="row g-3">
                        <div class="col-md-4">
                            <div class="admin-stat"><i class="bi bi-folder2-open"></i><strong><?= count($projects) ?></strong><span>Projects</span></div>
                        </div>
                        <div class="col-md-4">
                            <div class="admin-stat"><i class="bi bi-tools"></i><strong><?= count($skills) ?></strong><span>Skills</span></div>
                        </div>
                        <div class="col-md-4">
                            <div class="admin-stat"><i class="bi bi-mortarboard"></i><strong><?= count($education) ?></strong><span>Education</span></div>
                        </div>
                    </div>
                    <div class="admin-card mt-4 p-4">
                        <h4 class="fw-bold">Quick Start</h4>
                        <p class="text-secondary">Use the menu to update your portfolio. Changes are stored in MySQL and immediately appear on the public website.</p>
                        <div class="d-flex flex-wrap gap-2"><a class="btn btn-primary" href="?tab=projects"><i class="bi bi-plus-lg"></i> Add Project</a><a class="btn btn-outline-dark" href="?tab=personal"><i class="bi bi-person"></i> Edit Profile</a><a class="btn btn-outline-dark" href="?tab=resume"><i class="bi bi-file-earmark-arrow-up"></i> Update Resume</a></div>
                    </div>
                <?php elseif ($tab === 'personal'): ?><div class="admin-card">
                        <div class="card-header">
                            <h5>Personal Information</h5><span>All public profile information is managed here.</span>
                        </div>
                        <form class="p-4" method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="personal">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label">Full Name</label><input name="name" class="form-control" required value="<?= e($personal['name']) ?>"></div>
                                <div class="col-md-6"><label class="form-label">Professional Title</label><input name="role" class="form-control" value="<?= e($personal['role']) ?>"></div>
                                <div class="col-12"><label class="form-label">Tagline</label><input name="tagline" class="form-control" value="<?= e($personal['tagline']) ?>"></div>
                                <div class="col-12"><label class="form-label">About Me</label><textarea name="about" rows="6" class="form-control"><?= e($personal['about']) ?></textarea></div>
                                <div class="col-md-6"><label class="form-label">Email</label><input name="email" type="email" class="form-control" value="<?= e($personal['email']) ?>"></div>
                                <div class="col-md-6"><label class="form-label">Phone</label><input name="phone" class="form-control" value="<?= e($personal['phone']) ?>"></div>
                                <div class="col-md-6"><label class="form-label">Location</label><input name="location" class="form-control" value="<?= e($personal['location']) ?>"></div>
                                <div class="col-md-6"><label class="form-label">Education Summary</label><input name="education_summary" class="form-control" value="<?= e($personal['education_summary']) ?>"></div>
                                <div class="col-md-6"><label class="form-label">Languages</label><input name="languages" class="form-control" placeholder="English, Hindi, Gujarati" value="<?= e($personal['languages']) ?>"></div>
                                <div class="col-md-6"><label class="form-label">Profile Photo</label><input name="profile_photo" type="file" class="form-control" accept="image/*"></div>
                                <div class="col-12"><?php if ($personal['profile_photo']): ?><img class="preview-photo me-2" src="../<?= e($personal['profile_photo']) ?>" alt=""><label class="form-check-label"><input class="form-check-input" type="checkbox" name="remove_photo" value="1"> Remove current photo</label><?php endif; ?></div>
                                <div class="col-md-6"><label class="form-label">GitHub</label><input name="github" class="form-control" value="<?= e($personal['github']) ?>"></div>
                                <div class="col-md-6"><label class="form-label">LinkedIn</label><input name="linkedin" class="form-control" value="<?= e($personal['linkedin']) ?>"></div>
                                <div class="col-md-6"><label class="form-label">Instagram</label><input name="instagram" class="form-control" value="<?= e($personal['instagram']) ?>"></div>
                                <div class="col-md-6"><label class="form-label">Facebook</label><input name="facebook" class="form-control" value="<?= e($personal['facebook']) ?>"></div>
                                <div class="col-md-6"><label class="form-label">X / Twitter</label><input name="twitter" class="form-control" value="<?= e($personal['twitter']) ?>"></div>
                                <div class="col-md-6"><label class="form-label">Canva Resume URL</label><input name="canva_url" class="form-control" value="<?= e($personal['canva_url']) ?>"></div>
                                <div class="col-12"><button class="btn btn-primary"><i class="bi bi-save"></i> Save Personal Details</button></div>
                            </div>
                        </form>
                    </div>
                <?php elseif ($tab === 'skills'): ?><div class="admin-card">
                        <div class="card-header">
                            <h5>Skills</h5><span>Add, edit and delete skills.</span>
                        </div>
                        <div class="p-4">
                            <form method="post" class="row g-2 mb-4"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="skill_save"><input type="hidden" name="id" value="<?= e($editSkill['id'] ?? 0) ?>">
                                <div class="col-md-5"><input name="name" class="form-control" placeholder="Skill" required value="<?= e($editSkill['name'] ?? '') ?>"></div>
                                <div class="col-md-4"><input name="category" class="form-control" placeholder="Category" value="<?= e($editSkill['category'] ?? '') ?>"></div>
                                <div class="col-md-2"><input name="sort_order" type="number" class="form-control" placeholder="Order" value="<?= e($editSkill['sort_order'] ?? 0) ?>"></div>
                                <div class="col-md-1"><button class="btn btn-primary w-100"><i class="bi bi-save"></i></button></div>
                            </form>
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Skill</th>
                                            <th>Category</th>
                                            <th>Order</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody><?php foreach ($skills as $s): ?><tr>
                                                <td><?= e($s['name']) ?></td>
                                                <td><?= e($s['category']) ?></td>
                                                <td><?= e($s['sort_order']) ?></td>
                                                <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="?tab=skills&edit_skill=<?= $s['id'] ?>"><i class="bi bi-pencil"></i></a>
                                                    <form class="d-inline" method="post" onsubmit="return confirm('Delete this skill?')"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="skill_delete"><input type="hidden" name="id" value="<?= $s['id'] ?>"><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
                                                </td>
                                            </tr><?php endforeach; ?></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php elseif ($tab === 'education'): ?><div class="admin-card">
                        <div class="card-header">
                            <h5>Education</h5><span>Manage degrees, institutions and academic details.</span>
                        </div>
                        <div class="p-4">
                            <form method="post" class="row g-3 mb-5"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="education_save"><input type="hidden" name="id" value="<?= e($editEducation['id'] ?? 0) ?>">
                                <div class="col-md-6"><label class="form-label">Degree / Course</label><input name="degree" class="form-control" required value="<?= e($editEducation['degree'] ?? '') ?>"></div>
                                <div class="col-md-6"><label class="form-label">Institution</label><input name="institution" class="form-control" required value="<?= e($editEducation['institution'] ?? '') ?>"></div>
                                <div class="col-md-4"><input name="location" class="form-control" placeholder="Location" value="<?= e($editEducation['location'] ?? '') ?>"></div>
                                <div class="col-md-2"><input name="start_date" class="form-control" placeholder="Start" value="<?= e($editEducation['start_date'] ?? '') ?>"></div>
                                <div class="col-md-2"><input name="end_date" class="form-control" placeholder="End" value="<?= e($editEducation['end_date'] ?? '') ?>"></div>
                                <div class="col-md-2"><input name="grade" class="form-control" placeholder="Grade/CGPA" value="<?= e($editEducation['grade'] ?? '') ?>"></div>
                                <div class="col-md-2"><input name="sort_order" type="number" class="form-control" placeholder="Order" value="<?= e($editEducation['sort_order'] ?? 0) ?>"></div>
                                <div class="col-12"><textarea name="description" rows="3" class="form-control" placeholder="Description"><?= e($editEducation['description'] ?? '') ?></textarea></div>
                                <div class="col-12"><button class="btn btn-primary"><i class="bi bi-save"></i> Save Education</button><?php if ($editEducation): ?><a href="?tab=education" class="btn btn-outline-secondary ms-2">Cancel</a><?php endif; ?></div>
                            </form><?php foreach ($education as $ed): ?><div class="list-row">
                                    <div><strong><?= e($ed['degree']) ?></strong>
                                        <div class="small text-secondary"><?= e($ed['institution']) ?> · <?= e(trim($ed['start_date'] . ' - ' . $ed['end_date'], ' -')) ?></div>
                                    </div>
                                    <div><a class="btn btn-sm btn-outline-primary" href="?tab=education&edit_education=<?= $ed['id'] ?>">Edit</a>
                                        <form class="d-inline" method="post" onsubmit="return confirm('Delete education?')"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="education_delete"><input type="hidden" name="id" value="<?= $ed['id'] ?>"><button class="btn btn-sm btn-outline-danger">Delete</button></form>
                                    </div>
                                </div><?php endforeach; ?>
                        </div>
                    </div>
                <?php elseif ($tab === 'experience'): ?><div class="admin-card">
                        <div class="card-header">
                            <h5>Work Experience</h5><span>Manage your employment or internship history.</span>
                        </div>
                        <div class="p-4">
                            <form method="post" class="row g-3 mb-5"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="experience_save"><input type="hidden" name="id" value="<?= e($editExperience['id'] ?? 0) ?>">
                                <div class="col-md-6"><input name="job_title" class="form-control" placeholder="Job title" required value="<?= e($editExperience['job_title'] ?? '') ?>"></div>
                                <div class="col-md-6"><input name="company" class="form-control" placeholder="Company" required value="<?= e($editExperience['company'] ?? '') ?>"></div>
                                <div class="col-md-4"><input name="location" class="form-control" placeholder="Location" value="<?= e($editExperience['location'] ?? '') ?>"></div>
                                <div class="col-md-2"><input name="start_date" class="form-control" placeholder="Start" value="<?= e($editExperience['start_date'] ?? '') ?>"></div>
                                <div class="col-md-2"><input name="end_date" class="form-control" placeholder="End / Present" value="<?= e($editExperience['end_date'] ?? '') ?>"></div>
                                <div class="col-md-2"><input name="sort_order" type="number" class="form-control" placeholder="Order" value="<?= e($editExperience['sort_order'] ?? 0) ?>"></div>
                                <div class="col-12"><textarea name="description" rows="4" class="form-control" placeholder="Responsibilities and achievements"><?= e($editExperience['description'] ?? '') ?></textarea></div>
                                <div class="col-12"><button class="btn btn-primary">Save Experience</button></div>
                            </form><?php foreach ($experience as $ex): ?><div class="list-row">
                                    <div><strong><?= e($ex['job_title']) ?></strong>
                                        <div class="small text-secondary"><?= e($ex['company']) ?> · <?= e(trim($ex['start_date'] . ' - ' . $ex['end_date'], ' -')) ?></div>
                                    </div>
                                    <div><a class="btn btn-sm btn-outline-primary" href="?tab=experience&edit_experience=<?= $ex['id'] ?>">Edit</a>
                                        <form class="d-inline" method="post" onsubmit="return confirm('Delete experience?')"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="experience_delete"><input type="hidden" name="id" value="<?= $ex['id'] ?>"><button class="btn btn-sm btn-outline-danger">Delete</button></form>
                                    </div>
                                </div><?php endforeach; ?>
                        </div>
                    </div>
                <?php elseif ($tab === 'projects'): ?><div class="admin-card">
                        <div class="card-header">
                            <h5>Project Management</h5><span>Add complete project details, screenshots and PDF documents.</span>
                        </div>
                        <div class="p-4">
                            <form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="project_save"><input type="hidden" name="id" value="<?= e($editProject['id'] ?? 0) ?>">
                                <div class="row g-3">
                                    <div class="col-md-6"><label class="form-label">Project Title</label><input name="title" class="form-control" required value="<?= e($editProject['title'] ?? '') ?>"></div>
                                    <div class="col-md-6"><label class="form-label">Category</label><input name="category" class="form-control" placeholder="Web / Java / PHP / College Project" value="<?= e($editProject['category'] ?? '') ?>"></div>
                                    <div class="col-12"><label class="form-label">Description</label><textarea name="description" rows="5" class="form-control" required><?= e($editProject['description'] ?? '') ?></textarea></div>
                                    <div class="col-12"><label class="form-label">Technologies / Languages</label><input name="technologies" class="form-control" placeholder="Java, JSP, Servlet, MySQL" value="<?= e($editProject['technologies'] ?? '') ?>"></div>
                                    <div class="col-md-6"><label class="form-label">GitHub URL</label><input name="github_url" class="form-control" value="<?= e($editProject['github_url'] ?? '') ?>"></div>
                                    <div class="col-md-6"><label class="form-label">Live Project URL</label><input name="live_url" class="form-control" value="<?= e($editProject['live_url'] ?? '') ?>"></div>
                                    <div class="col-md-6"><label class="form-label">Start Date</label><input name="start_date" class="form-control" placeholder="Jan 2026" value="<?= e($editProject['start_date'] ?? '') ?>"></div>
                                    <div class="col-md-6"><label class="form-label">Completion Date</label><input name="completion_date" class="form-control" placeholder="Mar 2026" value="<?= e($editProject['completion_date'] ?? '') ?>"></div>
                                    <div class="col-md-6"><label class="form-label">Cover Image</label><input name="cover_image" type="file" class="form-control" accept="image/*"><small class="text-secondary">JPG, PNG, WEBP, GIF · max 5 MB</small><?php if (!empty($editProject['cover_image'])): ?><div class="mt-2"><img class="admin-project-img" src="../<?= e($editProject['cover_image']) ?>" alt=""><label class="small"><input type="checkbox" name="remove_cover" value="1"> Remove current image</label></div><?php endif; ?></div>
                                    <div class="col-md-6"><label class="form-label">Project PDF / Document</label><input name="pdf_file" type="file" class="form-control" accept="application/pdf"><small class="text-secondary">PDF only · max 10 MB</small><?php if (!empty($editProject['pdf_file'])): ?><div class="mt-2"><a target="_blank" class="btn btn-sm btn-outline-danger" href="../<?= e($editProject['pdf_file']) ?>">View current PDF</a><label class="small ms-2"><input type="checkbox" name="remove_pdf" value="1"> Remove</label></div><?php endif; ?></div>
                                    <div class="col-12"><label class="form-label">Additional Screenshots (select multiple)</label><input name="gallery[]" type="file" class="form-control" accept="image/*" multiple><small class="text-secondary">You can upload multiple images at once.</small><?php if ($editProject && !empty($imgByProject[$editProject['id']])): ?><div class="row g-2 mt-2"><?php foreach ($imgByProject[$editProject['id']] as $im): ?><div class="col-6 col-md-3"><img class="admin-project-img" src="../<?= e($im['image_path']) ?>" alt="">
                                                        <form method="post" class="mt-1" onsubmit="return confirm('Delete image?')"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="image_delete"><input type="hidden" name="id" value="<?= $im['id'] ?>"><button class="btn btn-sm btn-outline-danger w-100">Delete Image</button></form>
                                                    </div><?php endforeach; ?></div><?php endif; ?></div>
                                    <div class="col-12"><button class="btn btn-primary"><i class="bi bi-save"></i> <?= $editProject ? 'Update Project' : 'Add Project' ?></button><?php if ($editProject): ?><a href="?tab=projects" class="btn btn-outline-secondary ms-2">Cancel</a><?php endif; ?></div>
                                </div>
                            </form>
                            <hr class="my-5">
                            <div class="row g-4"><?php foreach ($projects as $p): ?><div class="col-md-6">
                                        <div class="info-card h-100 p-3">
                                            <div class="d-flex gap-3"><img class="admin-project-img" style="width:120px;flex:none" src="../<?= e($p['cover_image'] ?: 'https://placehold.co/240x150/eaf2ff/0d6efd?text=Project') ?>" alt="">
                                                <div>
                                                    <h5 class="fw-bold mb-1"><?= e($p['title']) ?></h5>
                                                    <p class="small text-secondary mb-2"><?= e($p['category']) ?></p><a class="btn btn-sm btn-outline-primary" href="?tab=projects&edit_project=<?= $p['id'] ?>">Edit</a>
                                                    <form class="d-inline" method="post" onsubmit="return confirm('Delete this project and all its files?')"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="project_delete"><input type="hidden" name="id" value="<?= $p['id'] ?>"><button class="btn btn-sm btn-outline-danger">Delete</button></form>
                                                </div>
                                            </div>
                                        </div>
                                    </div><?php endforeach; ?></div>
                        </div>
                    </div>
                <?php elseif ($tab === 'resume'): ?><div class="admin-card">
                        <div class="card-header">
                            <h5>Resume & Files</h5><span>Upload, replace, view or remove your current resume.</span>
                        </div>
                        <div class="p-4">
                            <form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="resume_save"><label class="form-label">Current / New Resume (PDF)</label><input name="resume" type="file" class="form-control mb-2" accept="application/pdf"><small class="text-secondary">PDF only · max 10 MB</small><?php if ($resume['file_path']): ?><div class="file-box mt-3 d-flex flex-wrap align-items-center justify-content-between gap-2"><span><i class="bi bi-file-earmark-pdf text-danger"></i> <?= e($resume['file_name'] ?: basename($resume['file_path'])) ?></span>
                                        <div><a target="_blank" class="btn btn-sm btn-outline-primary" href="../<?= e($resume['file_path']) ?>">View</a><a download class="btn btn-sm btn-outline-secondary" href="../<?= e($resume['file_path']) ?>">Download</a><label class="ms-2 small"><input type="checkbox" name="remove_resume" value="1"> Remove</label></div>
                                    </div><?php endif; ?><button class="btn btn-primary mt-3"><i class="bi bi-cloud-arrow-up"></i> Save Resume</button></form>
                            <hr class="my-4">
                            <h5 class="fw-bold">Canva</h5>
                            <p class="text-secondary">Set your Canva design/profile URL under Personal Information. It appears on the public Resume section as “Create on Canva”.</p>
                        </div>
                    </div>
                <?php elseif ($tab === 'settings'): ?><div class="admin-card">
                        <div class="card-header">
                            <h5>Admin Settings</h5><span>Change your admin email and password.</span>
                        </div>
                        <form method="post" class="p-4"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="password">
                            <div class="row g-3">
                                <div class="col-12"><label class="form-label">Admin Email</label><input name="email" type="email" class="form-control" required value="<?= e($_SESSION['admin_email']) ?>"></div>
                                <div class="col-md-4"><label class="form-label">Current Password</label><input name="old_password" type="password" class="form-control" required></div>
                                <div class="col-md-4"><label class="form-label">New Password</label><input name="new_password" id="new_password" type="password" class="form-control" minlength="8" required></div>
                                <div class="col-md-4"><label class="form-label">Confirm New Password</label><input name="confirm_password" id="confirmPassword" type="password" class="form-control" minlength="8" required></div>
                                <div class="col-12"><button class="btn btn-primary" onclick="if(document.getElementById('new_password')?.value!==document.getElementById('confirmPassword').value){event.preventDefault();alert('Passwords do not match.')}">Update Login</button></div>
                            </div>
                        </form>
                    </div><?php endif; ?>
            </section>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>